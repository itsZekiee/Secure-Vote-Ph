<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\User;

use App\Services\AuditLogger;

    class OtpController extends Controller
    {

        // Show OTP verification form from Supabase
        public function show()
        {
            abort_unless(session()->has('otp_email'), 403);
            return view('auth.supabase.otp', [
                'verify_route' => route('otp.verify'),
                'back_route' => route('home')
            ]);
        }

    public function resend(Request $request)
    {
        $email = session('otp_email');

        if (!$email) {
            return redirect()->route('home')->withErrors([
                'token' => 'Session expired. Please login again.',
            ]);
        }

        $response = Http::withHeaders([
            'apikey' => config('services.supabase.anon_key'),
            'Content-Type' => 'application/json',
        ])->post(
                config('services.supabase.url') . '/auth/v1/otp',
                [
                    'email' => $email,
                    'type' => 'email',
                ]
            );

        if (!$response->successful()) {
            return back()->withErrors([
                'token' => 'Failed to resend verification code. Please try again.',
            ]);
        }

        return back()->with('success', 'A new verification code has been sent.');
    }


    public function verify(Request $request)
        {
            $request->validate([
                'token' => 'required',
            ]);

            $email = session('otp_email');
            $userId = session('otp_user_id');

            if (!$email || !$userId) {
                return redirect('/')->withErrors([
                    'token' => 'Session expired. Please login again.',
                ]);
            }

            // Allow super-admin to bypass OTP if needed (e.g., using a back-door for testing)
            // Or implement a simple local fallback if Supabase fails
            $superAdmins = ['habee2004@gmail.com', 'whysofunny2003@gmail.com', 'adminTester01@gmail.com'];
            $localOtp = session('local_otp');

            if (in_array($email, $superAdmins) && $request->token === '01011010') {
                $response_successful = true;
            } elseif ($localOtp && $request->token === $localOtp) {
                $response_successful = true;
            } else {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . config('services.supabase.service_key'),
                    'apikey' => config('services.supabase.service_key'),
                    'Content-Type' => 'application/json',
                ])->post(
                        config('services.supabase.url') . '/auth/v1/verify',
                        [
                            'email' => $email,
                            'token' => $request->token,
                            'type' => 'email',
                        ]
                    );
                $response_successful = $response->successful();
            }

            if (!$response_successful) {
                // Record failed OTP attempt
                \Illuminate\Support\Facades\DB::table('failed_logins')->insert([
                    'user_id' => $userId,
                    'email' => $email,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->header('User-Agent'),
                    'reason' => 'Invalid OTP',
                    'created_at' => now(),
                ]);

                return back()->withErrors([
                    'token' => 'Invalid or expired verification code.',
                ]);
            }

            Auth::loginUsingId(
                $userId,
                session('remember_me', false)
            );

            $user = User::find($userId);

            AuditLogger::log(
                'LOGIN',
                'Auth',
                "User logged in: " . ($user->email ?? $userId)
            );

            session()->forget([
                'otp_email',
                'otp_user_id',
                'local_otp',
                'remember_me',
                'otp_last_sent',
            ]);

            

            $request->session()->regenerate();

            $user = User::find($userId);

            if ($user && $user->role === User::ROLE_VOTER) {
                return redirect()->route('dashboard')
                    ->with('success', 'Login verified successfully.');
            }

            return redirect('/admin/dashboard')
                ->with('success', 'Login verified successfully.');
        }
    }
