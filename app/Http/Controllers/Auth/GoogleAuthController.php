<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    public function handleCallback(Request $request)
    {
        try {
            $credential = $request->input('credential');

            if (!$credential) {
                return response()->json([
                    'success' => false,
                    'message' => 'No credential provided'
                ], 400);
            }

            // Decode JWT token (Base64Url)
            $tokenParts = explode('.', $credential);
            if (count($tokenParts) < 2) {
                return response()->json(['success' => false, 'message' => 'Invalid token format'], 400);
            }

            $payload = $tokenParts[1];
            $remainder = strlen($payload) % 4;
            if ($remainder) {
                $padlen = 4 - $remainder;
                $payload .= str_repeat('=', $padlen);
            }
            $tokenPayload = base64_decode(strtr($payload, '-_', '+/'));
            $jwtPayload = json_decode($tokenPayload);

            if (!$jwtPayload || !isset($jwtPayload->email)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid token payload'
                ], 400);
            }

            // 1. Try to find the user by google_id first
            $user = User::where('google_id', $jwtPayload->sub)->first();

            if (!$user) {
                // 2. If not found by google_id, try to find by email
                $user = User::where('email', $jwtPayload->email)->first();

                if ($user) {
                    // 3. If found by email, link the Google account
                    $user->update([
                        'google_id' => $jwtPayload->sub,
                        'email_verified_at' => $user->email_verified_at ?? now(),
                    ]);
                } else {
                    // 4. If still not found, create a new user
                    $user = User::create([
                        'name' => $jwtPayload->name ?? 'Google User',
                        'email' => $jwtPayload->email,
                        'password' => Hash::make(Str::random(24)),
                        'email_verified_at' => now(),
                        'google_id' => $jwtPayload->sub,
                        'role' => User::ROLE_ADMIN, // Default to Admin for now, as requested for admins
                    ]);
                }
            }

            Auth::login($user);

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
