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
                $totalVotes = $election->voters->whereNotNull('voted_at')->count();

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

        $auditLogs = AuditLog::with('user')->latest()->limit(50)->get();

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

        $activeAuthenticated = DB::table('sessions')
            ->where('last_activity', '>=', $thirtyMinutesAgo)
            ->whereNotNull('user_id')
            ->count();

        // Average time to vote: avg difference between voter.created_at and votes.voted_at (seconds)
        $avgSeconds = DB::table('votes')
            ->join('voters', 'votes.voter_id', '=', 'voters.id')
            ->where('votes.election_id', $election->id)
            ->whereNotNull('votes.voted_at')
            ->avg(DB::raw('TIMESTAMPDIFF(SECOND, voters.created_at, votes.voted_at)'));

        $avgTimeToVote = $avgSeconds ? round($avgSeconds) : 0; // seconds

        // Failed login attempts (ghost registrations): count in last 24 hours and total
        // failed_logins table uses `created_at` timestamp
        $failedLast24 = DB::table('failed_logins')
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->count();

        $failedTotal = DB::table('failed_logins')->count();

        // suspicious IPs: number of distinct IPs with > X failed attempts in last 24h
        $suspiciousIPs = DB::table('failed_logins')
            ->select('ip_address', DB::raw('COUNT(*) as cnt'))
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->groupBy('ip_address')
            ->having('cnt', '>', 5)
            ->count();

        // verification success rate: if elections require verification, compute ratio
        $verificationSuccessRate = 100;
        if ($election->require_verification ?? false) {
            $totalVerificationAttempts = DB::table('voters')->where('election_id', $election->id)->count();
            $verified = DB::table('voters')->where('election_id', $election->id)->where('is_verified', true)->count();
            $verificationSuccessRate = $totalVerificationAttempts > 0 ? round(($verified / $totalVerificationAttempts) * 100, 1) : 100;
        }

        return [
            'votesPerMinute' => [],
            'avgTimeToVote' => $avgTimeToVote, // seconds
            'activeSessions' => [
                'total' => $activeSessionsTotal,
                'authenticated' => $activeAuthenticated,
                'guest' => max(0, $activeSessionsTotal - $activeAuthenticated),
            ],
            'failedLogins' => [
                'last24h' => $failedLast24,
                'total' => $failedTotal,
            ],
            'suspiciousIPs' => $suspiciousIPs,
            'verificationSuccessRate' => $verificationSuccessRate,
            'ghostRegistrations' => $failedLast24,
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
