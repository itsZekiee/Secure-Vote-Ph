<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubAdminDashboardController extends Controller
{
    /**
     * Show the sub-admin dashboard with assigned elections
     */
    public function index()
    {
        $user = auth()->user();

        // Get elections assigned to this sub-admin (not created by them)
        $recentForms = $user->assignedElections()
            ->with(['organization', 'creator'])
            ->withCount(['candidates', 'votes'])
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        // Get stats for assigned elections
        $stats = [
            'total_assigned_elections' => $user->assignedElections()->count(),
            'active_elections' => $user->assignedElections()->where('status', 'active')->count(),
            'draft_elections' => $user->assignedElections()->where('status', 'draft')->count(),
            'completed_elections' => $user->assignedElections()->where('status', 'completed')->count(),
            'total_candidates' => $user->assignedElections()
                ->with('candidates')
                ->get()
                ->sum(function ($election) {
                    return $election->candidates()->count();
                }),
            'total_votes' => $user->assignedElections()
                ->with('votes')
                ->get()
                ->sum(function ($election) {
                    return $election->votes()->count();
                }),
        ];

        // mark can_edit on each returned election (sub-admins may edit assigned elections)
        $recentForms->getCollection()->transform(function ($election) use ($user) {
            $election->can_edit = $election->created_by === $user->id || $election->subAdmins()->where('user_id', $user->id)->exists();
            return $election;
        });

        return view('main-admin.sub-admin-dashboard', compact('recentForms', 'stats'));
    }

    /**
     * Get election data for a sub-admin
     */
    public function getElectionData(Request $request)
    {
        $user = auth()->user();

        // Verify user is assigned to this election
        $election = $user->assignedElections()
            ->with([
                'positions.candidates.partylist',
                'positions.candidates.votes',
                'organization',
                'creator',
                'subAdmins'
            ])
            ->find($request->route('election'));

        if (!$election) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Determine if the current user can edit this election (creator or assigned sub-admin)
        $canEdit = $election->created_by === $user->id || $election->subAdmins()->where('user_id', $user->id)->exists();

        $positions = $election->positions()->with(['candidates.partylist', 'candidates.votes'])->get();

        // Add can_edit flags to positions and candidates
        $positions->transform(function ($position) use ($canEdit) {
            $position->can_edit = $canEdit;
            $position->candidates->transform(function ($candidate) use ($canEdit) {
                $candidate->can_edit = $canEdit || ($candidate->created_by === auth()->id());
                return $candidate;
            });
            return $position;
        });

        return response()->json([
            'election' => $election->makeHidden(['positions']),
            'positions' => $positions,
            'stats' => [
                'total_positions' => $election->positions()->count(),
                'total_candidates' => $election->candidates()->count(),
                'total_votes' => $election->votes()->count(),
                'active_sub_admins' => $election->subAdmins()->count(),
            ],
            'can_edit' => $canEdit,
        ]);
    }

    /**
     * Get assigned elections as JSON
     */
    public function getAssignedElections()
    {
        $elections = auth()->user()->assignedElections()
            ->with(['organization', 'creator'])
            ->withCount(['candidates', 'votes'])
            ->orderBy('updated_at', 'desc')
            ->get();

        $user = auth()->user();
        $elections->transform(function ($election) use ($user) {
            $election->can_edit = $election->created_by === $user->id || $election->subAdmins()->where('user_id', $user->id)->exists();
            return $election;
        });

        return response()->json(['elections' => $elections]);
    }
}
