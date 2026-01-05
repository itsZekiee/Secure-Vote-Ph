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

        // Store election in session and redirect to registration
        session(['election_id' => $election->id, 'election_code' => $code]);

        return redirect()->route('voter.registration.index', $election->code);
    }

    /**
     * Step 3: Welcome page with countdown
     */
    public function welcome($code)
    {
        $election = Election::where('code', $code)->first();

        if (!$election) {
            return redirect()->route('voter.elections.access')
                ->withErrors(['code' => 'Election not found.']);
        }

        $voter = session('voter');

        if (!$voter) {
            return redirect()->route('voter.registration.index', $election->code);
        }

        $hasVoted = $this->checkIfVoted($election->id, $voter['id']);

        return view('voter.welcome', [
            'election' => $election,
            'voter' => $voter,
            'hasVoted' => $hasVoted
        ]);
    }


    /**
     * Step 4: Display voting page with positions and candidates
     */
    public function index($code)
    {
        $election = Election::where('code', $code)->first();

        if (!$election) {
            return redirect()->route('voter.elections.access')
                ->withErrors(['code' => 'Election not found.']);
        }

        $voter = session('voter');

        if (!$voter) {
            return redirect()->route('voter.registration.index', $election->code);
        }

        // Rest of existing logic...
        if (Carbon::now()->lt($election->start_date)) {
            return redirect()->route('voter.elections.welcome', $election->code)
                ->withErrors(['election' => 'Election has not started yet.']);
        }

        if (Carbon::now()->gt($election->end_date)) {
            return redirect()->route('voter.elections.welcome', $election->code)
                ->withErrors(['election' => 'Election has already ended.']);
        }

        if ($this->checkIfVoted($election->id, $voter['id'])) {
            return redirect()->route('voter.elections.welcome', $election->code)
                ->with('info', 'You have already cast your vote.');
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
    public function submitVote(Request $request, $code)
    {
        $election = Election::where('code', $code)->first();

        if (!$election) {
            return redirect()->route('voter.elections.access')
                ->withErrors(['code' => 'Election not found.']);
        }

        $request->validate([
            'votes' => 'required|array',
            'votes.*' => 'required|exists:candidates,id',
        ]);

        $voter = session('voter');

        if (!$voter) {
            return redirect()->route('voter.registration.index', $election->code)
                ->withErrors(['error' => 'Please register first to vote.']);
        }

        // Check if already voted
        if ($this->checkIfVoted($election->id, $voter['id'])) {
            return redirect()->route('voter.elections.welcome', $election->code)
                ->withErrors(['error' => 'You have already voted in this election.']);
        }

        // Record votes
        foreach ($request->votes as $positionId => $candidateIds) {
            // Handle both single candidate ID (string/int) and multiple IDs (array)
            $ids = is_array($candidateIds) ? $candidateIds : [$candidateIds];

            foreach ($ids as $candidateId) {
                if (!empty($candidateId)) {
                    Vote::create([
                        'voter_id' => $voter['id'],
                        'election_id' => $election->id,
                        'position_id' => $positionId,
                        'candidate_id' => $candidateId,
                    ]);
                }
            }
        }

        return redirect()->route('voter.elections.welcome', $election->code)
            ->with('success', 'Your vote has been recorded successfully!');
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

        if (!$voter) {
            return redirect()->route('voter.registration.index', $election->code);
        }

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

        return view('live.result', [
            'election' => $election,
            'positions' => $positions,
            'partylists' => $partylists,
            'voter' => $voter
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
     * Check if voter has already voted
     */
    private function checkIfVoted($electionId, $voterId)
    {
        return Vote::where('election_id', $electionId)
            ->where('voter_id', $voterId)
            ->exists();
    }
}
