<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VoterAuth
{
    public function handle(Request $request, Closure $next)
    {
        $voter = session('voter');
        $election = $request->route('election');

        if (!$voter) {
            return redirect()->route('voter.elections.access')
                ->withErrors(['session' => 'Please register or sign in to continue.']);
        }

        // Verify voter belongs to this election
        if ($election && $voter['election_id'] !== $election->id) {
            return redirect()->route('voter.registration.index', $election->code)
                ->withErrors(['session' => 'Please register for this election.']);
        }

        return $next($request);
    }
}
