<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VoterAuth
{
    public function handle(Request $request, Closure $next)
    {
        $voterGuard = auth()->guard('voter');
        $webGuard = auth()->guard('web');

        // Allow both voters and admins
        if (!$voterGuard->check() && !$webGuard->check()) {
            return redirect()->route('voter.elections.access')
                ->withErrors(['session' => 'Please register or sign in to continue.']);
        }

        // If it's a voter, perform status checks
        if ($voterGuard->check()) {
            $voterModel = $voterGuard->user();

            $isApproved = $voterModel->registration_status === 'approved';
            $isPendingWithAutoApprove = $voterModel->registration_status === 'pending' &&
                                       $voterModel->election &&
                                       $voterModel->election->auto_approve_voters;

            if (!$isApproved && !$isPendingWithAutoApprove) {
                $voterGuard->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $msg = $voterModel->registration_status === 'declined'
                    ? 'Your registration has been declined.'
                    : 'Your registration is pending approval.';
                return redirect()->route('voter.elections.access')
                    ->withErrors(['session' => $msg]);
            }
        }

        $voterModel = $voterGuard->user() ?? $webGuard->user();
        $electionParam = $request->route('election');

        // Verify voter belongs to this election if election is present in route
        if ($electionParam && $voterGuard->check()) {
            $election = $electionParam instanceof \App\Models\Election
                ? $electionParam
                : \App\Models\Election::where('id', $electionParam)->orWhere('code', $electionParam)->first();

            if ($election && $voterModel->election_id !== $election->id) {
                // Redirect them to THEIR election's corresponding page
                $voterElection = $voterModel->election;
                if ($voterElection) {
                    $routeName = $request->route()->getName();
                    if (in_array($routeName, ['voter.elections.results', 'voter.elections.welcome', 'voter.elections.vote'])) {
                        return redirect()->route($routeName, $voterElection->code)
                            ->with('info', 'You have been redirected to the election you are registered for.');
                    }
                }

                return redirect()->route('voter.registration.index', $election->code)
                    ->withErrors(['session' => 'Please register for this election.']);
            }
        }

        return $next($request);
    }
}
