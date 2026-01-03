<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Election;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $elections = Election::whereNull('deleted_at')
            ->with(['votes' => function ($query) {
                $query->select('election_id');
            }])
            ->get()
            ->map(function ($election) {
                $totalVotes = $election->votes->count();
                $registeredVoters = $election->registered_voters ?? 0;
                $turnoutRate = $registeredVoters > 0 ? ($totalVotes / $registeredVoters) * 100 : 0;

                return [
                    'id' => $election->id,
                    'name' => $election->election_name ?? $election->title ?? 'Unnamed Election',
                    'organization' => $election->organization_name ?? $election->organization ?? 'N/A',
                    'createdDate' => $election->created_at,
                    'status' => $election->status ?? 'scheduled',
                    'totalVotes' => $totalVotes,
                    'registeredVoters' => $registeredVoters,
                    'turnoutRate' => round($turnoutRate, 1),
                    'realtimeMetrics' => [
                        'votesPerMinute' => array_fill(0, 10, 0),
                        'avgTimeToVote' => 0,
                        'activeSessions' => 0,
                        'failedLogins' => 0,
                        'suspiciousIPs' => 0,
                        'verificationSuccessRate' => 0,
                        'ghostRegistrations' => 0
                    ],
                    'demographicData' => [
                        'ageGroups' => [],
                        'regions' => [],
                        'submissionMethods' => []
                    ]
                ];
            });

        return view('main-admin.dashboard', compact('elections'));
    }
}
