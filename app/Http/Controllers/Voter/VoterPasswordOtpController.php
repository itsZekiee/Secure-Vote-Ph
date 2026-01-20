<?php

namespace App\Http\Controllers\Voter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class VoterPasswordOtpController extends Controller
{
    /**
     * STEP 1
     * Send OTP using Supabase Email OTP (8-digit)
     */
    public function send(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $response = Http::withHeaders([
            'apikey' => config('services.supabase.anon_key'),
            'Content-Type' => 'application/json',
        ])->post(
                config('services.supabase.url') . '/auth/v1/otp',
                [
                    'email' => $request->email,
                    'type' => 'email',
                ]
            );

        if (!$response->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send verification code. Please try again.',
            ], 422);
        }

        // Store email in session for verification step
        Session::put('password_reset_email', $request->email);

        return response()->json([
            'success' => true,
            'message' => 'An 8-digit verification code has been sent to your email.',
        ]);
    }

    /**
     * STEP 2
     * Verify OTP using Supabase
     */
    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|min:8|max:8',
        ]);

        $email = Session::get('password_reset_email');

        if (!$email) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please start again.',
            ], 403);
        }

        $response = Http::withHeaders([
            'apikey' => config('services.supabase.anon_key'),
            'Content-Type' => 'application/json',
        ])->post(
                config('services.supabase.url') . '/auth/v1/verify',
                [
                    'email' => $email,
                    'token' => $request->otp,
                    'type' => 'email',
                ]
            );

        if (!$response->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired verification code.',
            ], 422);
        }

        // Mark OTP as verified for password reset
        Session::put('password_reset_verified', true);

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * OPTIONAL
     * Clear OTP session if user restarts flow
     */
    public function reset()
    {
        Session::forget([
            'password_reset_email',
            'password_reset_verified',
        ]);

        return response()->json([
            'success' => true,
        ]);
    }
}
