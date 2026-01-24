<?php

namespace App\Http\Controllers\Voter;

use App\Http\Controllers\Controller;
use App\Models\Voter;
use App\Models\Election;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class PasswordResetController extends Controller
{
    public function searchEmails(Request $request, $code = null)
    {
        $query = $request->get('q');
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $election = null;
        if ($code) {
            $election = Election::where('code', $code)->orWhere('id', $code)->first();
        }

        $emails = Voter::where('email', 'LIKE', "{$query}%")
            ->when($election, function($q) use ($election) {
                return $q->where('election_id', $election->id);
            })
            ->limit(10)
            ->pluck('email');

        return response()->json($emails);
    }

    public function sendOTP(Request $request, $code = null)
    {
        $request->validate(['email' => 'required|email']);

        $election = null;
        if ($code) {
            $election = Election::where('code', $code)->orWhere('id', $code)->first();
        }

        $voter = Voter::where('email', $request->email)
            ->when($election, function($q) use ($election) {
                return $q->where('election_id', $election->id);
            })
            ->first();

        if (!$voter) {
            return response()->json(['success' => false, 'message' => 'Email not found for this election.'], 404);
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
            return response()->json(['success' => false, 'message' => 'Failed to send OTP.'], 500);
        }
    }

    public function verifyOTP(Request $request, $code = null)
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
        session(['password_reset_verified' => true]);

        return response()->json(['success' => true]);

    }

    public function showLinkRequestForm(Request $request, $code = null)
    {
        $election = null;
        $electionId = $code ?: $request->get('election');

        if ($electionId) {
            $election = Election::where('id', $electionId)
                ->orWhere('code', $electionId)
                ->first();
        }
        return view('voter.auth.forgot-password', compact('election'));
    }



    public function showResetForm(Request $request, $code = null, $token = null)
    {
        $election = null;
        if ($code) {
            $election = Election::where('code', $code)->first();
        }
        return view('voter.auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
            'election' => $election
        ]);
    }

    public function reset(Request $request, $code = null)
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

        $election = null;
        if ($code) {
            $election = Election::where('code', $code)->orWhere('id', $code)->first();
        }

        $voter = Voter::where('email', trim(strtolower($request->email)))
            ->when($election, function($q) use ($election) {
                return $q->where('election_id', $election->id);
            })
            ->first();

        if (!$voter) {
            return back()->withErrors([
                'email' => 'Voter account not found.'
            ]);
        }

        $hashed = Hash::make($request->password);

        $voter->update([
            'password' => $hashed,
            'failed_login_attempts' => 0,
            'locked_until' => null,
            'is_permanently_blocked' => 0,
        ]);

        \App\Services\AuditLogger::log(
            'PASSWORD_RESET',
            'Voter',
            "Voter password reset successful for: {$voter->email} in election: " . ($voter->election_id ?? 'N/A')
        );

        session()->forget([
            'password_reset_otp',
            'password_reset_email',
            'password_reset_otp_expires',
            'password_reset_verified'
        ]);

        if ($election) {
            return redirect()
                ->route('voter.registration.index', $election->code)
                ->with('success', 'Your password has been reset successfully!');
        }

        return redirect()
            ->route('voter.elections.access')
            ->with('success', 'Your password has been reset successfully! Please sign in.');
    }



}
