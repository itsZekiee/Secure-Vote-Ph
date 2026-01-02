<?php

namespace App\Http\Controllers\Voter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VoterElectionController extends Controller
{
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
            $code = $request->input('election_code');
            // TODO: Verify election code and redirect to voting page
        } else {
            $request->validate([
                'election_link' => 'required|url',
            ]);
            $link = $request->input('election_link');
            // TODO: Parse link and redirect to voting page
        }

        // Placeholder - redirect back with error for now
        return back()->withErrors(['election_code' => 'Invalid election code or link.']);
    }
}
