<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Partylist;
use App\Models\Election;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PartylistController extends Controller
{
    public function index()
    {
        $partylists = Partylist::with(['election', 'organization'])
            ->withCount(['candidates'])
            ->orderBy('created_at', 'desc')
            ->get();

        $elections = Election::all();

        return view('main-admin.partylists', compact('partylists', 'elections'));
    }

    public function create()
    {
        $organizations = Organization::orderBy('name')->get();

        return view('main-admin.partylist.partylists-create', compact('organizations'));
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
                $logoPath = $request->file('logo')->store('partylists/logos', 'public');
                $validated['logo'] = $logoPath;
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
                ->with('success', 'Party list created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating partylist: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while creating the party list.',
                    'errors' => ['general' => 'An error occurred while creating the party list.']
                ], 422);
            }

            return back()->withErrors(['general' => 'An error occurred while creating the party list.'])
                ->withInput();
        }
    }

    public function show(Partylist $partylist)
    {
        $partylist->load(['election', 'candidates.position', 'candidates.user']);
        // pass both variables so views expecting `$party` or `$partylist` work
        return view('main-admin.partylist.partylist-view', [
            'partylist' => $partylist,
            'party' => $partylist
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

            if ($request->hasFile('logo')) {
                if ($partylist->logo && \Storage::disk('public')->exists($partylist->logo)) {
                    \Storage::disk('public')->delete($partylist->logo);
                }
                $logoPath = $request->file('logo')->store('partylists/logos', 'public');
                $validated['logo'] = $logoPath;
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
                ->with('success', 'Party list updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating partylist: ' . $e->getMessage());

            return back()->withErrors(['general' => 'An error occurred while updating the party list.'])
                ->withInput();
        }
    }

    public function edit(Partylist $partylist)
    {
        $elections = Election::where('status', 'active')->get();
        // include both `partylist` and `party` for the edit view compatibility
        return view('main-admin.partylist.partylist-edit', [
            'partylist' => $partylist,
            'party' => $partylist,
            'elections' => $elections
        ]);
    }

    public function destroy(Partylist $partylist)
    {
        try {
            DB::beginTransaction();

            if ($partylist->candidates()->count() > 0) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete party list with existing candidates.'
                    ], 422);
                }

                return back()->withErrors(['general' => 'Cannot delete party list with existing candidates.']);
            }

            if ($partylist->logo && \Storage::disk('public')->exists($partylist->logo)) {
                \Storage::disk('public')->delete($partylist->logo);
            }

            $partylist->delete();

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Party list deleted successfully!'
                ]);
            }

            return redirect()->route('admin.partylists.index')
                ->with('success', 'Party list deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting partylist: ' . $e->getMessage());

            return back()->withErrors(['general' => 'An error occurred while deleting the party list.']);
        }
    }

    public function toggleStatus(Partylist $partylist)
    {
        try {
            $partylist->update([
                'status' => $partylist->status === 'active' ? 'inactive' : 'active'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully!',
                'status' => $partylist->status
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

        $partylists = Partylist::with(['election'])
            ->withCount(['candidates'])
            ->when($query, function ($q) use ($query) {
                return $q->where('name', 'like', "%{$query}%")
                    ->orWhere('acronym', 'like', "%{$query}%");
            })
            ->when($status, function ($q) use ($status) {
                return $q->where('status', $status);
            })
            ->when($election_id, function ($q) use ($election_id) {
                return $q->where('election_id', $election_id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['partylists' => $partylists]);
    }

    public function export(Request $request)
    {
        $partylists = Partylist::with(['election'])
            ->withCount(['candidates'])
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'partylists_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($partylists) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID', 'Name', 'Acronym', 'Election', 'Status', 'Candidates', 'Created At'
            ]);

            foreach ($partylists as $partylist) {
                fputcsv($file, [
                    $partylist->id,
                    $partylist->name,
                    $partylist->acronym,
                    $partylist->election->name ?? 'N/A',
                    $partylist->status,
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
            'user_id' => 'required|exists:users,id'
        ]);

        try {
            $existingCandidate = $partylist->election->candidates()
                ->where('user_id', $request->user_id)
                ->first();

            if ($existingCandidate) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is already a candidate in this election.'
                ], 422);
            }

            $partylist->candidates()->create([
                'user_id' => $request->user_id,
                'election_id' => $partylist->election_id,
                'position_id' => $request->position_id ?? null,
                'status' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Member added successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('Error adding partylist member: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while adding the member.'
            ], 500);
        }
    }

    public function removeMember(Partylist $partylist, $userId)
    {
        try {
            $candidate = $partylist->candidates()
                ->where('user_id', $userId)
                ->first();

            if (!$candidate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member not found.'
                ], 404);
            }

            $candidate->delete();

            return response()->json([
                'success' => true,
                'message' => 'Member removed successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('Error removing partylist member: ' . $e->getMessage());

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
        ];

        return response()->json(['statistics' => $stats]);
    }
}
