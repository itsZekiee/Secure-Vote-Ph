<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('welcome');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => __('The provided credentials do not match our records.'),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Account Status Checks (BEFORE OTP)
        |--------------------------------------------------------------------------
        */

        // Permanently blocked
        if ($user->is_permanently_blocked) {
            throw ValidationException::withMessages([
                'email' => 'Your account has been permanently blocked. Please contact the Administrator.',
            ]);
        }

        // Temporarily locked
        if ($user->locked_until && $user->locked_until->isFuture()) {
            $diff = $user->locked_until->diffForHumans();
            throw ValidationException::withMessages([
                'email' => "Your account is temporarily locked. Please try again in $diff.",
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Validate Credentials (NO LOGIN YET)
        |--------------------------------------------------------------------------
        */

        if (!Auth::validate($request->only('email', 'password'))) {
            $user->increment('failed_login_attempts');
            $attempts = $user->failed_login_attempts;

            $message = __('The provided credentials do not match our records.');

            if ($attempts >= 6) {
                $user->update(['is_permanently_blocked' => true]);
                $message = 'Your account has been permanently blocked due to too many failed attempts. Please contact the Administrator.';
            } elseif ($attempts == 5) {
                $user->update(['locked_until' => now()->addHours(24)]);
                $message = 'Too many failed attempts. Your account has been locked for 24 hours.';
            } elseif ($attempts == 3) {
                $user->update(['locked_until' => now()->addMinutes(60)]);
                $message = 'Too many failed attempts. Your account has been locked for 60 minutes.';
            }

            throw ValidationException::withMessages([
                'email' => $message,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Send OTP via Supabase
        |--------------------------------------------------------------------------
        */

        Http::withHeaders([
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

        /*
        |--------------------------------------------------------------------------
        | Store OTP Session Data
        |--------------------------------------------------------------------------
        */

        session([
            'otp_email' => $user->email,
            'otp_user_id' => $user->id,
            'remember_me' => $request->boolean('remember'),
        ]);

        return redirect()
            ->route('otp.form')
            ->with('success', 'A verification code has been sent to your email.');
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been logged out successfully.');
    }
}
