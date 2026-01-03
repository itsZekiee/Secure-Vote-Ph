<?php

namespace App\Http\Controllers\Voter;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Show the registration form
     */
    public function showRegisterForm()
    {
        return view('voter.registration.index');
    }

    /**
     * Handle voter registration
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'voter',
        ]);

        Auth::login($user);

        // Redirect to welcome page if election data exists in session
        if (session()->has('election_id')) {
            return redirect()->route('voter.elections.welcome');
        }

        return redirect()->route('voter.dashboard');
    }

    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('voter.registration.index');
    }

    /**
     * Handle voter login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Redirect to welcome page if election data exists in session
            if (session()->has('election_id')) {
                return redirect()->route('voter.elections.welcome');
            }

            return redirect()->intended(route('voter.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle voter logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('voter.elections.access');
    }

    /**
     * Show the welcome page with election details
     */
    public function welcome()
    {
        if (!session()->has('election_id')) {
            return redirect()->route('voter.elections.access')
                ->withErrors(['error' => 'Please enter an election code first.']);
        }

        return view('voter.welcome');
    }
}
