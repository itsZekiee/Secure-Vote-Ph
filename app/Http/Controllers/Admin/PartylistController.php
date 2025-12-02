<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Partylist;
use App\Models\Election;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class PartylistController extends Controller
{
    public function index()
    {
        $partylists = Partylist::with(['election', 'organization'])
            ->withCount(['candidates'])
            ->orderBy('created_at', 'desc')
            ->get();

        $elections = Election::all();
        $organizations = Organization::where('is_active', 1)->orderBy('name')->get();

        return view('main-admin.partylists', compact('partylists', 'elections', 'organizations'));
    }

    public function create()
    {
        // organizations: use 'status' if present, otherwise fall back to 'is_active' if present
        $orgQuery = Organization::query();
        if (Schema::hasColumn('organizations', 'status')) {
            $orgQuery->where('status', 'active');
        } elseif (Schema::hasColumn('organizations', 'is_active')) {
            $orgQuery->where('is_active', 1);
        }
        $organizations = $orgQuery->orderBy('name')->get();

        // elections: only order by a column that exists
        $electionQuery = Election::query();
        if (Schema::hasColumn('elections', 'status')) {
            $electionQuery->where('status', 'active');
        }

        if (Schema::hasColumn('elections', 'name')) {
            $electionQuery->orderBy('name');
        } elseif (Schema::hasColumn('elections', 'title')) {
            $electionQuery->orderBy('title');
        } else {
            $electionQuery->orderBy('created_at', 'desc');
        }

        $elections = $electionQuery->get();

        return view('main-admin.partylist.partylists-create', compact('organizations', 'elections'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:partylists,name',
            'acronym' => 'nullable|string|max:10|unique:partylists,acronym',
            'description' => 'nullable|string|max:1000',
            'platform' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-F]{6}$/i',
            'organization_id' => 'required|exists:organizations,id',
            'status' => 'required|in:active,pending,inactive'
        ]);

        try {
            DB::beginTransaction();

            if ($request->hasFile('logo')) {
                $logoName = time() . '_' . $request->file('logo')->getClientOriginalName();
                $request->file('logo')->storeAs('public/partylists', $logoName);
                $validated['logo'] = $logoName;
            }

            $partylist = Partylist::create($validated);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Party list created successfully!',
                    'partylist' => $partylist->load('organization')
                ]);
            }

            return redirect()->route('admin.partylists.index')
                ->with('success', 'Party list created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating partylist: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while creating the party list.',
                    'errors' => ['general' => $e->getMessage()]
                ], 422);
            }

            return back()->withErrors(['general' => 'An error occurred while creating the party list.'])
                ->withInput();
        }
    }

    public function show(Partylist $partylist)
    {
        $partylist->load(['election', 'organization', 'candidates.position', 'candidates.user']);

        return view('main-admin.partylist.partylist-view', [
            'partylist' => $partylist,
            'party' => $partylist
        ]);
    }

    public function edit(Partylist $partylist)
    {
        $orgQuery = Organization::query();
        if (Schema::hasColumn('organizations', 'status')) {
            $orgQuery->where('status', 'active');
        } elseif (Schema::hasColumn('organizations', 'is_active')) {
            $orgQuery->where('is_active', 1);
        }
        $organizations = $orgQuery->orderBy('name')->get();

        $selectionQuery = Election::query();
        if (Schema::hasColumn('elections', 'status')) {
            $selectionQuery->where('status', 'active');
        }

        if (Schema::hasColumn('elections', 'name')) {
            $selectionQuery->orderBy('name');
        } elseif (Schema::hasColumn('elections', 'title')) {
            $selectionQuery->orderBy('title');
        } else {
            $selectionQuery->orderBy('created_at', 'desc');
        }

        $selections = $selectionQuery->get();

        $partylist->load('organization');

        return view('main-admin.partylist.partylist-edit', [
            'partylist' => $partylist,
            'party' => $partylist,
            'organizations' => $organizations,
            'elections' => $selections
        ]);
    }

    public function update(Request $request, Partylist $partylist)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:partylists,name,' . $partylist->id,
            'acronym' => 'nullable|string|max:10|unique:partylists,acronym,' . $partylist->id,
            'description' => 'nullable|string|max:1000',
            'platform' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-F]{6}$/i',
            'organization_id' => 'required|exists:organizations,id',
            'status' => 'required|in:active,pending,inactive'
        ]);

        try {
            DB::beginTransaction();

            // Handle logo upload
            if ($request->hasFile('logo')) {
                // Delete old logo if exists
                if ($partylist->logo && Storage::exists('public/partylists/' . $partylist->logo)) {
                    Storage::delete('public/partylists/' . $partylist->logo);
                }

                $logoName = time() . '_' . $request->file('logo')->getClientOriginalName();
                $request->file('logo')->storeAs('public/partylists', $logoName);
                $validated['logo'] = $logoName;
            }

            $partylist->update($validated);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Party list updated successfully!',
                    'partylist' => $partylist->load('organization')
                ]);
            }

            return redirect()->route('admin.partylists.index')
                ->with('success', 'Party list updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating partylist: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the party list.',
                    'errors' => ['general' => $e->getMessage()]
                ], 422);
            }

            return back()->withErrors(['general' => 'An error occurred while updating the party list.'])
                ->withInput();
        }
    }

    public function destroy(Partylist $partylist)
    {
        try {
            DB::beginTransaction();

            // Check if partylist has candidates
            if ($partylist->candidates()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete party list with existing candidates.'
                ], 422);
            }

            // Delete logo file if exists
            if ($partylist->logo && Storage::exists('public/partylists/' . $partylist->logo)) {
                Storage::delete('public/partylists/' . $partylist->logo);
            }

            $partylist->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Party list deleted successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting partylist: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the party list.'
            ], 500);
        }
    }

    public function toggleStatus(Partylist $partylist)
    {
        try {
            $newStatus = $partylist->status === 'active' ? 'inactive' : 'active';
            $partylist->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => 'Party list status updated successfully!',
                'status' => $newStatus
            ]);

        } catch (\Exception $e) {
            Log::error('Error toggling partylist status: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the status.'
            ], 500);
        }
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $status = $request->get('status', '');
        $election_id = $request->get('election_id', '');
        $organization_id = $request->get('organization_id', '');

        $partylists = Partylist::with(['election', 'organization'])
            ->withCount(['candidates'])
            ->when($query, function ($q) use ($query) {
                return $q->where(function ($subQuery) use ($query) {
                    $subQuery->where('name', 'LIKE', "%{$query}%")
                        ->orWhere('acronym', 'LIKE', "%{$query}%")
                        ->orWhere('description', 'LIKE', "%{$query}%");
                });
            })
            ->when($status, function ($q) use ($status) {
                return $q->where('status', $status);
            })
            ->when($election_id, function ($q) use ($election_id) {
                return $q->where('election_id', $election_id);
            })
            ->when($organization_id, function ($q) use ($organization_id) {
                return $q->where('organization_id', $organization_id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['partylists' => $partylists]);
    }

    public function export(Request $request)
    {
        $organization_id = $request->get('organization_id', '');
        $status = $request->get('status', '');

        $partylists = Partylist::with(['election', 'organization'])
            ->withCount(['candidates'])
            ->when($organization_id, function ($q) use ($organization_id) {
                return $q->where('organization_id', $organization_id);
            })
            ->when($status, function ($q) use ($status) {
                return $q->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'partylists_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function () use ($partylists) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID',
                'Name',
                'Acronym',
                'Organization',
                'Description',
                'Platform',
                'Color',
                'Status',
                'Candidates Count',
                'Created At'
            ]);

            foreach ($partylists as $partylist) {
                fputcsv($file, [
                    $partylist->id,
                    $partylist->name,
                    $partylist->acronym,
                    $partylist->organization ? $partylist->organization->name : 'N/A',
                    $partylist->description,
                    $partylist->platform,
                    $partylist->color,
                    ucfirst($partylist->status),
                    $partylist->candidates_count,
                    $partylist->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function members(Partylist $partylist)
    {
        $members = $partylist->candidates()->with('user')->get();

        return view('main-admin.partylists-members', [
            'partylist' => $partylist,
            'party' => $partylist,
            'members' => $members
        ]);
    }

    public function addMember(Request $request, Partylist $partylist)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'position_id' => 'required|exists:positions,id'
        ]);

        try {
            DB::beginTransaction();

            // Check if user is already a candidate in this partylist
            $existingCandidate = $partylist->candidates()
                ->where('user_id', $request->user_id)
                ->first();

            if ($existingCandidate) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is already a member of this party list.'
                ], 422);
            }

            $partylist->candidates()->create([
                'user_id' => $request->user_id,
                'position_id' => $request->position_id,
                'status' => 'active'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Member added successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding member to partylist: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while adding the member.'
            ], 500);
        }
    }

    public function removeMember(Partylist $partylist, $userId)
    {
        try {
            DB::beginTransaction();

            $candidate = $partylist->candidates()
                ->where('user_id', $userId)
                ->first();

            if (!$candidate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member not found in this party list.'
                ], 404);
            }

            $candidate->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Member removed successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error removing member from partylist: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while removing the member.'
            ], 500);
        }
    }

    public function candidates(Partylist $partylist)
    {
        $candidates = $partylist->candidates()
            ->with(['user', 'position'])
            ->withCount(['votes'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('main-admin.partylists-candidates', [
            'partylist' => $partylist,
            'party' => $partylist,
            'candidates' => $candidates
        ]);
    }

    public function statistics(Partylist $partylist)
    {
        $stats = [
            'total_candidates' => $partylist->candidates()->count(),
            'active_candidates' => $partylist->candidates()->where('status', 'active')->count(),
            'pending_candidates' => $partylist->candidates()->where('status', 'pending')->count(),
            'total_votes' => $partylist->candidates()->withCount('votes')->get()->sum('votes_count'),
            'organization' => $partylist->organization ? $partylist->organization->name : null,
        ];

        return response()->json(['statistics' => $stats]);
    }

    /**
     * Get partylists by organization
     */
    public function byOrganization(Organization $organization)
    {
        $partylists = $organization->partylists()
            ->with(['candidates.user', 'candidates.position'])
            ->withCount(['candidates'])
            ->orderBy('name')
            ->get();

        return view('main-admin.organization-partylists', [
            'organization' => $organization,
            'partylists' => $partylists
        ]);
    }

    /**
     * API endpoint to get partylists for a specific organization
     */
    public function getByOrganization(Request $request, $organizationId)
    {
        $partylists = Partylist::where('organization_id', $organizationId)
            ->with(['candidates.user'])
            ->withCount(['candidates'])
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'partylists' => $partylists
        ]);
    }
}
