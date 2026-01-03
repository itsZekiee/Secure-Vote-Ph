<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Election;

class DashboardController extends Controller
{
    public function index()
    {
        $elections = Election::where('created_by', auth()->id())
            ->orWhereHas('subAdmins', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->with(['organization', 'voters'])
            ->get()
            ->map(function ($election) {
                $registeredVoters = $election->voters->count();
                $totalVotes = $election->voters->whereNotNull('voted_at')->count();

                return [
                    'id' => $election->id,
                    'name' => $election->title,
                    'organization' => $election->organization?->name ?? 'N/A',
                    'code' => $election->code ?? 'N/A',
                    'link' => $election->access_link ?? url("/voter/register/{$election->code}"),
                    'createdDate' => $election->created_at->toISOString(),
                    'status' => $this->getElectionStatus($election),
                    'totalVotes' => $totalVotes,
                    'registeredVoters' => $registeredVoters,
                    'turnoutRate' => $registeredVoters > 0
                        ? round(($totalVotes / $registeredVoters) * 100, 1)
                        : 0,
                    'realtimeMetrics' => [
                        'votesPerMinute' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
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
