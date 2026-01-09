<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

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

        // Check if permanently blocked
        if ($user->is_permanently_blocked) {
            throw ValidationException::withMessages([
                'email' => 'Your account has been permanently blocked. Please contact the Administrator.',
            ]);
        }

        // Check if currently locked
        if ($user->locked_until && $user->locked_until->isFuture()) {
            $diff = $user->locked_until->diffForHumans();
            throw ValidationException::withMessages([
                'email' => "Your account is temporarily locked. Please try again in $diff.",
            ]);
        }

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $user->increment('failed_login_attempts');
            $attempts = $user->failed_login_attempts;
            $msg = __('The provided credentials do not match our records.');

            if ($attempts >= 6) {
                $user->update(['is_permanently_blocked' => true]);
                $msg = 'Your account has been permanently blocked due to too many failed attempts. Please contact the Administrator.';
            } elseif ($attempts == 5) {
                $user->update(['locked_until' => now()->addHours(24)]);
                $msg = 'Too many failed attempts. Your account has been locked for 24 hours.';
            } elseif ($attempts == 3) {
                $user->update(['locked_until' => now()->addMinutes(60)]);
                $msg = 'Too many failed attempts. Your account has been locked for 60 minutes.';
            }

            throw ValidationException::withMessages([
                'email' => $msg,
            ]);
        }

        $request->session()->regenerate();

        // Reset failed attempts
        $user->update([
            'failed_login_attempts' => 0,
            'locked_until' => null
        ]);

        // Ensure the user has the 'admin' role upon sign in
        if ($user->role !== 'admin') {
            $user->update(['role' => 'admin']);
        }

        return redirect()->intended(route('admin.dashboard'))->with('success', 'Welcome back!');
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been logged out successfully.');
    }
}
