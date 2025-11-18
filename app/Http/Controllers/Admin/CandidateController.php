<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\User;
use App\Models\Election;
use App\Models\Partylist;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class CandidateController extends Controller
{
    /**
     * Display a listing of candidates
     */
    public function index()
    {
        $candidates = Candidate::with(['user', 'election', 'position', 'partylist'])
            ->withCount(['votes'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('main-admin.candidates', compact('candidates'));
    }

    /**
     * Show the form for creating a new candidate
     */
    public function create()
    {
        try {
            $usersQuery = $this->voterUsersQuery();

            // Only apply is_active filter if the column exists
            if (Schema::hasColumn('users', 'is_active')) {
                $usersQuery = $usersQuery->where('is_active', true);
            }

            $users = $usersQuery->get();
        } catch (\Throwable $e) {
            Log::error('CandidateController@create - voterUsersQuery failed: '.$e->getMessage());
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
            Log::error('CandidateController@create - positionsQuery failed: '.$e->getMessage());
            $positions = Position::all();
        }

        $commonPositions = ['President','Vice President','Secretary','Treasurer','Auditor','Representative','Senator','Councilor'];

        return view('main-admin.candidate.candidate-create', compact('users', 'elections', 'partylists', 'positions', 'commonPositions'));
    }

    /**
     * Store a newly created candidate
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'election_id' => 'required|exists:elections,id',
            'position_id' => 'nullable|exists:positions,id',
            'new_position_name' => 'required_if:position_id,null|string|max:255',
            'partylist_id' => 'nullable|exists:partylists,id',
            'platform' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:active,inactive,disqualified'
        ]);

        try {
            DB::beginTransaction();

            // Handle new position creation
            if (empty($validated['position_id'])) {
                $position = Position::firstOrCreate([
                    'election_id' => $validated['election_id'],
                    'title' => $validated['new_position_name']
                ]);
                $validated['position_id'] = $position->id;
            }

            $existingCandidate = Candidate::where('user_id', $validated['user_id'])
                ->where('election_id', $validated['election_id'])
                ->where('position_id', $validated['position_id'])
                ->first();

            if ($existingCandidate) {
                DB::rollBack();
                if ($request->ajax()) {
                    return response()->json(['errors' => ['user_id' => ['This user is already a candidate for this position in this election']]], 422);
                }
                return back()->withErrors(['user_id' => 'This user is already a candidate for this position in this election'])
                    ->withInput();
            }

            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('candidates', 'public');
                $validated['photo'] = $path;
            }

            Candidate::create($validated);

            DB::commit();

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Candidate created successfully.']);
            }

            return redirect()->route('admin.candidates.index')
                ->with('success', 'Candidate created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CandidateController@store error: '.$e->getMessage());

            if ($request->ajax()) {
                return response()->json(['errors' => ['general' => ['An error occurred while creating the candidate']]], 422);
            }

            return back()->withErrors(['general' => 'An error occurred while creating the candidate'])
                ->withInput();
        }
    }

    /**
     * Display the specified candidate
     */
    public function show(Candidate $candidate)
    {
        $candidate->load(['user', 'election', 'position', 'partylist', 'votes']);

        return view('main-admin.candidates.show', compact('candidate'));
    }

    /**
     * Show the form for editing the specified candidate
     */
    public function edit(Candidate $candidate)
    {
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
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'election_id' => 'required|exists:elections,id',
            'position_id' => 'required|exists:positions,id',
            'partylist_id' => 'nullable|exists:partylists,id',
            'platform' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
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

        $candidates = Candidate::with(['user', 'election', 'position', 'partylist'])
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

        $candidates = Candidate::with(['user', 'election', 'position', 'partylist'])
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
                    $candidate->election->title,
                    $candidate->position->title,
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
