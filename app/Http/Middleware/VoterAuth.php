<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VoterAuth
{
    public function handle(Request $request, Closure $next)
    {
        $voter = session('voter');
        $code = $request->route('code');

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

        // Verify voter belongs to this election if code is present in route
        if ($code) {
            $election = \App\Models\Election::where('code', $code)->first();
            if ($election && $voterModel->election_id !== $election->id) {
                // If the voter is trying to access another election's results/vote page,
                // redirect them to THEIR election's corresponding page instead of just registration.
                $voterElection = \App\Models\Election::find($voterModel->election_id);
                if ($voterElection) {
                    $routeName = $request->route()->getName();
                    // Map common routes to voter's election
                    if (in_array($routeName, ['voter.elections.results', 'voter.elections.welcome', 'voter.elections.vote'])) {
                        return redirect()->route($routeName, $voterElection->code)
                            ->with('info', 'You have been redirected to the election you are registered for.');
                    }
                }

                return redirect()->route('voter.registration.index', $code)
                    ->withErrors(['session' => 'Please register for this election.']);
            }
        }

        return $next($request);
    }
}
