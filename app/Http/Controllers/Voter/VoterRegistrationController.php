<?php

namespace App\Http\Controllers\Voter;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Models\Voter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class VoterRegistrationController extends Controller
{
    /**
     * Get the throttle key for the given request.
     */
    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->input('email')));
    }

    /**
     * Step 2: Show registration/login form
     */
    public function index(Election $election)
    {
        $election->load('organization');

        if (!$election) {
            return redirect()->route('voter.elections.access')
                ->withErrors(['code' => 'Election not found.']);
        }

        // Always show login form as registration is removed
        $isSignInMode = true;

        return view('voter.registration.index', [
            'election' => $election,
            'isSignInMode' => $isSignInMode,
            'registrationOver' => false // Not relevant anymore if we only login
        ]);
    }

    /**
     * Step 2: Register new voter
     */
    public function store(Request $request, Election $election)
    {
        // Registration deadline check
        if ($election->registration_deadline && now()->gt($election->registration_deadline)) {
            return back()->withErrors(['registration' => 'Registration is over.'])->withInput();
        }

        // Accepted domains validation
        if ($election->accepted_domains) {
            $domains = array_map('trim', explode(',', $election->accepted_domains));
            $email = $request->email;
            $isValidDomain = false;
            foreach ($domains as $domain) {
                if (str_ends_with($email, $domain)) {
                    $isValidDomain = true;
                    break;
                }
            }
            if (!$isValidDomain) {
                return back()->withErrors(['email' => 'Only emails from the following domains are allowed: ' . $election->accepted_domains])->withInput();
            }
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'student_id' => [
                'required',
                'string',
                'regex:/^[A-Z0-9-]+$/i', // allow letters, numbers, dashes
                function ($attribute, $value, $fail) {
                    // Count letters/numbers only, ignore dashes
                    $count = preg_replace('/[^A-Z0-9]/i', '', $value);
                    if (strlen($count) > 12) {
                        $fail('The student ID must not be greater than 12 characters (excluding dashes).');
                    }
                },
            ],

            'password' => 'required|string|min:6|confirmed',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'id_photo' => $election->require_id_verification ? 'required|image|max:5120' : 'nullable',
        ], [
            'student_id.regex' => 'The ID format is invalid. Use numbers and dashes only.',
            'id_photo.required' => 'A photo of your ID is required for verification.',
        ]);

        // Check if already registered for THIS election
        $existingVoter = Voter::where('election_id', $election->id)
            ->where(function($q) use ($request) {
                $q->where('email', $request->email)
                  ->orWhere('student_id', $request->student_id);
            })
            ->exists();

        if ($existingVoter) {
            return back()->withErrors(['registration' => 'You are already registered for this election with this email or ID.'])->withInput();
        }

        if ($election->require_geo_registration) {
            if (!$request->latitude || !$request->longitude) {
                return back()->withErrors(['registration' => 'Location access is required to register for this election. Please enable GPS.'])->withInput();
            }

            $distance = $this->calculateDistance(
                $election->geo_latitude,
                $election->geo_longitude,
                $request->latitude,
                $request->longitude
            );

            if ($distance > ($election->geo_radius_meters + 10)) { // Add 10m buffer for GPS accuracy
                return back()->withErrors(['registration' => 'You must be within the designated voting area to register. (Distance: ' . round($distance) . 'm, Allowed: ' . $election->geo_radius_meters . 'm)'])->withInput();
            }
        }

        // Split full name into first/middle/last like candidate creation logic
        $userNameForCandidate = $request->name;
        $nameParts = preg_split('/\s+/', trim($userNameForCandidate));
        if (count($nameParts) === 1) {
            $firstName = $nameParts[0];
            $middleName = null;
            $lastName = $nameParts[0];
        } elseif (count($nameParts) === 2) {
            $firstName = $nameParts[0];
            $middleName = null;
            $lastName = $nameParts[1];
        } else {
            $firstName = array_shift($nameParts);
            $lastName = array_pop($nameParts);
            $middleName = implode(' ', $nameParts);
        }

        $email = trim(strtolower($request->email));

        DB::beginTransaction();
        try {
            $idPhotoPath = null;
            $idPhotoHash = null;

            if ($request->hasFile('id_photo')) {
                $file = $request->file('id_photo');
                $idPhotoPath = $file->store('id_photos', 'public');

                // Perceptual Hashing
                $fullPath = storage_path('app/public/' . $idPhotoPath);
                $idPhotoHash = \App\Helpers\ImageHash::dhash($fullPath);

                // Check for potential duplicate ID photos in this election
                if ($idPhotoHash) {
                    $existingHashes = Voter::where('election_id', $election->id)
                        ->whereNotNull('id_photo_hash')
                        ->pluck('id_photo_hash', 'id');

                    foreach ($existingHashes as $id => $existingHash) {
                        $distance = \App\Helpers\ImageHash::distance($idPhotoHash, $existingHash);
                        if ($distance <= 5) { // Threshold for similarity
                            \Illuminate\Support\Facades\Log::warning("Potential duplicate ID photo detected for voter registration in election {$election->id}. New voter: {$request->email}, Matching voter ID: {$id}, Hamming Distance: {$distance}");
                        }
                    }
                }
            }

            $voter = Voter::create([
                'election_id' => $election->id,
                'name' => $request->name,
                'first_name' => $firstName,
                'middle_name' => $middleName,
                'last_name' => $lastName,
                'email' => $email,
                'student_id' => $request->student_id ?? null,
                'phone' => $request->phone ?? null,
                'password' => Hash::make($request->password),
                'id_photo' => $idPhotoPath,
                'id_photo_hash' => $idPhotoHash,
                'registration_status' => ($election->auto_approve_voters && !$election->require_id_verification) ? 'approved' : 'pending',
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['registration' => 'Error during registration: ' . $e->getMessage()])->withInput();
        }

        $msg = $election->auto_approve_voters
            ? 'Registration successful! You can now Sign In.'
            : 'Registration submitted! Please wait 1 to 24 hours for admin approval of your registration before you can Sign In.';

        return redirect()->route('voter.registration.index', $election->code)
            ->with('success', $msg)
            ->with('switch_to_login', true)
            ->with('registered_email', $request->email);
    }

    /**
     * Step 2: Login existing voter
     */
    public function login(Request $request, Election $election)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $throttleKey = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $msg = "Too many login attempts. Please try again in $seconds seconds.";
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 429);
            }
            return back()->withErrors(['login' => $msg])->withInput();
        }

        if ($election->require_geo_registration) {
            if (!$request->latitude || !$request->longitude) {
                return back()->withErrors(['login' => 'Location access is required to sign in for this election. Please enable GPS.'])->withInput();
            }

            $distance = $this->calculateDistance(
                $election->geo_latitude,
                $election->geo_longitude,
                $request->latitude,
                $request->longitude
            );

            if ($distance > ($election->geo_radius_meters + 1000)) { // Increased buffer to 1000m for better reliability
                $msg = 'You must be within the designated voting area to sign in. (Distance: ' . round($distance) . 'm, Allowed: ' . ($election->geo_radius_meters + 1000) . 'm)';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 403);
                }
                return back()->withErrors(['login' => $msg])->withInput();
            }
        }

        $email = trim(strtolower($request->email));
        $voter = Voter::where('email', $email)
            ->where('election_id', $election->id)
            ->first();

        if (!$voter) {
            RateLimiter::hit($throttleKey);
            $msg = 'Invalid email or password.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 401);
            }
            return back()->withErrors(['login' => $msg])->withInput();
        }

        // Check if permanently blocked
        if ($voter->is_permanently_blocked) {
            $msg = 'Your account has been permanently blocked. Please contact the Administrator.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }
            return back()->withErrors(['login' => $msg])->withInput();
        }

        // Check if currently locked
        if ($voter->locked_until && $voter->locked_until->isFuture()) {
            $diff = $voter->locked_until->diffForHumans();
            $msg = "Your account is temporarily locked. Please try again in $diff.";
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }
            return back()->withErrors(['login' => $msg]);
        }

        if (!Hash::check($request->password, $voter->password)) {
            RateLimiter::hit($throttleKey);
            $voter->increment('failed_login_attempts');
            $attempts = $voter->failed_login_attempts;
            $maxAttempts = 6;

            $msg = "Invalid email or password. Attempt $attempts of $maxAttempts.";

            if ($attempts < 3) {
                $msg .= " You will be locked out for 60 minutes after 3 failed attempts.";
            } elseif ($attempts == 4) {
                $msg .= " You will be locked out for 24 hours after 5 failed attempts.";
            }

            if ($attempts >= $maxAttempts) {
                $voter->update(['is_permanently_blocked' => true]);
                $msg = 'Your account has been permanently blocked due to too many failed attempts.';
            } elseif ($attempts == 5) {
                $voter->update(['locked_until' => now()->addHours(24)]);
                $msg = 'Too many failed attempts. Your account has been locked for 24 hours.';
            } elseif ($attempts == 3) {
                $voter->update(['locked_until' => now()->addMinutes(60)]);
                $msg = 'Too many failed attempts. Your account has been locked for 60 minutes.';
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $msg,
                    'attempts' => $attempts,
                    'max_attempts' => $maxAttempts
                ], 401);
            }

            return back()->withErrors(['login' => $msg]);
        }

        // Reset failed attempts on successful login
        RateLimiter::clear($throttleKey);
        $voter->update([
            'failed_login_attempts' => 0,
            'locked_until' => null
        ]);

        if ($voter->registration_status === 'pending' && !$election->auto_approve_voters) {
            $msg = 'Your registration is still pending approval. Please wait 1 to 24 hours.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }
            return back()->withErrors(['login' => $msg]);
        }

        if ($voter->registration_status === 'declined') {
            $msg = 'Your registration has been declined. You cannot sign in.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }
            return back()->withErrors(['login' => $msg]);
        }

        // 🔐 Send OTP (Email)
        try {
            Http::timeout(10)->withHeaders([
                'Authorization' => 'Bearer ' . config('services.supabase.service_key'),
                'apikey' => config('services.supabase.service_key'),
                'Content-Type' => 'application/json',
            ])->post(
                    config('services.supabase.url') . '/auth/v1/otp',
                    [
                        'email' => $voter->email,
                        'type' => 'email',
                    ]
                );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Supabase Voter OTP connection error: ' . $e->getMessage());
            $msg = 'Failed to connect to the authentication service. Please check your internet connection and try again.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 503);
            }
            return back()->withErrors(['login' => $msg])->withInput();
        }

        // Store OTP info in session for 2FA
        session([
            'otp_email' => $voter->email,
            'otp_voter_id' => $voter->id,
            'otp_election_id' => $election->id,
            'remember_me' => $request->has('remember'),
        ]);


        // Redirect to OTP form instead of welcome
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'A verification code has been sent to your email.',
                'redirect' => route('voter.otp.form')
            ]);
        }

        return redirect()->route('voter.otp.form')
            ->with('success', 'A verification code has been sent to your email.');
    }

    /**
     * Handle voter logout
     */
    public function logout(Request $request)
    {
        Auth::guard('voter')->logout();
        $request->session()->forget('voter');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('voter.elections.access')
            ->with('success', 'You have been logged out.');
    }

    /**
     * Check if voter registration is allowed
     */
    private function validateRegistrationWindow(Election $election)
    {
        $now = now();

        // Election already finished
        if ($election->end_at && $now->gt($election->end_at)) {
            abort(403, 'This election has already ended. Registration is closed.');
        }

        // Voting already started
        if ($election->start_at && $now->gte($election->start_at)) {
            abort(403, 'Registration is closed because voting has already started.');
        }

        // Explicit closed status
        if ($election->status === 'closed') {
            abort(403, 'This election is already closed.');
        }
    }


    /**
     * Calculate distance between two points in meters using Haversine formula
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meters

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
