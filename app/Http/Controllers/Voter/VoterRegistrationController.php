<?php

namespace App\Http\Controllers\Voter;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Models\Voter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class VoterRegistrationController extends Controller
{
    /**
     * Step 2: Show registration/login form
     */
    public function index($code)
    {
        $election = Election::where('code', $code)
            ->with('organization')
            ->first();

        if (!$election) {
            return redirect()->route('voter.elections.access')
                ->withErrors(['code' => 'Election not found.']);
        }

        // Registration deadline check
        if ($election->registration_deadline && now()->gt($election->registration_deadline)) {
            return redirect()->route('voter.elections.access')
                ->withErrors(['registration' => 'Registration is over.']);
        }

        return view('voter.registration.index', [
            'election' => $election
        ]);
    }

    /**
     * Step 2: Register new voter
     */
    public function store(Request $request, $code)
    {
        $election = Election::where('code', $code)->first();

        if (!$election) {
            return redirect()->route('voter.elections.access')
                ->withErrors(['code' => 'Election not found.']);
        }

        // Registration deadline check
        if ($election->registration_deadline && now()->gt($election->registration_deadline)) {
            return back()->withErrors(['registration' => 'Registration is over.'])->withInput();
        }

        // Accepted domains validation
        if ($election->accepted_domains) {
            $domains = array_map('trim', explode(',', $election->accepted_domains));
            $email = $request->email;
            $isValidDomain = false;
            foreach ($domains as $domain) {
                if (str_ends_with($email, $domain)) {
                    $isValidDomain = true;
                    break;
                }
            }
            if (!$isValidDomain) {
                return back()->withErrors(['email' => 'Only emails from the following domains are allowed: ' . $election->accepted_domains])->withInput();
            }
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:voters,email,NULL,id,election_id,' . $election->id,
            'student_id' => 'nullable|string|max:50',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $voter = Voter::create([
            'election_id' => $election->id,
            'name' => $request->name,
            'email' => $request->email,
            'student_id' => $request->student_id,
            'password' => Hash::make($request->password),
        ]);

        // Store voter in session
        session(['voter' => [
            'id' => $voter->id,
            'name' => $voter->name,
            'email' => $voter->email,
            'election_id' => $election->id
        ]]);

        return redirect()->route('voter.elections.welcome', $election->code)
            ->with('success', 'Registration successful!');
    }

    /**
     * Step 2: Login existing voter
     */
    public function login(Request $request, $code)
    {
        $election = Election::where('code', $code)->first();

        if (!$election) {
            return redirect()->route('voter.elections.access')
                ->withErrors(['code' => 'Election not found.']);
        }

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $voter = Voter::where('election_id', $election->id)
            ->where('email', $request->email)
            ->first();

        if (!$voter || !Hash::check($request->password, $voter->password)) {
            return back()->withErrors(['login' => 'Invalid email or password.']);
        }

        // Store voter in session
        session(['voter' => [
            'id' => $voter->id,
            'name' => $voter->name,
            'email' => $voter->email,
            'election_id' => $election->id
        ]]);

        return redirect()->route('voter.elections.welcome', $election->code);
    }

    /**
     * Handle voter logout
     */
    public function logout(Request $request)
    {
        $request->session()->forget('voter');

        return redirect()->route('voter.elections.access')
            ->with('success', 'You have been logged out.');
    }
}
