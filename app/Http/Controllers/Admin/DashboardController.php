<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\AuditLog;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $elections = Election::where('created_by', $userId)
            ->orWhereHas('subAdmins', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            // still eager-load organization and voters if relation exists
            ->with(['organization', 'voters'])
            ->get()
            ->map(function ($election) {
                $registeredVoters = $election->voters->count();
                $totalVotes = $election->votes()->distinct('voter_id')->count('voter_id');

                // Prefer the loaded relation, otherwise try to lookup by organization_id,
                // otherwise use any organization_name column stored on the election.
                $org = $election->organization
                    ?? (isset($election->organization_id) ? Organization::find($election->organization_id) : null);

                $orgName = optional($org)->name
                    ?? optional($org)->title
                    ?? optional($org)->org_name
                    ?? ($election->organization_name ?? null);

                return [
                    'id' => $election->id,
                    'name' => $election->title,
                    'organization' => $org ? ['name' => $orgName] : null,
                    'organization_name' => $orgName ?? 'N/A',
                    'code' => $election->code ?? 'N/A',
                    'link' => $election->access_link ?? url("/voter/register/{$election->code}"),
                    'createdDate' => optional($election->created_at)->toIso8601String(),
                    'status' => $this->getElectionStatus($election),
                    'totalVotes' => $totalVotes,
                    'registeredVoters' => $registeredVoters,
                    'turnoutRate' => $registeredVoters > 0
                        ? round(($totalVotes / $registeredVoters) * 100, 1)
                        : 0,
                    'realtimeMetrics' => $this->computeRealtimeMetrics($election),
                    'demographicData' => [
                        'ageGroups' => [],
                        'regions' => [],
                        'submissionMethods' => [],
                    ],
                ];
            });

        $auditLogs = [];
        if (auth()->user()->hasRole('super-admin')) {
            $auditLogs = AuditLog::with('user')->latest()->limit(50)->get();
        }

        $view = 'main-admin.dashboard';
        if (auth()->user()->hasRole('admin') && !auth()->user()->hasRole('super-admin')) {
            $view = 'admin.dashboard';
        }

        return view($view, compact('elections', 'auditLogs'));
    }

    /**
     * Compute realtime metrics for an election.
     */
    private function computeRealtimeMetrics(Election $election): array
    {
        // Active sessions (last 30 minutes)
        $thirtyMinutesAgo = Carbon::now()->subMinutes(30)->timestamp;
        $activeSessionsTotal = DB::table('sessions')
            ->where('last_activity', '>=', $thirtyMinutesAgo)
            ->count();

        // Average time to vote: avg difference between voter.created_at and votes.voted_at (seconds)
        $avgSeconds = DB::table('votes')
            ->join('voters', 'votes.voter_id', '=', 'voters.id')
            ->where('votes.election_id', $election->id)
            ->whereNotNull('votes.voted_at')
            ->avg(DB::raw('TIMESTAMPDIFF(SECOND, voters.created_at, votes.voted_at)'));

        $avgTimeToVote = $avgSeconds ? round($avgSeconds / 60, 1) : 0; // minutes

        // Failed login attempts: count total for this election if linked, or globally if not
        $failedLoginsCount = DB::table('failed_logins')
            ->where(function($query) use ($election) {
                $query->where('election_id', $election->id)
                      ->orWhereNull('election_id');
            })
            ->count();

        // suspicious IPs: number of distinct IPs with > 5 failed attempts in last 24h
        $suspiciousIPs = DB::table('failed_logins')
            ->select('ip_address', DB::raw('COUNT(*) as cnt'))
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->groupBy('ip_address')
            ->having('cnt', '>', 5)
            ->get()
            ->count();

        // verification success rate
        $totalVoters = DB::table('voters')->where('election_id', $election->id)->count();
        $verifiedVoters = DB::table('voters')->where('election_id', $election->id)->where('registration_status', 'approved')->count();
        $verificationSuccessRate = $totalVoters > 0 ? round(($verifiedVoters / $totalVoters) * 100, 1) : 0;

        // Ghost Registrations (Flagged accounts)
        // Let's define ghost registrations as voters who are not verified after some time or flagged
        $ghostRegistrations = DB::table('voters')
            ->where('election_id', $election->id)
            ->where('registration_status', 'pending')
            ->where('created_at', '<', Carbon::now()->subDays(2))
            ->count();

        // Votes trend: Votes per minute for the last 10 minutes
        $votesPerMinute = [];
        for ($i = 9; $i >= 0; $i--) {
            $minute = Carbon::now()->subMinutes($i);
            $count = DB::table('votes')
                ->where('election_id', $election->id)
                ->whereBetween('voted_at', [
                    $minute->copy()->startOfMinute(),
                    $minute->copy()->endOfMinute()
                ])
                ->count();
            $votesPerMinute[] = $count;
        }

        return [
            'votesPerMinute' => $votesPerMinute,
            'avgTimeToVote' => $avgTimeToVote,
            'activeSessions' => $activeSessionsTotal,
            'failedLogins' => $failedLoginsCount,
            'suspiciousIPs' => $suspiciousIPs,
            'verificationSuccessRate' => $verificationSuccessRate,
            'ghostRegistrations' => $ghostRegistrations,
        ];
    }

    private function getElectionStatus(Election $election): string
    {
        $now = now();

        if ($election->end_date && $now->gt($election->end_date)) {
            return 'completed';
        }

        if ($election->start_date && $now->lt($election->start_date)) {
            return 'scheduled';
        }

        return 'active';
    }
}
