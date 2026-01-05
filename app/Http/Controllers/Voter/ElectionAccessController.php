<?php

namespace App\Http\Controllers\Voter;

use App\Http\Controllers\Controller;
use App\Models\Election;
use Illuminate\Http\Request;

class ElectionAccessController extends Controller
{
    public function show()
    {
        return view('voter.elections.access');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'input_type' => 'required|in:code,link',
            'election_code' => 'required_if:input_type,code|nullable|string|size:6',
            'election_link' => 'required_if:input_type,link|nullable|url',
        ]);

        $code = null;

        if ($request->input_type === 'code') {
            $code = strtoupper($request->election_code);
        } else {
            $link = $request->election_link;
            if (preg_match('/\/voter\/register\/([A-Za-z0-9]+)/', $link, $matches)) {
                $code = strtoupper($matches[1]);
            } elseif (preg_match('/\/([A-Za-z0-9]{6,8})$/', $link, $matches)) {
                $code = strtoupper($matches[1]);
            }
        }

        if (!$code) {
            return back()->withErrors(['election_code' => 'Invalid election link format.'])->withInput();
        }

        $election = Election::where('code', $code)->first();

        if (!$election) {
            return back()->withErrors(['election_code' => 'Invalid election code. Please check and try again.'])->withInput();
        }

        $now = now();

        if ($election->end_date && $now->gt($election->end_date)) {
            return back()->withErrors(['election_code' => 'This election has already ended.'])->withInput();
        }

        if ($election->registration_deadline && $now->gt($election->registration_deadline)) {
            return back()->withErrors(['election_code' => 'Registration deadline has passed for this election.'])->withInput();
        }

        session(['election_id' => $election->id, 'election_code' => $code]);

        return redirect()->route('voter.register', ['code' => $code]);
    }

    public function register(string $code)
    {
        $election = Election::where('code', strtoupper($code))->first();

        if (!$election) {
            return redirect()->route('voter.elections.access')
                ->withErrors(['election_code' => 'Invalid election code.']);
        }

        return view('voter.registration.index', compact('election'));
    }

    public function welcome($code)
    {
        $election = Election::where('code', strtoupper($code))
            ->with(['candidates' => function($query) {
                $query->withCount('votes');
            }, 'candidates.position'])
            ->firstOrFail();

        return view('voter.welcome', compact('election'));
    }

    public function welcomeFromSession()
    {
        $electionId = session('election_id');

        if (!$electionId) {
            return redirect()->route('voter.elections.access')
                ->withErrors(['election_code' => 'Please enter an election code first.']);
        }

        $election = Election::with(['candidates' => function($query) {
            $query->withCount('votes');
        }, 'candidates.position'])->findOrFail($electionId);

        return view('voter.welcome', compact('election'));
    }
}
