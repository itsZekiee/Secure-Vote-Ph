<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;


class AuthController extends Controller
{
    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        Log::info('Login attempt', ['email' => $request->email]);

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {

            $user = Auth::user();

            // Send OTP using Supabase
            Http::withHeaders([
                'apikey' => config('services.supabase.service_key'),
                'Authorization' => 'Bearer ' . config('services.supabase.service_key'),
            ])->post(config('services.supabase.url') . '/auth/v1/otp', [
                        'email' => $user->email,
                        'type' => 'email',
                    ]);

            // Logout temporarily until OTP is verified
            Auth::logout();

            // Store email for OTP verification
            session([
                'otp_email' => $user->email,
                'remember_me' => $request->filled('remember')
            ]);

            Log::info('OTP sent', ['email' => $user->email]);

            return redirect('/auth/otp')
                ->with('success', 'We sent a verification code to your email.');
        }


        Log::warning('Login failed - Invalid credentials', ['email' => $request->email]);

        // Record failed login attempt for analytics (ghost registrations)
        try {
            DB::table('failed_logins')->insert([
                'email' => $request->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to record failed_login', ['error' => $e->getMessage()]);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle registration request
     */
    public function register(Request $request)
    {
        Log::info('Registration attempt', ['email' => $request->email]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            Log::info('User registered successfully', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            Auth::login($user);

            return redirect('/admin/dashboard')
                ->with('success', 'Account created successfully! Welcome, ' . $user->name . '!');

        } catch (\Exception $e) {
            Log::error('Registration failed', [
                'error' => $e->getMessage(),
                'email' => $request->email
            ]);

            return back()->withErrors([
                'email' => 'Registration failed. Please try again.',
            ])->withInput($request->except('password', 'password_confirmation'));
        }
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        $userName = Auth::user()->name;

        Log::info('User logout', ['user_id' => Auth::id()]);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')
            ->with('success', 'You have been logged out successfully.');
    }
}
