<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Models\Organization;
use App\Models\Partylist;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ElectionController extends Controller
{
    private function canUserManageElection(Election $election): bool
    {
        return $election->created_by === auth()->id() ||
            $election->subAdmins()->where('user_id', auth()->id())->exists();
    }

    private function getView($name)
    {
        if (auth()->check() && auth()->user()->hasRole('admin') && !auth()->user()->hasRole('super-admin')) {
            return "admin.$name";
        }
        return "main-admin.$name";
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

        $organizations = Organization::all();
        $positions = collect();

        return view($this->getView('elections'), compact('elections', 'organizations', 'positions'));
    }

    public function create()
    {
        $organizations = Organization::all();
        $positions = collect();

        return view($this->getView('elections'), compact('organizations', 'positions'));
    }

    public function edit(string $id)
    {
        $election = Election::findOrFail($id);
        if (!$this->canUserManageElection($election)) {
            abort(403);
        }
        $organizations = Organization::all();
        return view($this->getView('elections.edit'), compact('election', 'organizations'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'organization_id' => 'nullable|exists:organizations,id',
                'voting_start' => 'required|date',
                'voting_end' => 'required|date|after:voting_start',
                'positions' => 'required|array|min:1',
                'positions.*.name' => 'required|string|max:255',
                'positions.*.candidates' => 'nullable|array',
                'enable_geo_location' => 'nullable|boolean',
                'enable_geo_registration' => 'nullable|boolean',
                'geo_latitude' => 'nullable|numeric',
                'geo_longitude' => 'nullable|numeric',
                'geo_radius' => 'nullable|numeric',
                'auto_approve_voters' => 'nullable|boolean',
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
                'require_geo_registration' => $request->boolean('enable_geo_registration'),
                'auto_approve_voters' => $request->boolean('auto_approve_voters'),
            ]);

            foreach ($validated['positions'] as $positionData) {
                $position = $election->positions()->create([
                    'name' => $positionData['name']
                ]);

                if (!empty($positionData['candidates'])) {
                    foreach ($positionData['candidates'] as $candidateName) {

                        if ($candidateName && !empty($validated['organization_id'])) {
                            $candidate = Candidate::where('organization_id', $validated['organization_id'])
                                ->where('position_id', $position->id)
                                ->where('name', $candidateName)
                                ->first();

                            if ($candidate) {
                                // ✅ ATTACH TO ELECTION
                                $candidate->update([
                                    'election_id' => $election->id
                                ]);
                            }
                        }
                    }
                }


            }

            DB::commit();

            if ($request->expectsJson()) {
                // Ensure relation is loaded so the frontend receives organization data
                $election->load('organization');

                $org = $election->organization;
                $orgName = optional($org)->name
                    ?? optional($org)->title
                    ?? optional($org)->org_name
                    ?? null;

                return response()->json([
                    'success' => true,
                    'message' => 'Election created successfully',
                    'election' => [
                        'id' => $election->id,
                        'access_code' => $election->access_code ?? null,
                        // provide both keys the frontend may expect
                        'code' => $election->access_code ?? null,
                        'title' => $election->title,
                        'organization' => $org ? ['id' => $org->id, 'name' => $orgName] : null,
                        'organization_name' => $orgName,
                        'created_at' => optional($election->created_at)->toIso8601String(),
                    ],
                    'registration_url' => url('/voter/register/' . ($election->access_code ?? '')),
                ], 201);
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

    public function show(string $id)
    {
        $election = Election::findOrFail($id);
        if (!$this->canUserManageElection($election)) {
            abort(403, 'Unauthorized');
        }

        $election->load(['organization', 'candidates', 'votes', 'positions' => function($q) {
            $q->with('candidates.partylist');
        }]);

        // Mock voter data for preview
        $voter = [
            'name' => 'Admin Preview User',
            'email' => auth()->user()->email,
        ];

        $positions = $election->positions;

        return view($this->getView('elections.show'), compact('election', 'voter', 'positions'));
    }

    public function update(Request $request, string $id)
    {
        $election = Election::findOrFail($id);
        if (!$this->canUserManageElection($election)) {
            return back()->withErrors(['general' => 'You do not have permission to edit this election']);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'organization_id' => 'nullable|exists:organizations,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:draft,active,completed,cancelled',
            'registration_deadline' => 'nullable|date',
            'accepted_domains' => 'nullable|string',
            'max_votes' => 'nullable|integer|min:1',
            'enable_geo_location' => 'nullable|boolean',
            'enable_geo_registration' => 'nullable|boolean',
            'geo_latitude' => 'nullable|numeric',
            'geo_longitude' => 'nullable|numeric',
            'geo_radius' => 'nullable|numeric',
            'auto_approve_voters' => 'nullable|boolean',
            'sub_admin_ids' => 'nullable|array',
            'sub_admin_ids.*' => 'exists:users,id',
            'positions' => 'nullable|array',
            'positions.*.id' => 'nullable',
            'positions.*.name' => 'required|string|max:255',
            'positions.*.candidates' => 'nullable|array',
            'positions.*.candidates.*.id' => 'nullable',
            'positions.*.candidates.*.first_name' => 'required|string|max:255',
            'positions.*.candidates.*.middle_name' => 'nullable|string|max:255',
            'positions.*.candidates.*.last_name' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $election->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? $election->description,
                'organization_id' => $validated['organization_id'] ?? $election->organization_id,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'status' => $validated['status'],
                'registration_deadline' => $validated['registration_deadline'] ?? null,
                'accepted_domains' => $validated['accepted_domains'] ?? null,
                'max_votes' => $validated['max_votes'] ?? 1,
                'geo_latitude' => $request->geo_latitude ?? $election->geo_latitude,
                'geo_longitude' => $request->geo_longitude ?? $election->geo_longitude,
                'geo_radius_meters' => $request->geo_radius ?? $election->geo_radius_meters,
                'require_geo_verification' => $request->boolean('enable_geo_location'),
                'require_geo_registration' => $request->boolean('enable_geo_registration'),
                'auto_approve_voters' => $request->boolean('auto_approve_voters'),
            ]);

            if (!empty($validated['sub_admin_ids'])) {
                $election->subAdmins()->sync($validated['sub_admin_ids']);
            } else {
                $election->subAdmins()->detach();
            }

            // Handle Positions and Candidates
            if (isset($validated['positions'])) {
                $positionIds = collect($validated['positions'])->pluck('id')->filter()->toArray();

                // Delete positions not in the request (only if no votes)
                $positionsToDelete = $election->positions()->whereNotIn('id', $positionIds)->get();
                foreach ($positionsToDelete as $pos) {
                    if ($pos->votes()->count() > 0) {
                        throw new \Exception("Cannot delete position '{$pos->name}' because it already has votes.");
                    }
                    $pos->candidates()->delete();
                    $pos->delete();
                }

                foreach ($validated['positions'] as $pIdx => $pData) {
                    $position = $election->positions()->updateOrCreate(
                        ['id' => $pData['id'] ?? null],
                        ['title' => $pData['name'], 'order' => $pIdx + 1]
                    );

                    if (isset($pData['candidates'])) {
                        $candidateIds = collect($pData['candidates'])->pluck('id')->filter()->toArray();

                        // Delete candidates not in the request
                        $candidatesToDelete = $position->candidates()->whereNotIn('id', $candidateIds)->get();
                        foreach ($candidatesToDelete as $cand) {
                            if ($cand->votes()->count() > 0) {
                                throw new \Exception("Cannot delete candidate '{$cand->first_name} {$cand->last_name}' because they already have votes.");
                            }
                            $cand->delete();
                        }

                        foreach ($pData['candidates'] as $cIdx => $cData) {
                            $firstName = trim($cData['first_name'] ?? '');
                            $lastName = trim($cData['last_name'] ?? '');
                            $candidateName = trim($firstName . ' ' . (isset($cData['middle_name']) && $cData['middle_name'] ? $cData['middle_name'] . ' ' : '') . $lastName);

                            if (!$firstName || !$lastName) {
                                continue; // skip incomplete names
                            }

                            // If id is provided, update the candidate; else create or update by name to avoid duplicates
                            if (!empty($cData['id'])) {
                                $position->candidates()->updateOrCreate(
                                    ['id' => $cData['id']],
                                    [
                                        'election_id' => $election->id,
                                        'name' => $candidateName,
                                        'first_name' => $firstName,
                                        'middle_name' => $cData['middle_name'] ?? null,
                                        'last_name' => $lastName,
                                        'order' => $cIdx + 1,
                                    ]
                                );
                            } else {
                                // Try to find candidate by name to avoid duplicates
                                $candidate = $position->candidates()
                                    ->where('first_name', $firstName)
                                    ->where('last_name', $lastName)
                                    ->first();

                                if ($candidate) {
                                    $candidate->update([
                                        'election_id' => $election->id,
                                        'name' => $candidateName,
                                        'first_name' => $firstName,
                                        'middle_name' => $cData['middle_name'] ?? null,
                                        'last_name' => $lastName,
                                        'order' => $cIdx + 1,
                                    ]);
                                } else {
                                    $position->candidates()->create([
                                        'election_id' => $election->id,
                                        'name' => $candidateName,
                                        'first_name' => $firstName,
                                        'middle_name' => $cData['middle_name'] ?? null,
                                        'last_name' => $lastName,
                                        'order' => $cIdx + 1,
                                    ]);
                                }
                            }
                        }

                    }
                }
            }

            DB::commit();

            return redirect()->route('admin.elections.index')
                ->with('success', 'Election updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Election update failed: ' . $e->getMessage());
            return back()->withErrors(['general' => $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(string $id)
    {
        $election = Election::findOrFail($id);
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

    public function assignSubAdmin(Request $request, string $id)
    {
        $election = Election::findOrFail($id);
        if ($election->created_by !== auth()->id()) {
            return response()->json(['error' => 'Only the creator can share access'], 403);
        }

        $validated = $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        try {
            $user = \App\Models\User::where('email', $validated['email'])->first();

            if (!$user->isAdmin() && !$user->hasRole('admin') && !$user->hasRole('super-admin')) {
                return response()->json(['error' => 'Only administrators can be given access'], 422);
            }

            if ($election->subAdmins()->where('user_id', $user->id)->exists()) {
                return response()->json(['error' => 'This user already has access'], 422);
            }

            if ($user->id === auth()->id()) {
                return response()->json(['error' => 'You already have access as the creator'], 422);
            }

            $election->subAdmins()->attach($user->id);

            AuditLogger::log(
                'SHARE_ACCESS',
                'Elections',
                "Shared access for election: {$election->title} with user: {$user->email}"
            );

            return response()->json(['success' => true, 'message' => 'Access shared successfully with ' . $user->name]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to share access'], 422);
        }
    }

    public function searchAdmins(Request $request)
    {
        $query = $request->get('q', '');
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $admins = \App\Models\User::where('email', 'like', "%{$query}%")
            ->where(function($q) {
                $q->where('role', 'admin')
                  ->orWhere('role', 'super-admin');
            })
            ->where('id', '!=', auth()->id())
            ->limit(10)
            ->get(['id', 'name', 'email']);

        return response()->json($admins);
    }

    public function removeSubAdmin(Request $request, string $id)
    {
        $election = Election::findOrFail($id);
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

    public function candidates(string $id)
    {
        $election = Election::findOrFail($id);
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

    /**
     * Get partylists for an organization (Automation Mode)
     */
    public function getOrganizationPartylists(string $organizationId): JsonResponse
    {
        $partylists = Partylist::where('organization_id', $organizationId)
            ->where('status', 'active')
            ->select('id', 'name')
            ->get();

        return response()->json([
            'success' => true,
            'partylists' => $partylists
        ]);
    }

    /**
     * Get candidates grouped by position for a partylist (Automation Mode)
     */
    public function getPartylistCandidates(string $partylistId): JsonResponse
    {
        $partylist = Partylist::with(['candidates.position'])->findOrFail($partylistId);

        $groupedCandidates = $partylist->candidates
            ->groupBy(function($candidate) {
                return $candidate->position_id ?? 0;
            })
            ->map(function ($candidates) {
                $firstCandidate = $candidates->first();
                $positionName = ($firstCandidate && $firstCandidate->position)
                    ? $firstCandidate->position->title
                    : 'Unassigned';

                return [
                    'name' => (string) $positionName,
                    'candidates' => $candidates->pluck('name')->toArray()
                ];
            })
            ->values()
            ->toArray();

        return response()->json([
            'success' => true,
            'partylist_name' => $partylist->name,
            'positions' => $groupedCandidates
        ]);
    }
}

