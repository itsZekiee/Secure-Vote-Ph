<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VoterAuth
{
    public function handle(Request $request, Closure $next)
    {
        $voter = session('voter');
        $electionParam = $request->route('election');

        if (!$voter) {
            return redirect()->route('voter.elections.access')
                ->withErrors(['session' => 'Please register or sign in to continue.']);
        }

        // Check for approval status
        $voterModel = \App\Models\Voter::with('election')->find($voter['id']);

        if (!$voterModel) {
            $request->session()->forget('voter');
            return redirect()->route('voter.elections.access')
                ->withErrors(['session' => 'Voter not found.']);
        }

        $isApproved = $voterModel->registration_status === 'approved';
        $isPendingWithAutoApprove = $voterModel->registration_status === 'pending' &&
                                   $voterModel->election &&
                                   $voterModel->election->auto_approve_voters;

        if (!$isApproved && !$isPendingWithAutoApprove) {
            $request->session()->forget('voter');
            $msg = $voterModel->registration_status === 'declined'
                ? 'Your registration has been declined.'
                : 'Your registration is pending approval.';
            return redirect()->route('voter.elections.access')
                ->withErrors(['session' => $msg]);
        }

        // Verify voter belongs to this election if election is present in route
        if ($electionParam) {
            $election = $electionParam instanceof \App\Models\Election
                ? $electionParam
                : \App\Models\Election::where('id', $electionParam)->orWhere('code', $electionParam)->first();

            if ($election && $voterModel->election_id !== $election->id) {
                // If the voter is trying to access another election's results/vote page,
                // redirect them to THEIR election's corresponding page instead of just registration.
                $voterElection = $voterModel->election;
                if ($voterElection) {
                    $routeName = $request->route()->getName();
                    // Map common routes to voter's election
                    if (in_array($routeName, ['voter.elections.results', 'voter.elections.welcome', 'voter.elections.vote'])) {
                        return redirect()->route($routeName, $voterElection->id)
                            ->with('info', 'You have been redirected to the election you are registered for.');
                    }
                }

                return redirect()->route('voter.registration.index', $election->id)
                    ->withErrors(['session' => 'Please register for this election.']);
            }
        }

        return $next($request);
    }
}
