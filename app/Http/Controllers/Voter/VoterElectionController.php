<?php

namespace App\Http\Controllers\Voter;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Models\Vote;
use App\Models\Voter;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VoterElectionController extends Controller
{
    /**
     * Step 1: Show access form (enter election code/link)
     */
    public function access()
    {
        return view('voter.elections.access');
    }

    /**
     * Step 1: Verify election code/link
     */
    public function verify(Request $request)
    {
        $inputType = $request->input('input_type');

        if ($inputType === 'code') {
            $request->validate([
                'election_code' => 'required|string|size:6'
            ]);
            $code = strtoupper($request->election_code);
        } else {
            $request->validate([
                'election_link' => 'required|url'
            ]);
            $code = $this->extractCodeFromLink($request->election_link);
        }

        $election = Election::where('code', $code)
            ->where('status', '!=', 'cancelled')
            ->first();

        if (!$election) {
            return back()->withErrors(['election_code' => 'Invalid election code or link. Please check and try again.']);
        }

        // Store election in session and redirect to login form
        session(['election_id' => $election->id, 'election_code' => $election->code]);

        return redirect()->route('voter.registration.index', ['election' => $election->id, 'form' => 'login']);
    }

    /**
     * Step 3: Welcome page with countdown
     */
    public function welcome(Election $election)
    {
        $voter = session('voter');

        if (!$voter && auth()->check()) {
            // Admin or other web-guard user previewing
            $voter = [
                'id' => null,
                'name' => auth()->user()->name,
                'role' => 'admin'
            ];
        }

        if (!$voter) {
            return redirect()->route('voter.registration.index', $election->id)
                ->withErrors(['session' => 'Please sign in to view the election welcome page.']);
        }

        $ballotCount = $voter['id'] ? $this->getBallotCount($election->id, $voter['id']) : 0;
        $hasVoted = $voter['id'] ? ($ballotCount >= ($election->max_votes ?? 1)) : false;

        return view('voter.welcome', [
            'election' => $election,
            'voter' => $voter,
            'hasVoted' => $hasVoted,
            'ballotCount' => $ballotCount,
            'maxVotes' => $election->max_votes ?? 1
        ]);
    }


    /**
     * Step 4: Display voting page with positions and candidates
     */
    public function index(Election $election)
    {
        $voter = session('voter');

        // Rest of existing logic...
        if (Carbon::now()->lt($election->start_date)) {
            return redirect()->route('voter.elections.welcome', $election->id)
                ->withErrors(['election' => 'Election has not started yet.']);
        }

        if (Carbon::now()->gt($election->end_date)) {
            return redirect()->route('voter.elections.welcome', $election->id)
                ->withErrors(['election' => 'Election has already ended.']);
        }

        if ($this->getBallotCount($election->id, $voter['id']) >= ($election->max_votes ?? 1)) {
            return redirect()->route('voter.elections.welcome', $election->id)
                ->with('info', 'You have already reached the maximum number of votes allowed for this election.');
        }

        $positions = $election->positions()
            ->with(['candidates.partylist'])
            ->orderBy('order')
            ->get();

        return view('voter.elections.index', [
            'election' => $election,
            'positions' => $positions,
            'voter' => $voter
        ]);
    }


    /**
     * Process the vote submission
     */
    public function submitVote(Request $request, Election $election)
    {
        $voter = session('voter');
        if (!$voter) {
            return redirect()->route('voter.elections.access')
                ->withErrors(['error' => 'Voter session expired.']);
        }

        // Check if already voted max times
        $maxVotes = $election->max_votes ?? 1;
        $votedCount = $this->getBallotCount($election->id, $voter['id']);

        if ($votedCount >= $maxVotes) {
            $msg = 'You have already reached the maximum number of votes allowed for this election.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->route('voter.elections.welcome', $election->id)
                ->withErrors(['error' => $msg]);
        }

        $request->validate([
            'votes' => 'required|array',
            'votes.*' => ['required', function ($attribute, $value, $fail) {
                if (is_array($value)) {
                    foreach ($value as $id) {
                        if ($id !== 'abstain' && !\DB::table('candidates')->where('id', $id)->exists()) {
                            $fail("The selected $attribute is invalid.");
                        }
                    }
                } else {
                    if ($value !== 'abstain' && !\DB::table('candidates')->where('id', $value)->exists()) {
                        $fail("The selected $attribute is invalid.");
                    }
                }
            }],
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        if ($election->require_geo_verification) {
            if (!$request->latitude || !$request->longitude) {
                $msg = 'Location access is required to submit your vote. Please enable GPS.';
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 422);
                }
                return back()->withErrors(['error' => $msg]);
            }

            $distance = $this->calculateDistance(
                $election->geo_latitude,
                $election->geo_longitude,
                $request->latitude,
                $request->longitude
            );

            if ($distance > ($election->geo_radius_meters + 1000)) { // Increased buffer to 1000m to handle GPS accuracy issues
                $msg = 'You are currently outside the designated voting area. (Distance: ' . round($distance) . 'm, Allowed: ' . ($election->geo_radius_meters + 1000) . 'm). You must return to the designated area to submit your vote.';
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 422);
                }
                return back()->withErrors(['error' => $msg]);
            }
        }

        // Record votes
        $votedAt = now();
        $ballotId = \Str::random(10); // Generate a unique ID for this ballot
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();

        foreach ($request->votes as $positionId => $candidateIds) {
            // Handle both single candidate ID (string/int) and multiple IDs (array)
            $ids = is_array($candidateIds) ? $candidateIds : [$candidateIds];

            foreach ($ids as $candidateId) {
                if (!empty($candidateId)) {
                    Vote::create([
                        'ballot_id' => $ballotId,
                        'voter_id' => $voter['id'],
                        'election_id' => $election->id,
                        'position_id' => $positionId,
                        'candidate_id' => $candidateId === 'abstain' ? null : $candidateId,
                        'latitude' => $request->latitude,
                        'longitude' => $request->longitude,
                        'ip_address' => $ipAddress,
                        'user_agent' => $userAgent,
                        'voted_at' => $votedAt,
                    ]);
                }
            }
        }

        \App\Services\AuditLogger::log(
            'VOTE_CAST',
            'Voter',
            "Voter cast a vote in election: {$election->title} (Ballot ID: {$ballotId})"
        );

        $remainingVotes = $maxVotes - ($votedCount + 1);
        $successMsg = 'Your vote has been recorded successfully!';
        if ($remainingVotes > 0) {
            $successMsg .= " You have $remainingVotes " . \Str::plural('vote', $remainingVotes) . " remaining.";
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg,
                'remaining_votes' => $remainingVotes
            ]);
        }

        return redirect()->route('voter.elections.welcome', $election->code)
            ->with('success', $successMsg);
    }

    /**
     * Redirect join form to access page
     */
    public function showJoinForm()
    {
        return redirect()->route('voter.elections.access');
    }

    /**
     * Redirect join submission to access page
     */
    public function join(Request $request)
    {
        return redirect()->route('voter.elections.access');
    }


    /**
     * Show real-time election results
     */
    public function results($code)
    {
        $election = Election::where('code', $code)->first();

        if (!$election) {
            return redirect()->route('voter.elections.access')
                ->withErrors(['code' => 'Election not found.']);
        }

        $voter = session('voter');

        $positions = $election->positions()
            ->with(['candidates' => function ($query) use ($election) {
                $query->withCount(['votes' => function ($q) use ($election) {
                    $q->where('election_id', $election->id);
                }]);
            }])
            ->orderBy('order')
            ->get();

        $partylists = $election->partylists()
            ->with(['candidates' => function ($query) use ($election) {
                $query->withCount(['votes' => function ($q) use ($election) {
                    $q->where('election_id', $election->id);
                }])->with('position');
            }])
            ->get();

        $resultsAnonymized = $election->anonymize_results;

        return view('voter.elections.results', [
            'election' => $election,
            'positions' => $positions,
            'partylists' => $partylists,
            'voter' => $voter,
            'resultsAnonymized' => $resultsAnonymized
        ]);
    }

    /**
     * Get real-time vote counts for an election
     */
    public function getVotes($code)
    {
        $election = Election::where('code', $code)->first();

        if (!$election) {
            return response()->json(['error' => 'Election not found'], 404);
        }

        $candidates = \App\Models\Candidate::where('election_id', $election->id)
            ->withCount(['votes' => function ($q) use ($election) {
                $q->where('election_id', $election->id);
            }])
            ->get()
            ->mapWithKeys(function ($candidate) {
                return [$candidate->id => $candidate->votes_count];
            });

        return response()->json([
            'success' => true,
            'votes' => $candidates,
            'total_votes' => \App\Models\Vote::where('election_id', $election->id)->count()
        ]);
    }

    /**
     * Extract election code from URL
     */
    private function extractCodeFromLink($link)
    {
        preg_match('/\/([A-Z0-9]{6})(?:\/|$)/i', $link, $matches);
        return strtoupper($matches[1] ?? '');
    }

    /**
     * Check how many ballots the voter has submitted
     */
    private function getBallotCount($electionId, $voterId)
    {
        return Vote::where('election_id', $electionId)
            ->where('voter_id', $voterId)
            ->distinct('ballot_id')
            ->count('ballot_id');
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
