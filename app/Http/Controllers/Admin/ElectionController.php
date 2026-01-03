<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ElectionController extends Controller
{
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

        $organizations = Organization::where('created_by', auth()->id())->get();

        return view('main-admin.elections', compact('elections', 'organizations'));
    }

    public function create()
    {
        $organizations = Organization::where('created_by', auth()->id())->get();
        return view('main-admin.elections.create', compact('organizations'));
    }

    public function edit(Election $election)
    {
        $organizations = Organization::where('created_by', auth()->id())->get();
        return view('main-admin.elections.edit', compact('election', 'organizations'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'organization_id' => 'required|exists:organizations,id',
                'voting_start' => 'required|date',
                'voting_end' => 'required|date|after:voting_start',
                'positions' => 'required|array|min:1',
                'positions.*.name' => 'required|string|max:255',
                'positions.*.candidates' => 'nullable|array',
                'enable_geo_location' => 'nullable|boolean',
                'geo_latitude' => 'nullable|numeric',
                'geo_longitude' => 'nullable|numeric',
                'geo_radius' => 'nullable|numeric',
            ]);

            DB::beginTransaction();

            $accessCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            $election = Election::create([
                'title' => $validated['title'],
                'description' => $request->description,
                'organization_id' => $validated['organization_id'],
                'start_date' => $validated['voting_start'],
                'end_date' => $validated['voting_end'],
                'created_by' => auth()->id(),
                'status' => 'draft',
                'access_code' => $accessCode,
                'geo_latitude' => $request->geo_latitude,
                'geo_longitude' => $request->geo_longitude,
                'geo_radius_meters' => $request->geo_radius,
                'require_geo_verification' => $request->boolean('enable_geo_location'),
            ]);

            // Create positions and candidates
            foreach ($validated['positions'] as $positionData) {
                $position = $election->positions()->create([
                    'name' => $positionData['name']
                ]);

                if (!empty($positionData['candidates'])) {
                    foreach ($positionData['candidates'] as $candidateName) {
                        if (trim($candidateName)) {
                            $position->candidates()->create([
                                'name' => trim($candidateName)
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            // Return JSON for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'election_id' => $election->id,
                    'election_code' => $election->access_code,
                    'registration_url' => url('/voter/register/' . $election->access_code),
                ]);
            }

            return redirect()->route('admin.elections.index')
                ->with('success', 'Election created successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }
            throw $e;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Election creation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create election: ' . $e->getMessage(),
                ], 500);
            }

            return back()->withErrors(['general' => 'An error occurred while creating the election'])
                ->withInput();
        }
    }

    public function show(Election $election)
    {
        if (!$this->canUserManageElection($election)) {
            abort(403, 'Unauthorized');
        }

        $election->load(['organization', 'candidates', 'votes']);

        return view('main-admin.elections.show', compact('election'));
    }

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

    public function destroy(Election $election)
    {
        if ($election->created_by !== auth()->id()) {
            return back()->withErrors(['general' => 'Only the election creator can delete it']);
        }

        try {
            DB::beginTransaction();

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
