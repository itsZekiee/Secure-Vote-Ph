<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;

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
                    'realtimeMetrics' => [
                        'votesPerMinute' => array_fill(0, 10, 0),
                        'avgTimeToVote' => 0,
                        'activeSessions' => 0,
                        'failedLogins' => 0,
                        'suspiciousIPs' => 0,
                        'verificationSuccessRate' => 100,
                        'ghostRegistrations' => 0,
                    ],
                    'demographicData' => [
                        'ageGroups' => [],
                        'regions' => [],
                        'submissionMethods' => [],
                    ],
                ];
            });

        return view('main-admin.dashboard', compact('elections'));
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
