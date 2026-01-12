<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use Laravel\Socialite\Facades\Socialite;
use App\Services\AuditLogger;

class GoogleAuthController extends Controller
{
    public function handleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google-one-tap')->user();

            if (!$googleUser || !$googleUser->getEmail()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Google user data'
                ], 400);
            }

            // 1. Try to find the user by google_id first
            $user = User::where('google_id', $googleUser->getId())->first();

            if (!$user) {
                // 2. If not found by google_id, try to find by email
                $user = User::where('email', $googleUser->getEmail())->first();

                if ($user) {
                    // 3. If found by email, link the Google account
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'email_verified_at' => $user->email_verified_at ?? now(),
                    ]);
                } else {
                    // 4. If still not found, create a new user
                    $user = User::create([
                        'name' => $googleUser->getName() ?? 'Google User',
                        'email' => $googleUser->getEmail(),
                        'password' => Hash::make(Str::random(24)),
                        'email_verified_at' => now(),
                        'google_id' => $googleUser->getId(),
                        'role' => User::ROLE_ADMIN, // Default to Admin as requested for admins
                    ]);
                }
            }

            Auth::login($user);

            AuditLogger::log(
                'LOGIN',
                'Auth',
                "User logged in via Google: " . $user->email
            );

            // Ensure the user has the 'admin' role upon sign in if they are meant to be an admin
            if (!$user->role || $user->role === User::ROLE_VOTER) {
                 $user->update(['role' => User::ROLE_ADMIN]);
            }

            // Determine redirect path based on role
            $redirect = $user->isAdmin() || $user->isElectionOfficer()
                ? route('admin.dashboard')
                : route('dashboard');

            return response()->json([
                'success' => true,
                'message' => 'Successfully authenticated with Google',
                'redirect' => $redirect
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
