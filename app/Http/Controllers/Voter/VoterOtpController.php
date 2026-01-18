<?php
namespace App\Http\Controllers\Voter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Services\AuditLogger;

class VoterOtpController extends Controller
{

    // Show OTP verification form
    public function show()
    {
        abort_unless(session()->has('otp_email'), 403);
        return view('auth.supabase.otp', [
            'verify_route' => route('voter.otp.verify'),
            'back_route' => route('voter.elections.access')
        ]);
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

        // Allow super-admin to bypass OTP if needed
        $superAdmins = ['habee2004@gmail.com', 'whysofunny2003@gmail.com', 'adminTester01@gmail.com'];
        if (in_array($email, $superAdmins) && $request->token === '01011010') {
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
                'election_id' => session('election_id'),
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

        // Set voter session for VoterAuth middleware
        $electionId = session('otp_election_id');
        if ($electionId) {
            $voter = \App\Models\Voter::where('user_id', $user->id)
                ->where('election_id', $electionId)
                ->first();
            if ($voter) {
                session(['voter' => [
                    'id' => $voter->id,
                    'name' => $voter->name,
                    'email' => $voter->email,
                    'election_id' => $voter->election_id,
                    'role' => 'voter'
                ]]);
            }
        }

        AuditLogger::log(
            'LOGIN',
            'Auth',
            "Voter logged in: " . ($user->email ?? $userId)
        );

        session()->forget([
            'otp_email',
            'otp_user_id',
            'remember_me',
        ]);

        $request->session()->regenerate();

        if (session()->has('otp_election_id')) {
            return redirect()->route('voter.elections.welcome', session('otp_election_id'))
                ->with('success', 'Login verified successfully.');
        }

        return redirect()->route('voter.welcome')
            ->with('success', 'Login verified successfully.');
    }
}
