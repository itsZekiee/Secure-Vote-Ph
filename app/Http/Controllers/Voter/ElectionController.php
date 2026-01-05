<?php

namespace App\Http\Controllers\Voter;

use App\Http\Controllers\Controller;
use App\Models\Election;

class ElectionController extends Controller
{
    public function welcome($code)
    {
        $election = Election::where('code', $code)->firstOrFail();
        return view('voter.welcome', compact('election'));
    }

    public function verify(Request $request)
    {
        $request->validate([
            'input_type' => 'required|in:code,link',
            'election_code' => 'required_if:input_type,code|nullable|string|size:6',
            'election_link' => 'required_if:input_type,link|nullable|url',
        ]);

        $code = $request->input_type === 'code'
            ? strtoupper($request->election_code)
            : $this->extractCodeFromLink($request->election_link);

        $election = Election::where('code', $code)->first();

        if (!$election) {
            return back()->withErrors(['election_code' => 'Invalid election code or link.']);
        }

        // Store election data in session
        session([
            'election_id' => $election->id,
            'election_code' => $election->code,
            'election_title' => $election->title,
            'election_description' => $election->description,
        ]);

        return redirect()->route('voter.register.index');
    }

    private function extractCodeFromLink($link)
    {
        // Extract code from URL like https://securevote.ph/vote/XXXXXX
        $parts = explode('/', rtrim($link, '/'));
        return strtoupper(end($parts));
    }
}
