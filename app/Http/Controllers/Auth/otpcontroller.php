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

        // Show OTP verification form
        public function show()
        {
            abort_unless(session()->has('otp_email'), 403);
            return view('auth.supabase.otp', [
                'verify_route' => route('otp.verify'),
                'back_route' => route('home')
            ]);
        }

        public function verify(Request $request)
        {
            $request->validate([
                'token' => 'required|digits:8',
            ]);

            $email = session('otp_email');
            $userId = session('otp_user_id');

            if (!$email || !$userId) {
                return redirect('/')->withErrors([
                    'token' => 'Session expired. Please login again.',
                ]);
            }

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

            if (!$response->successful()) {
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
                'remember_me',
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
