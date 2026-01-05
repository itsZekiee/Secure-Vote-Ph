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

        // Check for approval status
        $voterModel = \App\Models\Voter::find($voter['id']);
        if (!$voterModel || $voterModel->registration_status !== 'approved') {
            $request->session()->forget('voter');
            $msg = $voterModel && $voterModel->registration_status === 'declined'
                ? 'Your registration has been declined.'
                : 'Your registration is pending approval.';
            return redirect()->route('voter.elections.access')
                ->withErrors(['session' => $msg]);
        }

        // Verify voter belongs to this election
        if ($election && $voter['election_id'] !== $election->id) {
            return redirect()->route('voter.registration.index', $election->code)
                ->withErrors(['session' => 'Please register for this election.']);
        }

        return $next($request);
    }
}
