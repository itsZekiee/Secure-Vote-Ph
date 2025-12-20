<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\User;
use App\Models\Election;
use App\Models\Organization;
use App\Models\Partylist;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class CandidateController extends Controller
{
    /**
     * Display a listing of candidates
     */
    public function index()
    {
        $candidates = Candidate::where(function($q) {
                $q->where('created_by', auth()->id())
                  ->orWhereHas('election', function($qe) {
                      $qe->where('created_by', auth()->id())
                         ->orWhereHas('subAdmins', function($qs) {
                             $qs->where('user_id', auth()->id());
                         });
                  });
            })
            ->with(['user', 'election', 'position', 'partylist'])
            ->withCount(['votes'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('main-admin.candidates', compact('candidates'));
    }

    /**
     * Determine if the current user can manage the given candidate.
     * Creators of the candidate or sub-admins assigned to the candidate's election may manage the record.
     */
    private function canUserManageCandidate(Candidate $candidate): bool
    {
        if ($candidate->created_by === auth()->id()) {
            return true;
        }
        if ($candidate->election_id) {
            return Election::where('id', $candidate->election_id)
                ->where(function($q) {
                    $q->where('created_by', auth()->id())
                      ->orWhereHas('subAdmins', function($qs) {
                          $qs->where('user_id', auth()->id());
                      });
                })->exists();
        }

        return false;
    }

    /**
     * Show the form for creating a new candidate
     */
    public function create()
    {
        $users = User::select('id', 'name', 'email')->get();
        $positions = Position::select('id', 'title as name')->get();
        // Allow elections the user created or is assigned to
        $elections = Election::where(function($q) {
                $q->where('created_by', auth()->id())
                  ->orWhereHas('subAdmins', function($qs) {
                      $qs->where('user_id', auth()->id());
                  });
            })->select('id', 'title')->get();

        $organizations = Organization::where(function($q) {
                $q->where('created_by', auth()->id());
            })->select('id', 'name')->get();

        // Partylists that belong to allowed elections or created by user
        $partylists = Partylist::where(function($q) {
                $q->where('created_by', auth()->id())
                  ->orWhereHas('election', function($qe) {
                      $qe->where('created_by', auth()->id())
                         ->orWhereHas('subAdmins', function($qs) {
                             $qs->where('user_id', auth()->id());
                         });
                  });
            })->select('id', 'name', 'organization_id')->get();

        $commonPositions = [
            'President',
            'Vice President',
            'Secretary',
            'Treasurer',
            'Auditor',
            'Public Relations Officer',
            'Representative'
        ];

        return view('main-admin.candidate.candidate-create', compact(
            'users',
            'positions',
            'elections',
            'organizations',
            'partylists',
            'commonPositions'
        ));
    }

    /**
     * Store a newly created candidate
     *
     * Accepts either:
     * - `user_id` (existing user), OR
     * - `user_name` and `user_email` (will find or create user)
     */
    public function store(Request $request)
    {
        $rules = [
            'user_id' => 'nullable|exists:users,id',
            'user_name' => 'required_without:user_id|string|max:255',
            'user_email' => 'required_without:user_id|email|max:255',
            'organization_id' => 'required|exists:organizations,id',
            'election_id' => 'nullable|exists:elections,id',
            'position_id' => 'nullable|exists:positions,id',
            'new_position_name' => 'nullable|string|max:255',
            'partylist_id' => 'nullable|exists:partylists,id',
            'platform' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:3072',
            'status' => 'required|in:active,inactive,disqualified'
        ];

        $validated = $request->validate($rules);

        try {
            DB::beginTransaction();

            // Resolve or create user (only pass columns that exist in your users table)
            if (!empty($validated['user_id'])) {
                $userId = $validated['user_id'];
            } else {
                $user = User::firstWhere('email', $validated['user_email']);
                if (!$user) {
                    // Only insert name, email, password (remove role, is_active, etc.)
                    $user = User::create([
                        'name' => $validated['user_name'],
                        'email' => $validated['user_email'],
                        'password' => Hash::make(Str::random(16)),
                    ]);
                } else {
                    if (empty($user->name) && !empty($validated['user_name'])) {
                        $user->name = $validated['user_name'];
                        $user->save();
                    }
                }
                $userId = $user->id;
            }

            // Handle position
            if (empty($validated['position_id']) && !empty($validated['new_position_name'])) {

                // Determine election_id for the new position (may be null)
                $electionIdForPosition = $validated['election_id'] ?? null;

                // Create or find position. If an election_id is provided, scope the position to that election.
                if ($electionIdForPosition !== null) {
                    $position = Position::firstOrCreate(
                        [
                            'title' => $validated['new_position_name'],
                            'election_id' => $electionIdForPosition,
                        ],
                        ['organization_id' => $validated['organization_id'] ?? null]
                    );
                } else {
                    // If no election specified, create/find a global position record by title only
                    $position = Position::firstOrCreate(
                        ['title' => $validated['new_position_name']],
                        ['organization_id' => $validated['organization_id'] ?? null]
                    );
                }

                $validated['position_id'] = $position->id;
            }

            // Check duplicate candidate
            $existingCandidate = Candidate::where('user_id', $userId)
                ->when(!empty($validated['election_id']), fn($q) => $q->where('election_id', $validated['election_id']), fn($q) => $q->whereNull('election_id'))
                ->where('position_id', $validated['position_id'])
                ->first();

            if ($existingCandidate) {
                DB::rollBack();
                if ($request->ajax()) {
                    return response()->json(['message' => 'This user is already a candidate for this position in this election', 'errors' => ['user_email' => ['Duplicate candidate']]], 422);
                }
                return back()->withErrors(['user_email' => 'Duplicate candidate'])->withInput();
            }

            // Handle photo upload
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('candidates', 'public');
            }

            // If an election is provided, ensure the user can manage that election (creator or sub-admin)
            if (!empty($validated['election_id'])) {
                $allowed = Election::where('id', $validated['election_id'])
                    ->where(function($q) {
                        $q->where('created_by', auth()->id())
                          ->orWhereHas('subAdmins', function($qs) {
                              $qs->where('user_id', auth()->id());
                          });
                    })->exists();

                if (! $allowed) {
                    DB::rollBack();
                    if ($request->ajax()) {
                        return response()->json(['success' => false, 'message' => 'Unauthorized election selection'], 403);
                    }
                    return back()->withErrors(['election_id' => 'Unauthorized election selection'])->withInput();
                }
            }

            // Create candidate
            $candidateData = [
                'user_id' => $userId,
                'organization_id' => $validated['organization_id'],
                'election_id' => $validated['election_id'] ?? null,
                'position_id' => $validated['position_id'],
                'partylist_id' => $validated['partylist_id'] ?? null,
                'platform' => $validated['platform'] ?? null,
                'photo' => $photoPath,
                'status' => $validated['status'],
                'created_by' => auth()->id(),
            ];

            Candidate::create($candidateData);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'title' => 'Candidate Created',
                    'message' => 'Candidate created and linked to the selected partylist.'
                ]);
            }

            return redirect()->route('admin.candidates.index')->with('success', 'Candidate created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CandidateController@store error: '.$e->getMessage());

            if ($request->ajax()) {
                return response()->json(['message' => 'An error occurred: '.$e->getMessage(), 'errors' => []], 500);
            }

            return back()->withErrors(['general' => 'An error occurred'])->withInput();
        }
    }



    /**
     * Display the specified candidate
     */
    public function show(Candidate $candidate)
    {
        if (! $this->canUserManageCandidate($candidate)) {
            abort(403, 'Unauthorized');
        }

        $candidate->load(['user', 'election', 'position', 'partylist', 'votes']);

        return view('main-admin.candidates.show', compact('candidate'));
    }

    /**
     * Show the form for editing the specified candidate
     */
    public function edit(Candidate $candidate)
    {
        if (! $this->canUserManageCandidate($candidate)) {
            abort(403, 'Unauthorized');
        }

        try {
            $usersQuery = $this->voterUsersQuery();

            if (Schema::hasColumn('users', 'is_active')) {
                $usersQuery = $usersQuery->where('is_active', true);
            }

            $users = $usersQuery->get();
        } catch (\Throwable $e) {
            Log::error('CandidateController@edit - voterUsersQuery failed: '.$e->getMessage());
            $users = collect();
        }

        $elections = (Schema::hasColumn('elections', 'status'))
            ? Election::whereIn('status', ['active', 'draft'])->get()
            : Election::all();

        $partylists = (Schema::hasColumn('partylists', 'status'))
            ? Partylist::where('status', 'active')->get()
            : Partylist::all();

        try {
            $positions = $this->positionsQuery()->get();
        } catch (\Throwable $e) {
            Log::error('CandidateController@edit - positionsQuery failed: '.$e->getMessage());
            $positions = Position::all();
        }

        return view('main-admin.candidates.edit', compact('candidate', 'users', 'elections', 'partylists', 'positions'));
    }

    /**
     * Update the specified candidate
     */
    public function update(Request $request, Candidate $candidate)
    {
        if (! $this->canUserManageCandidate($candidate)) {
            return back()->withErrors(['general' => 'Unauthorized']);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'election_id' => 'nullable|exists:elections,id',
            'position_id' => 'required|exists:positions,id',
            'partylist_id' => 'nullable|exists:partylists,id',
            'platform' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:3072',
            'status' => 'required|in:active,inactive,disqualified'
        ]);

        try {
            DB::beginTransaction();

            $existingCandidate = Candidate::where('user_id', $validated['user_id'])
                ->where('election_id', $validated['election_id'])
                ->where('position_id', $validated['position_id'])
                ->where('id', '!=', $candidate->id)
                ->first();

            if ($existingCandidate) {
                return back()->withErrors(['user_id' => 'This user is already a candidate for this position in this election'])
                    ->withInput();
            }

            if ($request->hasFile('photo')) {
                if ($candidate->photo) {
                    Storage::disk('public')->delete($candidate->photo);
                }

                $path = $request->file('photo')->store('candidates', 'public');
                $validated['photo'] = $path;
            }

            $candidate->update($validated);

            DB::commit();

            return redirect()->route('admin.candidates.index')
                ->with('success', 'Candidate updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CandidateController@update error: '.$e->getMessage());

            return back()->withErrors(['general' => 'An error occurred while updating the candidate'])
                ->withInput();
        }
    }

    /**
     * Remove the specified candidate from storage
     */
    public function destroy(Candidate $candidate)
    {
        if (! $this->canUserManageCandidate($candidate)) {
            return back()->withErrors(['general' => 'Unauthorized']);
        }

        try {
            DB::beginTransaction();

            if ($candidate->votes()->count() > 0) {
                return back()->withErrors(['general' => 'Cannot delete candidate with existing votes']);
            }

            if ($candidate->photo) {
                Storage::disk('public')->delete($candidate->photo);
            }

            $candidate->delete();

            DB::commit();

            return redirect()->route('admin.candidates.index')
                ->with('success', 'Candidate deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CandidateController@destroy error: '.$e->getMessage());

            return back()->withErrors(['general' => 'An error occurred while deleting the candidate']);
        }
    }

    /**
     * Search candidates
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $election_id = $request->get('election_id', '');
        $position_id = $request->get('position_id', '');
        $partylist_id = $request->get('partylist_id', '');
        $status = $request->get('status', '');

        $candidates = Candidate::where('created_by', auth()->id())
            ->with(['user', 'election', 'position', 'partylist'])
            ->withCount(['votes'])
            ->when($query, function ($q) use ($query) {
                return $q->whereHas('user', function ($userQuery) use ($query) {
                    $userQuery->where('name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%");
                });
            })
            ->when($election_id, function ($q) use ($election_id) {
                return $q->where('election_id', $election_id);
            })
            ->when($position_id, function ($q) use ($position_id) {
                return $q->where('position_id', $position_id);
            })
            ->when($partylist_id, function ($q) use ($partylist_id) {
                return $q->where('partylist_id', $partylist_id);
            })
            ->when($status, function ($q) use ($status) {
                return $q->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['candidates' => $candidates]);
    }

    /**
     * Export candidates data
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');

        $candidates = Candidate::where('created_by', auth()->id())
            ->with(['user', 'election', 'position', 'partylist'])
            ->withCount(['votes'])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($format === 'json') {
            return response()->json(['candidates' => $candidates]);
        }

        $filename = 'candidates_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($candidates) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID', 'Name', 'Email', 'Election', 'Position', 'Partylist', 'Status', 'Votes', 'Created At'
            ]);

            foreach ($candidates as $candidate) {
                fputcsv($file, [
                    $candidate->id,
                    $candidate->user->name,
                    $candidate->user->email,
                    optional($candidate->election)->title,
                    optional($candidate->position)->title,
                    $candidate->partylist->name ?? 'Independent',
                    $candidate->status,
                    $candidate->votes_count,
                    $candidate->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Resolve a query builder that returns voter users.
     */
    private function voterUsersQuery()
    {
        try {
            if (method_exists(User::class, 'scopeRole')) {
                return User::role('voter');
            }
        } catch (\Throwable $e) {
            Log::debug('voterUsersQuery: scopeRole check failed: '.$e->getMessage());
        }

        try {
            // if relationship exists
            return User::whereHas('roles', function ($q) {
                $q->where('name', 'voter');
            });
        } catch (\Throwable $e) {
            Log::debug('voterUsersQuery: whereHas roles check failed: '.$e->getMessage());
        }

        try {
            if (Schema::hasColumn('users', 'type')) {
                return User::where('type', 'voter');
            }
        } catch (\Throwable $e) {
            Log::debug('voterUsersQuery: hasColumn(type) check failed: '.$e->getMessage());
        }

        try {
            if (Schema::hasColumn('users', 'is_voter')) {
                return User::where('is_voter', true);
            }
        } catch (\Throwable $e) {
            Log::debug('voterUsersQuery: hasColumn(is_voter) check failed: '.$e->getMessage());
        }

        return User::whereRaw('0 = 1');
    }

    /**
     * Resolve a query builder that returns active positions safely.
     */
    private function positionsQuery()
    {
        try {
            if (Schema::hasColumn('positions', 'is_active')) {
                return Position::where('is_active', true);
            }
        } catch (\Throwable $e) {
            Log::debug('positionsQuery: hasColumn(is_active) check failed: '.$e->getMessage());
        }

        try {
            if (Schema::hasColumn('positions', 'status')) {
                return Position::where('status', 'active');
            }
        } catch (\Throwable $e) {
            Log::debug('positionsQuery: hasColumn(status) check failed: '.$e->getMessage());
        }

        return Position::query()->orderBy('title');
    }
}
