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

            // Decode JWT token
            $tokenParts = explode('.', $credential);
            $tokenPayload = base64_decode($tokenParts[1]);
            $jwtPayload = json_decode($tokenPayload);

            if (!$jwtPayload) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid token'
                ], 400);
            }

            // Find or create user
            $user = User::where('email', $jwtPayload->email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $jwtPayload->name,
                    'email' => $jwtPayload->email,
                    'password' => Hash::make(Str::random(24)),
                    'email_verified_at' => now(),
                    'google_id' => $jwtPayload->sub ?? null,
                ]);
            }

            Auth::login($user);

            return response()->json([
                'success' => true,
                'message' => 'Successfully authenticated with Google',
                'redirect' => route('admin.dashboard')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
