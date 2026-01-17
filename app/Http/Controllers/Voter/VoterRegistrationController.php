<?php

namespace App\Http\Controllers\Voter;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Models\Voter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;


class VoterRegistrationController extends Controller
{
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

        // Registration deadline check
        try {
            $this->validateRegistrationWindow($election);
            $registrationOver = false;
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $registrationOver = true;
        }

        return view('voter.registration.index', [
            'election' => $election,
            'registrationOver' => $registrationOver
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
            'student_id' => 'required|string|max:12|regex:/^[0-9-]+$/',
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

        DB::beginTransaction();
        try {
            // Find or create global User account
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $request->name,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'middle_name' => $middleName,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role' => 'voter',
                    'is_active' => true,
                    'phone' => $request->phone,
                    'student_id' => $request->student_id,
                ]);
            } else {
                // User already exists. Reset failed attempts on successful registration for new election.
                $user->update([
                    'failed_login_attempts' => 0,
                    'locked_until' => null
                ]);
            }

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
                            // We can log this or flag the voter
                            \Illuminate\Support\Facades\Log::warning("Potential duplicate ID photo detected for voter registration in election {$election->id}. New voter: {$request->email}, Matching voter ID: {$id}, Hamming Distance: {$distance}");
                            // For now we just flag it in the database if we had a flag column,
                            // but we can also just let the admin see it during review.
                        }
                    }
                }
            }

            $voter = Voter::create([
                'election_id' => $election->id,
                'user_id' => $user->id,
                'name' => $request->name,
                'first_name' => $firstName,
                'middle_name' => $middleName,
                'last_name' => $lastName,
                'email' => $request->email,
                'student_id' => $request->student_id ?? null,
                'phone' => $request->phone ?? null,
                'password' => $user->password, // Sync password
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

        return redirect()->route('voter.registration.index', $election->id)
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
                return back()->withErrors(['login' => 'You must be within the designated voting area to sign in. (Distance: ' . round($distance) . 'm, Allowed: ' . ($election->geo_radius_meters + 1000) . 'm)'])->withInput();
            }
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['login' => 'Invalid email or password.']);
        }

        // Check if permanently blocked
        if ($user->is_permanently_blocked) {
            return back()->withErrors(['login' => 'Your account has been permanently blocked. Please contact the Administrator.']);
        }

        // Check if currently locked
        if ($user->locked_until && $user->locked_until->isFuture()) {
            $diff = $user->locked_until->diffForHumans();
            return back()->withErrors(['login' => "Your account is temporarily locked. Please try again in $diff."]);
        }

        if (!Hash::check($request->password, $user->password)) {
            $user->increment('failed_login_attempts');
            $attempts = $user->failed_login_attempts;
            $msg = 'Invalid email or password.';

            if ($attempts >= 6) {
                $user->update(['is_permanently_blocked' => true]);
                $msg = 'Your account has been permanently blocked due to too many failed attempts.';
            } elseif ($attempts == 5) {
                $user->update(['locked_until' => now()->addHours(24)]);
                $msg = 'Too many failed attempts. Your account has been locked for 24 hours.';
            } elseif ($attempts == 3) {
                $user->update(['locked_until' => now()->addMinutes(60)]);
                $msg = 'Too many failed attempts. Your account has been locked for 60 minutes.';
            }

            return back()->withErrors(['login' => $msg]);
        }

        $voter = Voter::where('election_id', $election->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$voter) {
            return back()->withErrors(['login' => 'You are not registered for this election. Please register first.']);
        }

        // Reset failed attempts on successful login
        $user->update([
            'failed_login_attempts' => 0,
            'locked_until' => null
        ]);

        if ($voter->registration_status === 'pending' && !$election->auto_approve_voters) {
            return back()->withErrors(['login' => 'Your registration is still pending approval. Please wait 1 to 24 hours.']);
        }

        if ($voter->registration_status === 'declined') {
            return back()->withErrors(['login' => 'Your registration has been declined. You cannot sign in.']);
        }

        // Store voter in session
        session(['voter' => [
            'id' => $voter->id,
            'name' => $voter->name,
            'email' => $voter->email,
            'election_id' => $election->id,
            'role' => 'voter'
        ]]);

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
            return back()->withErrors(['login' => 'Failed to connect to the authentication service. Please check your internet connection and try again.'])->withInput();
        }

        // 🔑 Keep current session data for voter
        session([
            'otp_voter_id' => $voter->id,
            'otp_email' => $voter->email,
            'otp_election_id' => $election->id,
        ]);


        // Store OTP info in session for 2FA
        session([
            'otp_email' => $voter->email,
            'otp_user_id' => $voter->id,
            'remember_me' => $request->has('remember'),
        ]);


        // Redirect to OTP form instead of welcome
        return redirect()->route('voter.otp.form')
            ->with('success', 'A verification code has been sent to your email.');


        return redirect()->route('voter.elections.welcome', $election->id);
    }

    /**
     * Handle voter logout
     */
    public function logout(Request $request)
    {
        $request->session()->forget('voter');

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
