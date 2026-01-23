<?php

namespace App\Http\Controllers\Voter;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Show the registration form
     */
    public function showRegisterForm()
    {
        return view('voter.registration.index');
    }

    /**
     * Handle voter registration
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => trim(strtolower($request->email)),
            'password' => $request->password, // Hashed by User model cast
            'role' => 'voter',
            'is_approved' => true,
        ]);

        Auth::login($user);

        // Redirect to welcome page if election data exists in session
        if (session()->has('election_id')) {
            $election = Election::find(session('election_id'));
            if ($election) {
                return redirect()->route('voter.elections.welcome', $election->id);
            }
        }

        return redirect()->route('voter.dashboard');
    }

    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('voter.registration.index');
    }

    /**
     * Handle voter login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $email = trim(strtolower($request->email));
        $electionId = session('election_id');

        $user = User::where('email', $email)
            ->where('election_id', $electionId)
            ->first();

        if ($user && (!$user->is_approved || !$user->is_active)) {
            $msg = 'Your account is not approved or is inactive. Please contact an administrator.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }
            return back()->withErrors([
                'email' => $msg,
            ]);
        }

        $electionId = session('election_id');

        // Check credentials WITHOUT logging in
        if (!$user || !Hash::check($request->password, $user->password)) {
            // Record failed login attempt
            \Illuminate\Support\Facades\DB::table('failed_logins')->insert([
                'user_id' => $user->id ?? null,
                'election_id' => $electionId,
                'email' => $request->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'reason' => 'Invalid credentials',
                'created_at' => now(),
            ]);

            $msg = 'Invalid email or password.';

            if ($user) {
                $user->increment('failed_login_attempts');
                $attempts = $user->failed_login_attempts;
                $maxAttempts = 6;
                $msg = "Invalid email or password. Attempt $attempts of $maxAttempts.";

                if ($user->role !== 'voter' && $user->role !== 'candidate') {
                    $msg .= " Since you have an " . ucfirst($user->role) . " account, please use your primary credentials.";
                }

                if ($attempts < 3) {
                    $msg .= " You will be locked out for 60 minutes after 3 failed attempts.";
                } elseif ($attempts == 4) {
                    $msg .= " You will be locked out for 24 hours after 5 failed attempts.";
                }

                if ($attempts >= $maxAttempts) {
                    $user->update(['is_permanently_blocked' => true]);
                    $msg = 'Your account has been permanently blocked due to too many failed attempts.';
                } elseif ($attempts == 5) {
                    $user->update(['locked_until' => now()->addHours(24)]);
                    $msg = 'Too many failed attempts. Your account has been locked for 24 hours.';
                } elseif ($attempts == 3) {
                    $user->update(['locked_until' => now()->addMinutes(60)]);
                    $msg = 'Too many failed attempts. Your account has been locked for 60 minutes.';
                }
            }

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 401);
            }

            return back()->withErrors([
                'email' => $msg,
            ]);
        }

        // Reset failed attempts on successful credentials validation
        if ($user) {
            $user->update([
                'failed_login_attempts' => 0,
                'locked_until' => null
            ]);
        }

        // One Session Per User Policy
        $hasExistingSession = \DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', session()->getId())
            ->where('last_activity', '>=', now()->subMinutes(config('session.lifetime', 120))->getTimestamp())
            ->exists();

        if ($hasExistingSession) {
            $msg = 'You are already logged in on another device. Please log out there first (Strict One-Device Policy).';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }
            return back()->withErrors([
                'email' => $msg,
            ]);
        }

        // 🔐 Send OTP via Supabase
        \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.supabase.service_key'),
            'apikey' => config('services.supabase.service_key'),
            'Content-Type' => 'application/json',
        ])->post(
                config('services.supabase.url') . '/auth/v1/otp',
                [
                    'email' => $user->email,
                    'type' => 'email',
                ]
            );

        // Store OTP session data
        session([
            'otp_email' => $user->email,
            'otp_user_id' => $user->id,
            'otp_election_id' => $electionId,
            'remember_me' => $request->boolean('remember'),
        ]);

        Http::withHeaders([
            'apikey' => config('services.supabase.anon_key'),
            'Content-Type' => 'application/json',
        ])->post(
                config('services.supabase.url') . '/auth/v1/otp',
                [
                    'email' => $user->email,
                    'type' => 'email',
                ]
            );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'A verification code has been sent to your email.',
                'redirect' => route('voter.otp.form')
            ]);
        }

        return redirect()
            ->route('voter.otp.form')
            ->with('success', 'A verification code has been sent to your email.');
    }


    /**
     * Handle voter logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('voter.elections.access');
    }

    /**
     * Show the welcome page with election details
     */
    public function welcome()
    {
        if (!session()->has('election_id')) {
            return redirect()->route('voter.elections.access')
                ->withErrors(['error' => 'Please enter an election code first.']);
        }

        $election = Election::find(session('election_id'));

        if (!$election) {
            session()->forget('election_id');
            return redirect()->route('voter.elections.access')
                ->withErrors(['error' => 'Election not found.']);
        }

        // Redirect to the proper route with election id
        return redirect()->route('voter.elections.welcome', $election->id);
    }
}
