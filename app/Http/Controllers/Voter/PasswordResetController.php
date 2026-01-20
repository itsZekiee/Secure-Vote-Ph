<?php

namespace App\Http\Controllers\Voter;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Election;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class PasswordResetController extends Controller
{
    public function searchEmails(Request $request, $code)
    {
        $query = $request->get('q');
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $emails = User::where('role', 'voter')
            ->where('email', 'LIKE', "{$query}%")
            ->limit(10)
            ->pluck('email');

        return response()->json($emails);
    }

    public function sendOTP(Request $request, $code)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Email not found.'], 404);
        }

        $otp = rand(10000000, 99999999);

        // Store OTP in session with expiration
        session([
            'password_reset_otp' => $otp,
            'password_reset_email' => $request->email,
            'password_reset_otp_expires' => now()->addMinutes(10)
        ]);

        try {
            Mail::raw("Your Password Reset OTP is: $otp", function ($message) use ($request) {
                $message->to($request->email)->subject('Voter Password Reset OTP');
            });
            return response()->json(['success' => true, 'message' => 'OTP sent to your email.']);
        } catch (\Exception $e) {
            Log::error('OTP Send failed: ' . $e->getMessage());
            // For development/demo, we can return the OTP if mail fails, but better to just log
            return response()->json(['success' => false, 'message' => 'Failed to send OTP.'], 500);
        }
    }

    public function verifyOTP(Request $request, $code)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:8'
        ]);

        $sessionOtp = session('password_reset_otp');
        $sessionEmail = session('password_reset_email');
        $expires = session('password_reset_otp_expires');

        if (!$sessionOtp || $sessionOtp != $request->otp || $sessionEmail != $request->email || now()->gt($expires)) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired OTP.'], 422);
        }

        // Generate a temporary token to unlock the password change form
        $token = Str::random(64);
        session(['password_reset_verified' => true]);

        return response()->json(['success' => true]);

    }

    public function showLinkRequestForm($code)
    {
        $election = Election::where('code', $code)->firstOrFail();
        return view('voter.auth.forgot-password', compact('election'));
    }

    public function sendResetLinkEmail(Request $request, $code)
    {
        $request->validate(['email' => 'required|email']);
        $election = Election::where('code', $code)->firstOrFail();

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'We could not find a user with that email address.']);
        }

        // Generate token
        $token = Str::random(64);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        // Send email (In a real app, use Mail::to($request->email)->send(new ResetPasswordMail($token)))
        // For now, let's just log it or use a simple mail if configured.
        // Given the environment, I'll assume standard Mail works.

        $resetUrl = route('voter.password.reset', ['code' => $election->code, 'token' => $token, 'email' => $request->email]);

        // Simulating email sending by putting it in session for demo if needed,
        // but let's try to use Mail::raw for simplicity if no Mailable exists.
        try {
            Mail::raw("Reset your password by clicking here: $resetUrl", function ($message) use ($request) {
                $message->to($request->email)->subject('Voter Password Reset');
            });
        } catch (\Exception $e) {
            \Log::error('Voter Password Reset Email failed: ' . $e->getMessage());
            // Fallback for demo: show link in success message (ONLY FOR DEV/DEMO)
            return back()->with('success', 'If the email address is valid, you will receive a password reset link shortly.');
        }

        return back()->with('success', 'We have emailed your password reset link!');
    }

    public function showResetForm(Request $request, $code, $token = null)
    {
        $election = Election::where('code', $code)->firstOrFail();
        return view('voter.auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
            'election' => $election
        ]);
    }

    public function reset(Request $request, $code)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        if (!session('password_reset_verified')) {
            return back()->withErrors([
                'email' => 'Unauthorized password reset attempt.'
            ]);
        }

        $hashed = Hash::make($request->password);

        $updated = DB::table('voters')
            ->where('email', $request->email)
            ->update(['password' => $hashed]);

        if (!$updated) {
            return back()->withErrors([
                'email' => 'No voter record found for this email.'
            ]);
        }

        session()->forget([
            'password_reset_otp',
            'password_reset_email',
            'password_reset_otp_expires',
            'password_reset_verified'
        ]);

        $election = Election::where('code', $code)->firstOrFail();

        return redirect()
            ->route('voter.registration.index', $election->id)
            ->with('success', 'Voter password has been reset successfully!');
    }



}
