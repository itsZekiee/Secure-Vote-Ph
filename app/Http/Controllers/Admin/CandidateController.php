<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\User;
use App\Models\Election;
use App\Models\Organization;
use App\Models\Partylist;
use App\Models\Position;
use App\Imports\CandidateImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

class CandidateController extends Controller
{
    private function getView($name)
    {
        if (auth()->check() && auth()->user()->hasRole('admin') && !auth()->user()->hasRole('super-admin')) {
            return "admin.$name";
        }
        return "main-admin.$name";
    }

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

        $elections = Election::where('created_by', auth()->id())->get();

        return view($this->getView('candidates'), compact('candidates', 'elections'));
    }

    /**
     * Determine if the current user can manage the given candidate.
     * Creators of the candidate or sub-admins assigned to the candidate's election may manage the record.
     */
    private function canUserManageCandidate(Candidate $candidate): bool
    {
        $user = auth()->user();

        // Admin can manage everything
        if ($user && ($user->isAdmin() || $user->hasRole('admin'))) {
            return true;
        }

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

        $organizations = Organization::all();

        // Partylists that belong to allowed elections or created by user
        $partylists = Partylist::all();

        $commonPositions = [
            'President',
            'Vice President',
            'Secretary',
            'Treasurer',
            'Auditor',
            'Public Relations Officer',
            'Representative'
        ];

        return view($this->getView('candidate.candidate-create'), compact(
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
            $userNameForCandidate = null;
            if (!empty($validated['user_id'])) {
                $userId = $validated['user_id'];
                $user = User::findOrFail($userId);
                $userNameForCandidate = $user->name;
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
                $userNameForCandidate = $validated['user_name'];
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
                    // Check whether the DB allows NULL for positions.election_id
                    // Using Schema facade is more portable than raw SQL
                    $isNullable = true; // Default to true based on migrations
                    try {
                        // Check if the column is NOT NULL
                        // In Laravel 11+, we can use Schema::getColumnType or similar,
                        // but a simple way to check is using Schema manager or just assuming nullable if migration says so.
                        // However, to be safe and avoid the raw query:
                        $isNullable = Schema::getConnection()
                            ->getDoctrineSchemaManager()
                            ->listTableDetails('positions')
                            ->getColumn('election_id')
                            ->getNotnull() === false;
                    } catch (\Exception $e) {
                        // Fallback: If we can't determine, check the migration history or just allow it.
                        // In this project, we know it was made nullable.
                        Log::warning("Could not determine nullability of positions.election_id: " . $e->getMessage());
                    }

                    if ($isNullable === false) {
                        // Database requires election_id. Return validation error asking user to select an election.
                        DB::rollBack();
                        $message = 'Creating a new position requires selecting an election on this server. Please select an election or use an existing position.';
                        if ($request->ajax()) {
                            return response()->json(['message' => $message, 'errors' => ['election_id' => ['Election required when creating a new position']]], 422);
                        }
                        return back()->withErrors(['election_id' => 'Election required when creating a new position'])->withInput();
                    }

                    // If DB allows NULL, create/find a global position record by title only
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
            $nameParts = preg_split('/\s+/', trim($userNameForCandidate));
            if (count($nameParts) === 1) {
                $firstName = $nameParts[0];
                $middleName = null;
                $lastName = $nameParts[0];
            } elseif (count($nameParts) === 2) {
                $firstName = $nameParts[0];
                $middleName = null;
                $lastName = $nameParts[1];
            } else {
                $firstName = array_shift($nameParts);
                $lastName = array_pop($nameParts);
                $middleName = implode(' ', $nameParts);
            }

            $candidateData = [
                'user_id' => $userId,
                'first_name' => $firstName,
                 'middle_name' => $middleName,
                'last_name' => $lastName,
                'name' => $userNameForCandidate,
                'organization_id' => $validated['organization_id'],
                'election_id' => $validated['election_id'] ?? null,
                'position_id' => $validated['position_id'],
                'partylist_id' => $validated['partylist_id'] ?? null,
                'platform' => $validated['platform'] ?? null,
                'photo' => $photoPath,
                'status' => $validated['status'],
                'created_by' => auth()->id(),
            ];

            Candidate::updateOrCreate(
                [
                    'user_id' => $userId,
                    'election_id' => $validated['election_id'] ?? null,
                    'position_id' => $validated['position_id'],
                ],
                $candidateData
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'title' => 'Candidate Created',
                    'message' => 'Candidate created and linked to the selected partylist.'
                ]);
            }

            if (!empty($validated['partylist_id'])) {
                $partylist = Partylist::find($validated['partylist_id']);
                if ($partylist) {
                    $validated['organization_id'] = $partylist->organization_id;
                }
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
    public function show(string $id)
    {
        $candidate = Candidate::findOrFail($id);
        if (! $this->canUserManageCandidate($candidate)) {
            abort(403, 'Unauthorized');
        }

        $candidate->load(['user', 'election', 'position', 'partylist', 'votes']);

        return view($this->getView('candidate.show'), compact('candidate'));
    }

    /**
     * Show the form for editing the specified candidate
     */
    public function edit(string $id)
    {
        $candidate = Candidate::findOrFail($id);
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

        $partylists = Partylist::all();

        // Added organizations because edit.blade.php requires it
        $organizations = Organization::all();

        try {
            $positions = $this->positionsQuery()->get();
        } catch (\Throwable $e) {
            Log::error('CandidateController@edit - positionsQuery failed: '.$e->getMessage());
            $positions = Position::all();
        }

        return view($this->getView('candidate.edit'), compact('candidate', 'users', 'elections', 'partylists', 'positions', 'organizations'));
    }

    /**
     * Display the candidate profile (alias for show)
     */
    public function profile(string $id)
    {
        return $this->show($id);
    }

    /**
     * Update the specified candidate
     */
    public function update(Request $request, string $id)
    {
        $candidate = Candidate::findOrFail($id);
        if (! $this->canUserManageCandidate($candidate)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            return back()->withErrors(['general' => 'Unauthorized']);
        }

        $validated = $request->validate([
            'user_name' => 'nullable|string', // Support for x-model="formData.user_name"
            'user_email' => 'nullable|email', // Support for x-model="formData.user_email"
            'organization_id' => 'nullable|exists:organizations,id',
            'election_id' => 'nullable|exists:elections,id',
            'position_id' => 'required|exists:positions,id',
            'partylist_id' => 'nullable|exists:partylists,id',
            'platform' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:3072',
            'status' => 'required|in:active,inactive,disqualified'
        ]);

        try {
            DB::beginTransaction();

            // Handle user name/email updates if provided
            if ($candidate->user) {
                if ($request->has('user_name')) $candidate->user->update(['name' => $validated['user_name']]);
                // Email update might be sensitive, usually handled separately, but following the form's lead
                if ($request->has('user_email')) $candidate->user->update(['email' => $validated['user_email']]);
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

            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => 'Candidate updated successfully.',
                    'candidate' => $candidate
                ]);
            }

            return redirect()->route('admin.candidates.index')
                ->with('success', 'Candidate updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CandidateController@update error: '.$e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }

            return back()->withErrors(['general' => 'An error occurred while updating the candidate'])
                ->withInput();
        }
    }

    /**
     * Remove the specified candidate from storage
     */
    public function destroy(Request $request, string $id)
    {
        $candidate = Candidate::findOrFail($id);
        if (! $this->canUserManageCandidate($candidate)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            return back()->withErrors(['general' => 'Unauthorized']);
        }

        try {
            DB::beginTransaction();

            if ($candidate->votes()->count() > 0) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Cannot delete candidate with existing votes'], 422);
                }
                return back()->withErrors(['general' => 'Cannot delete candidate with existing votes']);
            }

            if ($candidate->photo) {
                Storage::disk('public')->delete($candidate->photo);
            }

            $candidate->delete();

            DB::commit();

            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => 'Candidate deleted successfully.'
                ]);
            }

            return redirect()->route('admin.candidates.index')
                ->with('success', 'Candidate deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CandidateController@destroy error: '.$e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'An error occurred while deleting the candidate'], 500);
            }

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

    public function export(Request $request)
    {
        $candidates = Candidate::where('created_by', auth()->id())
            ->with(['user', 'election', 'position', 'partylist', 'organization'])
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'candidates_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($candidates) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Full Name',
                'Email',
                'Organization',
                'Political Affiliation',
                'Designated Position',
                'Platform Statement',
                'Profile Photo'
            ]);

            foreach ($candidates as $candidate) {
                fputcsv($file, [
                    $candidate->user ? $candidate->user->name : ($candidate->name ?? ''),
                    $candidate->user ? $candidate->user->email : '',
                    $candidate->organization ? $candidate->organization->name : '',
                    $candidate->partylist ? $candidate->partylist->name : 'Independent',
                    $candidate->position ? $candidate->position->title : '',
                    $candidate->platform,
                    $candidate->photo
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importPreview(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,xml,tsv|max:51200',
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());
        $readerType = null;

        if ($extension === 'tsv') {
            $readerType = \Maatwebsite\Excel\Excel::TSV;
        } elseif ($extension === 'csv') {
            $readerType = \Maatwebsite\Excel\Excel::CSV;
        } elseif ($extension === 'xml') {
            $readerType = \Maatwebsite\Excel\Excel::XML;
        }

        try {
            $sheets = Excel::toCollection(new CandidateImport(), $file, null, $readerType);
            $rows = $sheets->first() ?? collect();
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error reading file: ' . $e->getMessage()], 422);
        }

        if ($rows->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'The uploaded file is empty or has no data.'], 422);
        }

        $data = $rows->map(function ($row, $index) {
            $fullName = $row['full_name'] ?? ($row['name'] ?? null);
            $email = $row['email'] ?? null;
            $orgName = $row['organization'] ?? null;
            $partylistName = $row['political_affiliation'] ?? ($row['partylist'] ?? null);

            if (!$fullName && !$email) return null;

            // Check for duplication in existing data
            $isDuplicate = false;
            if ($email) {
                $isDuplicate = User::where('email', $email)->whereHas('candidacies')->exists();
            }

            $orgId = null;
            if ($orgName) {
                $org = Organization::where('name', 'LIKE', trim($orgName))->first();
                if ($org) $orgId = $org->id;
            }

            $partylistId = null;
            if ($partylistName) {
                $pl = Partylist::where('name', 'LIKE', trim($partylistName))->first();
                if ($pl) $partylistId = $pl->id;
            }

            return [
                'index' => $index,
                'full_name' => $fullName,
                'email' => $email,
                'organization' => $orgName,
                'organization_id' => $orgId,
                'political_affiliation' => $partylistName,
                'partylist_id' => $partylistId,
                'designated_position' => $row['designated_position'] ?? null,
                'platform_statement' => $row['platform_statement'] ?? ($row['platform'] ?? null),
                'profile_photo' => $row['profile_photo'] ?? ($row['photo'] ?? null),
                'is_duplicate' => $isDuplicate,
                'status' => $isDuplicate ? 'Duplicate' : 'Clear'
            ];
        })->filter()->values();

        $storedPath = $file->store('imports');

        return response()->json([
            'success' => true,
            'data' => $data,
            'importPath' => $storedPath,
            'organizations' => Organization::all(['id', 'name']),
            'partylists' => Partylist::all(['id', 'name', 'organization_id'])
        ]);
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'import_path' => 'required|string',
            'election_id' => 'nullable|exists:elections,id',
            'overrides' => 'nullable|array'
        ]);

        $path = $request->input('import_path');
        $electionId = $request->input('election_id');
        $overrides = $request->input('overrides', []);

        if (!Storage::disk('local')->exists($path)) {
            return response()->json(['success' => false, 'message' => 'Import file not found.'], 422);
        }

        $fullPath = Storage::disk('local')->path($path);
        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        $readerType = null;

        if ($extension === 'tsv') {
            $readerType = \Maatwebsite\Excel\Excel::TSV;
        } elseif ($extension === 'csv') {
            $readerType = \Maatwebsite\Excel\Excel::CSV;
        } elseif ($extension === 'xml') {
            $readerType = \Maatwebsite\Excel\Excel::XML;
        }

        try {
            $sheets = Excel::toCollection(new CandidateImport(), $fullPath, null, $readerType);
            $rows = $sheets->first() ?? collect();
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error reading stored file.'], 422);
        }

        $created = 0;
        $skipped = 0;

        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                $fullName = $row['full_name'] ?? ($row['name'] ?? null);
                $email = $row['email'] ?? null;

                if (!$fullName || !$email) {
                    $skipped++;
                    continue;
                }

                // Check duplication
                $existingUser = User::where('email', $email)->first();
                if ($existingUser && Candidate::where('user_id', $existingUser->id)->exists()) {
                    $skipped++;
                    continue;
                }

                $orgId = $overrides[$index]['organization_id'] ?? null;
                if (!$orgId) {
                    $orgName = $row['organization'] ?? null;
                    if ($orgName) {
                        $org = Organization::where('name', 'LIKE', trim($orgName))->first();
                        if ($org) $orgId = $org->id;
                    }
                }

                $partylistId = $overrides[$index]['partylist_id'] ?? null;
                if (!$partylistId) {
                    $partylistName = $row['political_affiliation'] ?? ($row['partylist'] ?? null);
                    if ($partylistName) {
                        $pl = Partylist::where('name', 'LIKE', trim($partylistName))->first();
                        if ($pl) $partylistId = $pl->id;
                    }
                }

                if (!$existingUser) {
                    // Create user as voter first? Or just a user.
                    $existingUser = User::create([
                        'name' => $fullName,
                        'email' => $email,
                        'password' => Hash::make(Str::random(12)),
                    ]);

                    // Attempt to assign role
                    try {
                        if (method_exists($existingUser, 'assignRole')) {
                            $existingUser->assignRole('voter');
                        } else if (Schema::hasTable('roles')) {
                            $voterRole = DB::table('roles')->where('name', 'voter')->first();
                            if ($voterRole) {
                                DB::table('model_has_roles')->insert([
                                    'role_id' => $voterRole->id,
                                    'model_type' => get_class($existingUser),
                                    'model_id' => $existingUser->id
                                ]);
                            }
                        }
                    } catch (\Exception $e) {
                        Log::warning('Could not assign role to imported candidate: ' . $e->getMessage());
                    }
                }

                $posTitle = $row['designated_position'] ?? null;
                $posId = null;
                if ($posTitle) {
                    $pos = Position::where('title', 'LIKE', trim($posTitle))
                        ->when($electionId, function($q) use ($electionId) {
                            return $q->where('election_id', $electionId);
                        })->first();
                    $posId = $pos ? $pos->id : null;
                }

                $nameParts = explode(' ', trim($fullName));
                $lastName = count($nameParts) > 1 ? array_pop($nameParts) : '';
                $firstName = implode(' ', $nameParts);
                if (empty($firstName)) {
                    $firstName = $lastName;
                    $lastName = '';
                }

                Candidate::create([
                    'user_id' => $existingUser->id,
                    'election_id' => $electionId,
                    'organization_id' => $orgId,
                    'partylist_id' => $partylistId,
                    'position_id' => $posId,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'name' => $fullName,
                    'platform' => $row['platform_statement'] ?? ($row['platform'] ?? null),
                    'photo' => $row['profile_photo'] ?? ($row['photo'] ?? null),
                    'status' => 'active',
                    'created_by' => auth()->id(),
                ]);
                $created++;
            }
            DB::commit();
            Storage::disk('local')->delete($path);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error importing data: ' . $e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully imported $created candidates. Skipped $skipped duplicates or invalid rows."
        ]);
    }


    /**
     * Automation Mode:
     * Attach existing candidates from a partylist to an election
     * WITHOUT creating duplicates
     */
    public function attachPartylistCandidatesToElection(
        Request $request,
        Election $election
    ): JsonResponse {
        if (!$this->canUserManageElection($election)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'partylist_id' => 'required|exists:partylists,id',
        ]);

        // ✅ THIS IS THE FIX
        $updated = Candidate::where('partylist_id', $validated['partylist_id'])
            ->where('organization_id', $election->organization_id)
            ->whereNull('election_id')   // prevent duplicates
            ->update([
                'election_id' => $election->id
            ]);

        return response()->json([
            'success' => true,
            'attached_candidates' => $updated
        ]);
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
