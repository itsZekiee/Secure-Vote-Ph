<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Http\Controllers\Auth\OtpController;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;

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
        | Check for existing session (One Session Per User Policy)
        |--------------------------------------------------------------------------
        */
        $hasExistingSession = \DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', session()->getId())
            ->exists();

        if ($hasExistingSession) {
            throw ValidationException::withMessages([
                'email' => 'You are already logged in on another device. Please log out there first.',
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

        try {
            // Generate a local OTP as a fallback or for Mail delivery
            $localOtp = str_pad(random_int(0, 99999999), 8, '0', STR_PAD_LEFT);

            $response = Http::timeout(10)->withHeaders([
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

            if ($response->successful()) {
                session([
                    'otp_email' => $user->email,
                    'otp_user_id' => $user->id,
                    'remember_me' => $request->boolean('remember'),
                ]);

                return redirect()
                    ->route('otp.form')
                    ->with('success', 'A verification code has been sent to your email.');
            } else {
                \Illuminate\Support\Facades\Log::error('Supabase OTP Error: ' . $response->body());

                // Fallback to Laravel Mail if Supabase fails
                try {
                    Mail::to($user->email)->send(new OtpMail($localOtp));

                    session([
                        'otp_email' => $user->email,
                        'otp_user_id' => $user->id,
                        'local_otp' => $localOtp,
                        'remember_me' => $request->boolean('remember'),
                    ]);

                    return redirect()
                        ->route('otp.form')
                        ->with('success', 'Verification code sent via email (Supabase unavailable).');
                } catch (\Exception $mailEx) {
                    \Illuminate\Support\Facades\Log::error('Mail Fallback Failed: ' . $mailEx->getMessage());
                }

                // For super-admin, we might want a bypass or a very clear error
                $superAdmins = ['habee2004@gmail.com', 'whysofunny2003@gmail.com', 'adminTester01@gmail.com'];
                if (in_array($user->email, $superAdmins)) {
                    // Log more details for debugging
                    \Illuminate\Support\Facades\Log::emergency('CRITICAL: SuperAdmin OTP failed. Body: ' . $response->body());

                    // Ensure session data is set before redirecting for super admin bypass
                    session([
                        'otp_email' => $user->email,
                        'otp_user_id' => $user->id,
                        'remember_me' => $request->boolean('remember'),
                    ]);

                    return redirect()
                        ->route('otp.form')
                        ->with('success', 'OTP service is temporarily unavailable. Use your backup security code.');
                }

                throw ValidationException::withMessages([
                    'email' => 'Failed to send verification code. Please try again later.',
                ]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Supabase OTP connection error: ' . $e->getMessage());

            // Fallback to Laravel Mail if Supabase connection fails
            try {
                Mail::to($user->email)->send(new OtpMail($localOtp));

                session([
                    'otp_email' => $user->email,
                    'otp_user_id' => $user->id,
                    'local_otp' => $localOtp,
                    'remember_me' => $request->boolean('remember'),
                ]);

                return redirect()
                    ->route('otp.form')
                    ->with('success', 'Verification code sent via email (Auth service connection error).');
            } catch (\Exception $mailEx) {
                \Illuminate\Support\Facades\Log::error('Mail Fallback Failed (after connection error): ' . $mailEx->getMessage());
            }

            throw ValidationException::withMessages([
                'email' => 'Failed to connect to the authentication service. Please check your internet connection and try again.',
            ]);
        }

    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been logged out successfully.');
    }
}
