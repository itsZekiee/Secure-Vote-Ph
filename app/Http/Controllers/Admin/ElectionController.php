<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ElectionController extends Controller
{
    /**
     * Helper method to check if user can manage an election
     */
    private function canUserManageElection(Election $election): bool
    {
        return $election->created_by === auth()->id() || 
               $election->subAdmins()->where('user_id', auth()->id())->exists();
    }
    public function index()
    {
        $elections = Election::where('created_by', auth()->id())
            ->orWhereHas('subAdmins', function($query) {
                $query->where('user_id', auth()->id());
            })
            ->with(['organization'])
            ->withCount(['candidates', 'votes'])
            ->orderBy('created_at', 'desc')
            ->get();

        $organizations = Organization::where('is_active', 1)->get();

        return view('main-admin.elections', compact('elections', 'organizations'));
    }

    public function create()
    {
        $organizations = Organization::where('is_active', 1)->get();
        return view('main-admin.elections.create', compact('organizations'));
    }

    public function edit(Election $election)
    {
        $organizations = Organization::where('is_active', 1)->get();
        return view('main-admin.elections.edit', compact('election', 'organizations'));
    }

    // Store a newly created election
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'organization_id' => 'required|exists:organizations,id',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:draft,active,completed,cancelled',
            'sub_admin_ids' => 'nullable|array',
            'sub_admin_ids.*' => 'exists:users,id'
        ]);

        try {
            DB::beginTransaction();

            $validated['created_by'] = auth()->id();
            $election = Election::create($validated);

            // Sync sub-admin assignments if provided
            if (!empty($validated['sub_admin_ids'])) {
                $election->subAdmins()->sync($validated['sub_admin_ids']);
            }

            DB::commit();

            return redirect()->route('admin.elections.index')
                ->with('success', 'Election created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['general' => 'An error occurred while creating the election'])
                ->withInput();
        }
    }

    // Display the specified election
    public function show(Election $election)
    {
        if (!$this->canUserManageElection($election)) {
            abort(403, 'Unauthorized');
        }

        $election->load(['organization', 'candidates', 'votes']);

        return view('main-admin.elections.show', compact('election'));
    }

    // Update the specified election
    public function update(Request $request, Election $election)
    {
        if ($election->created_by !== auth()->id()) {
            return back()->withErrors(['general' => 'Only the election creator can edit it']);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'organization_id' => 'required|exists:organizations,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:draft,active,completed,cancelled',
            'sub_admin_ids' => 'nullable|array',
            'sub_admin_ids.*' => 'exists:users,id'
        ]);

        try {
            DB::beginTransaction();

            $election->update($validated);

            // Sync sub-admin assignments if provided
            if (!empty($validated['sub_admin_ids'])) {
                $election->subAdmins()->sync($validated['sub_admin_ids']);
            } else {
                $election->subAdmins()->detach();
            }

            DB::commit();

            return redirect()->route('admin.elections.index')
                ->with('success', 'Election updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['general' => 'An error occurred while updating the election'])
                ->withInput();
        }
    }

    //Remove the specified election from storage
    public function destroy(Election $election)
    {
        if ($election->created_by !== auth()->id()) {
            return back()->withErrors(['general' => 'Only the election creator can delete it']);
        }

        try {
            DB::beginTransaction();

            // Check if election has votes
            if ($election->votes()->count() > 0) {
                return back()->withErrors(['general' => 'Cannot delete election with existing votes']);
            }

            $election->delete();

            DB::commit();

            return redirect()->route('admin.elections.index')
                ->with('success', 'Election deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['general' => 'An error occurred while deleting the election']);
        }
    }

    /**
     * Assign sub-admin to election
     */
    public function assignSubAdmin(Request $request, Election $election)
    {
        if ($election->created_by !== auth()->id()) {
            return response()->json(['error' => 'Only the creator can assign sub-admins'], 403);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        try {
            $election->subAdmins()->attach($validated['user_id']);
            return response()->json(['success' => true, 'message' => 'Sub-admin assigned successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to assign sub-admin'], 422);
        }
    }

    /**
     * Remove sub-admin from election
     */
    public function removeSubAdmin(Request $request, Election $election)
    {
        if ($election->created_by !== auth()->id()) {
            return response()->json(['error' => 'Only the creator can remove sub-admins'], 403);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        try {
            $election->subAdmins()->detach($validated['user_id']);
            return response()->json(['success' => true, 'message' => 'Sub-admin removed successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to remove sub-admin'], 422);
        }
    }
    public function candidates(Election $election)
    {
        if (!$this->canUserManageElection($election)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $candidates = $election->candidates()->with(['user', 'partylist'])->get();

        return response()->json(['candidates' => $candidates]);
    }

    // Search Elections
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $status = $request->get('status', '');
        $organization_id = $request->get('organization_id', '');

        $elections = Election::where('created_by', auth()->id())
            ->orWhereHas('subAdmins', function($q) {
                $q->where('user_id', auth()->id());
            })
            ->with(['organization'])
            ->withCount(['candidates', 'votes'])
            ->when($query, function ($q) use ($query) {
                return $q->where('title', 'like', "%{$query}%");
            })
            ->when($status, function ($q) use ($status) {
                return $q->where('status', $status);
            })
            ->when($organization_id, function ($q) use ($organization_id) {
                return $q->where('organization_id', $organization_id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['elections' => $elections]);
    }

    // Export elections data
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');

        $elections = Election::where('created_by', auth()->id())
            ->orWhereHas('subAdmins', function($q) {
                $q->where('user_id', auth()->id());
            })
            ->with(['organization'])
            ->withCount(['candidates', 'votes'])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($format === 'json') {
            return response()->json(['elections' => $elections]);
        }

        // CSV export logic
        $filename = 'elections_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($elections) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID', 'Title', 'Organization', 'Status', 'Start Date', 'End Date', 'Candidates', 'Votes', 'Created At'
            ]);

            foreach ($elections as $election) {
                fputcsv($file, [
                    $election->id,
                    $election->title,
                    $election->organization->name,
                    $election->status,
                    $election->start_date,
                    $election->end_date,
                    $election->candidates_count,
                    $election->votes_count,
                    $election->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
