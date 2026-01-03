<?php

namespace App\Http\Controllers\Voter;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Models\Vote;
use Illuminate\Http\Request;

class VoterElectionController extends Controller
{
    /**
     * Display list of elections for voter
     */
    public function index()
    {
        $election = null;
        $positions = collect();

        if (session('election_id')) {
            $election = Election::with(['positions.candidates.partylist'])->find(session('election_id'));
            $positions = $election ? $election->positions : collect();
        }

        return view('voter.elections.index', compact('election', 'positions'));
    }

    /**
     * Show a specific election for voting
     */
    public function show(Election $election)
    {
        $election->load(['candidates.position', 'candidates.partylist']);

        $positions = $election->candidates
            ->groupBy('position_id')
            ->map(function ($candidates) {
                $position = $candidates->first()->position;
                if ($position) {
                    $position->candidates = $candidates;
                }
                return $position;
            })
            ->filter()
            ->values();

        return view('voter.elections.index', compact('election', 'positions'));
    }



    /**
     * Process the vote submission
     */
    public function vote(Request $request, Election $election)
    {
        $request->validate([
            'votes' => 'required|array',
            'votes.*' => 'required|exists:candidates,id',
        ]);

        $voterId = session('voter_id');

        if (!$voterId) {
            return back()->withErrors(['error' => 'Please register first to vote.']);
        }

        // Check if already voted
        $existingVote = Vote::where('voter_id', $voterId)
            ->where('election_id', $election->id)
            ->first();

        if ($existingVote) {
            return back()->withErrors(['error' => 'You have already voted in this election.']);
        }

        // Record votes
        foreach ($request->votes as $positionId => $candidateId) {
            Vote::create([
                'voter_id' => $voterId,
                'election_id' => $election->id,
                'position_id' => $positionId,
                'candidate_id' => $candidateId,
            ]);
        }

        return redirect()->route('voter.elections.confirmation', $election->id)
            ->with('success', 'Your vote has been recorded successfully!');
    }

    /**
     * Show vote confirmation page
     */
    public function confirmation(Election $election)
    {
        return view('voter.elections.confirmation', compact('election'));
    }

    /**
     * Show voting history
     */
    public function history()
    {
        $voterId = session('voter_id');
        $votes = collect();

        if ($voterId) {
            $votes = Vote::with(['election', 'candidate', 'position'])
                ->where('voter_id', $voterId)
                ->latest()
                ->get()
                ->groupBy('election_id');
        }

        return view('voter.history.index', compact('votes'));
    }

    /**
     * Show the join election form
     */
    public function showJoinForm()
    {
        return view('voter.elections.access');
    }

    /**
     * Process the election code or link
     */
    public function join(Request $request)
    {
        $inputType = $request->input('input_type');

        if ($inputType === 'code') {
            $request->validate([
                'election_code' => 'required|string|size:6',
            ]);
            $code = strtoupper($request->input('election_code'));
        } else {
            $request->validate([
                'election_link' => 'required|url',
            ]);
            $link = $request->input('election_link');
            // Extract code from link
            preg_match('/\/register\/([A-Z0-9]{6})/', $link, $matches);
            $code = $matches[1] ?? null;
        }

        if (!$code) {
            return back()->withErrors(['election_code' => 'Invalid election code or link.']);
        }

        $election = Election::where('code', $code)->first();

        if (!$election) {
            return back()->withErrors(['election_code' => 'Election not found.']);
        }

        session(['election_id' => $election->id, 'election_code' => $code]);

        return redirect()->route('voter.register', ['code' => $code]);
    }

    /**
     * Register voter for election
     */
    public function register(string $code)
    {
        $election = Election::where('code', strtoupper($code))->first();

        if (!$election) {
            return redirect()->route('voter.elections.access')
                ->withErrors(['election_code' => 'Invalid election code.']);
        }

        return view('voter.registration.index', compact('election'));
    }

    /**
     * Show voter profile
     */
    public function profile()
    {
        $voterId = session('voter_id');
        $voter = null;

        if ($voterId) {
            $voter = \App\Models\Voter::find($voterId);
        }

        return view('voter.profile.index', compact('voter'));
    }

}
