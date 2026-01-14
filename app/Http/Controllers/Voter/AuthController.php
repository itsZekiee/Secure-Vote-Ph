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
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'voter',
        ]);

        Auth::login($user);

        // Redirect to welcome page if election data exists in session
        if (session()->has('election_id')) {
            $election = Election::find(session('election_id'));
            if ($election) {
                return redirect()->route('voter.elections.welcome', $election->code);
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

        $user = User::where('email', $request->email)->first();
        $electionId = session('election_id');

        // Check credentials WITHOUT logging in
        if (!Auth::validate($request->only('email', 'password'))) {
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

            return back()->withErrors([
                'email' => 'Invalid email or password.',
            ]);
        }

        // One Session Per User Policy
        $hasExistingSession = \DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', session()->getId())
            ->where('last_activity', '>=', now()->subMinutes(config('session.lifetime', 120))->getTimestamp())
            ->exists();

        if ($hasExistingSession) {
            return back()->withErrors([
                'email' => 'You are already logged in on another device. Please log out there first (Strict One-Device Policy).',
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
            'remember_me' => $request->boolean('remember'),
        ]);

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

        // Redirect to the proper route with election code
        return redirect()->route('voter.elections.welcome', $election->code);
    }
}
