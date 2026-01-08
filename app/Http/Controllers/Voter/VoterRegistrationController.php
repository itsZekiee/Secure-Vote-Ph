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
        $registrationOver = $election->registration_deadline && now()->gt($election->registration_deadline);

        return view('voter.registration.index', [
            'election' => $election,
            'registrationOver' => $registrationOver
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
            'email' => 'required|email|unique:voters,email,NULL,id,election_id,' . (string)$election->id,
            'phone' => 'required|string|max:20',
            'student_id' => 'nullable|string|max:50',
            'password' => 'required|string|min:6|confirmed',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        if ($election->require_geo_registration) {
            if (!$request->latitude || !$request->longitude) {
                return back()->withErrors(['registration' => 'Location access is required to register for this election. Please enable GPS.'])->withInput();
            }

            $distance = $this->calculateDistance(
                $election->geo_latitude,
                $election->geo_longitude,
                $request->latitude,
                $request->longitude
            );

            if ($distance > ($election->geo_radius_meters + 10)) { // Add 10m buffer for GPS accuracy
                return back()->withErrors(['registration' => 'You must be within the designated voting area to register. (Distance: ' . round($distance) . 'm, Allowed: ' . $election->geo_radius_meters . 'm)'])->withInput();
            }
        }

        // Split full name into first/middle/last like candidate creation logic
        $userNameForCandidate = $request->name;
        $nameParts = preg_split('/\s+/', trim($userNameForCandidate));
        if (count($nameParts) === 1) {
            $firstName = $nameParts[0];
            $middleName = null;
            $lastName = $nameParts[0];
        } elseif (count($nameParts) === 2) {
            $firstName = $nameParts[0];
            $middleName = null;
            $lastName = $nameParts[1];
        } else {
            $firstName = array_shift($nameParts);
            $lastName = array_pop($nameParts);
            $middleName = implode(' ', $nameParts);
        }

        $voter = Voter::create([
            'election_id' => $election->id,
            'name' => $request->name,
            'first_name' => $firstName,
            'middle_name' => $middleName,
            'last_name' => $lastName,
            'email' => $request->email,
            'student_id' => $request->student_id ?? null,
            'password' => Hash::make($request->password),
            'registration_status' => $election->auto_approve_voters ? 'approved' : 'pending',
        ]);

        $msg = $election->auto_approve_voters
            ? 'Registration successful! You can now Sign In.'
            : 'Registration submitted! Please wait 1 to 24 hours for admin approval of your registration before you can Sign In.';

        return redirect()->route('voter.registration.index', $election->code)
            ->with('success', $msg);
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
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        if ($election->require_geo_registration) {
            if (!$request->latitude || !$request->longitude) {
                return back()->withErrors(['login' => 'Location access is required to sign in for this election. Please enable GPS.'])->withInput();
            }

            $distance = $this->calculateDistance(
                $election->geo_latitude,
                $election->geo_longitude,
                $request->latitude,
                $request->longitude
            );

            if ($distance > ($election->geo_radius_meters + 10)) { // Add 10m buffer for GPS accuracy
                return back()->withErrors(['login' => 'You must be within the designated voting area to sign in. (Distance: ' . round($distance) . 'm, Allowed: ' . $election->geo_radius_meters . 'm)'])->withInput();
            }
        }

        $voter = Voter::where('election_id', $election->id)
            ->where('email', $request->email)
            ->first();

        if (!$voter || !Hash::check($request->password, $voter->password)) {
            return back()->withErrors(['login' => 'Invalid email or password.']);
        }

        if ($voter->registration_status === 'pending' && !$election->auto_approve_voters) {
            return back()->withErrors(['login' => 'Your registration is still pending approval. Please wait 1 to 24 hours.']);
        }

        if ($voter->registration_status === 'declined') {
            return back()->withErrors(['login' => 'Your registration has been declined. You cannot sign in.']);
        }

        // Store voter in session
        session(['voter' => [
            'id' => $voter->id,
            'name' => $voter->name,
            'email' => $voter->email,
            'election_id' => $election->id,
            'role' => 'voter'
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

    /**
     * Calculate distance between two points in meters using Haversine formula
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meters

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
