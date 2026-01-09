<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class MagicLinkController extends Controller
{
    public function handleMagicLink(Request $request)
    {
        // The magic link sends an access_token as a query parameter
        $accessToken = $request->query('access_token');

        if (!$accessToken) {
            return redirect('/login')->withErrors(['email' => 'Invalid or expired login link.']);
        }

        // Verify the access token with Supabase
        $response = Http::withHeaders([
            'apikey' => config('services.supabase.anon_key'),
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get(config('services.supabase.url') . '/auth/v1/user');

        if (!$response->successful()) {
            return redirect('/login')->withErrors(['email' => 'Invalid or expired login link.']);
        }

        $userData = $response->json();

        // Find or create the user in your database
        $user = User::firstOrCreate(
            ['email' => $userData['email']],
            [
                'name' => $userData['user_metadata']['full_name'] ?? $userData['email'],
                // add other user data here if needed
            ]
        );

        // Log in the user into Laravel
        Auth::login($user, true);

        return redirect('/admin/dashboard')->with('success', 'Login successful via magic link.');
    }
}
