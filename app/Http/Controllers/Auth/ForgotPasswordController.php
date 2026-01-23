<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)
            ->whereIn('role', [User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN, User::ROLE_ELECTION_OFFICER])
            ->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Email not found.'], 404);
        }

        $otp = rand(100000, 999999);

        // Store OTP in session with expiration
        session([
            'forgot_password_otp' => $otp,
            'forgot_password_email' => $request->email,
            'forgot_password_otp_expires' => now()->addMinutes(15)
        ]);

        try {
            Mail::raw("Your Secure Vote PH Password Reset OTP is: $otp", function ($message) use ($request) {
                $message->to($request->email)->subject('Password Reset OTP');
            });
            return response()->json(['success' => true, 'message' => 'OTP sent to your email.']);
        } catch (\Exception $e) {
            Log::error('Forgot Password OTP Send failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to send OTP.'], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric'
        ]);

        $sessionOtp = session('forgot_password_otp');
        $sessionEmail = session('forgot_password_email');
        $expires = session('forgot_password_otp_expires');

        if (!$sessionOtp || $sessionOtp != $request->otp || $sessionEmail != $request->email || now()->gt($expires)) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired OTP.'], 422);
        }

        // Generate a temporary token to unlock the password change form
        $token = Str::random(64);
        session(['forgot_password_verified_token' => $token]);

        return response()->json(['success' => true, 'token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
            'token' => 'required'
        ]);

        $sessionToken = session('forgot_password_verified_token');
        $sessionEmail = session('forgot_password_email');

        if (!$sessionToken || $sessionToken !== $request->token || $sessionEmail !== $request->email) {
            return response()->json(['success' => false, 'message' => 'Unauthorized password reset attempt.'], 403);
        }

        $user = User::where('email', $request->email)
            ->whereIn('role', [User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN, User::ROLE_ELECTION_OFFICER])
            ->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found.'], 404);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);


        // Clear session
        session()->forget([
            'forgot_password_otp',
            'forgot_password_email',
            'forgot_password_otp_expires',
            'forgot_password_verified_token'
        ]);

        return response()->json(['success' => true, 'message' => 'Your password has been reset successfully!']);
    }
}
