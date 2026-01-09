<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use App\Services\SupabaseAuthService;
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

        // Validate credentials (DO NOT log in yet)
        if (!Auth::validate($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => __('The provided credentials do not match our records.'),
            ]);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        // Send OTP via Supabase
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
