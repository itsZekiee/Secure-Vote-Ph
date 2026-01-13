<?php
namespace App\Http\Controllers\Voter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\User;

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
            return back()->withErrors([
                'token' => 'Invalid or expired verification code.',
            ]);
        }

        Auth::loginUsingId(
            $userId,
            session('remember_me', false)
        );

        session()->forget([
            'otp_email',
            'otp_user_id',
            'remember_me',
        ]);

        $request->session()->regenerate();

        return redirect()->route('voter.welcome')
            ->with('success', 'Login verified successfully.');
    }
}
