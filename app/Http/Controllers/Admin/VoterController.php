<?php
// File: `app/Http/Controllers/Admin/VoterController.php`
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\VoterImport;
use App\Models\User;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class VoterController extends Controller
{
    /**
     * Apply voter scope to a query builder and return it.
     */
    protected function applyVoterScope(Builder $query): Builder
    {
        if (Schema::hasColumn('users', 'role')) {
            return $query->where('role', 'voter');
        }

        if (Schema::hasColumn('users', 'user_type')) {
            return $query->where('user_type', 'voter');
        }

        if (Schema::hasColumn('users', 'type')) {
            return $query->where('type', 'voter');
        }

        if (Schema::hasColumn('users', 'is_voter')) {
            return $query->where('is_voter', true);
        }

        $userInstance = new User();
        if (method_exists($userInstance, 'roles')) {
            return $query->whereHas('roles', function (Builder $q) {
                $q->where('name', 'voter');
            });
        }

        // Fallback: no voters
        return $query->whereRaw('0 = 1');
    }

    /**
     * Assign voter role/flag to a user using available mechanics.
     */
    protected function assignVoterRole(User $user): void
    {
        if (Schema::hasColumn('users', 'role')) {
            $user->role = 'voter';
            $user->save();
            return;
        }

        if (Schema::hasColumn('users', 'is_voter')) {
            $user->is_voter = true;
            $user->save();
            return;
        }

        if (method_exists($user, 'assignRole')) {
            $user->assignRole('voter');
            return;
        }

        if (method_exists($user, 'roles')) {
            try {
                $roleModel = config('permission.models.role') ?? null;
                if ($roleModel && class_exists($roleModel)) {
                    $role = $roleModel::where('name', 'voter')->first();
                    if ($role) {
                        $user->roles()->attach($role->id);
                    }
                }
            } catch (\Throwable $e) {
                // ignore
            }
        }
    }

    /**
     * Display a listing of voters.
     */
    public function index(Request $request)
    {
        $query = User::with(['organization'])->withCount(['votes']);
        $query = $this->applyVoterScope($query);

        $voters = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('main-admin.voters', compact('voters'));
    }


    public function create()
    {
        // Prefer `status = 'active'`, fall back to `is_active = 1`, otherwise return all
        if (Schema::hasColumn('organizations', 'status')) {
            $organizations = Organization::where('status', 'active')->get();
        } elseif (Schema::hasColumn('organizations', 'is_active')) {
            $organizations = Organization::where('is_active', 1)->get();
        } else {
            $organizations = Organization::all();
        }

        return view('main-admin.voter.voter-create', compact('organizations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'student_id' => 'nullable|string|unique:users,student_id',
            'organization_id' => 'required|exists:organizations,id',
            'is_active' => 'boolean'
        ]);

        $validated['password'] = bcrypt('password');

        if (Schema::hasColumn('users', 'role')) {
            $validated['role'] = 'voter';
        }

        if (Schema::hasColumn('users', 'is_voter')) {
            $validated['is_voter'] = true;
        }

        try {
            DB::beginTransaction();

            $voter = User::create($validated);

            $this->assignVoterRole($voter);

            DB::commit();

            return redirect()->route('admin.voters.index')
                ->with('success', 'Voter created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['general' => 'An error occurred while creating the voter'])
                ->withInput();
        }
    }

    public function show(User $voter)
    {
        $voter->load(['organization', 'votes']);
        return view('main-admin.voters.show', compact('voter'));
    }

    public function edit(User $voter)
    {
        $organizations = Organization::where('status', 'active')->get();
        return view('main-admin.voters.edit', compact('voter', 'organizations'));
    }

    public function update(Request $request, User $voter)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $voter->id,
            'student_id' => 'nullable|string|unique:users,student_id,' . $voter->id,
            'organization_id' => 'required|exists:organizations,id',
            'is_active' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            $voter->update($validated);

            DB::commit();

            return redirect()->route('admin.voters.index')
                ->with('success', 'Voter updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['general' => 'An error occurred while updating the voter'])
                ->withInput();
        }
    }

    public function destroy(User $voter)
    {
        try {
            DB::beginTransaction();

            if (method_exists($voter, 'votes') && $voter->votes()->count() > 0) {
                return back()->withErrors(['general' => 'Cannot delete voter with existing votes']);
            }

            $voter->delete();

            DB::commit();

            return redirect()->route('admin.voters.index')
                ->with('success', 'Voter deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['general' => 'An error occurred while deleting the voter']);
        }
    }

    public function approve($id)
    {
        $user = User::findOrFail($id);
        $user->registration_status = 'approved';
        $user->save();

        return back()->with('success', 'Voter approved.');
    }

    public function decline($id)
    {
        $user = User::findOrFail($id);
        $user->registration_status = 'declined';
        $user->save();

        return back()->with('success', 'Voter declined.');
    }

    public function search(Request $request)
    {
        $queryTerm = $request->get('q', '');
        $organization_id = $request->get('organization_id', '');
        $is_active = $request->get('is_active', '');

        $query = User::with(['organization'])->withCount(['votes']);
        $query = $this->applyVoterScope($query);

        $query->when($queryTerm, function ($q) use ($queryTerm) {
            return $q->where(function ($subQuery) use ($queryTerm) {
                $subQuery->where('name', 'like', "%{$queryTerm}%")
                    ->orWhere('email', 'like', "%{$queryTerm}%")
                    ->orWhere('student_id', 'like', "%{$queryTerm}%");
            });
        })
            ->when($organization_id, function ($q) use ($organization_id) {
                return $q->where('organization_id', $organization_id);
            })
            ->when($is_active !== '', function ($q) use ($is_active) {
                return $q->where('is_active', $is_active);
            });

        $voters = $query->orderBy('created_at', 'desc')->paginate(15);

        if ($request->wantsJson()) {
            return response()->json(['voters' => $voters]);
        }

        return view('main-admin.voters', compact('voters'));
    }

    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');

        $query = User::with(['organization'])->withCount(['votes']);
        $query = $this->applyVoterScope($query);
        $voters = $query->orderBy('created_at', 'desc')->get();

        if ($format === 'json') {
            return response()->json(['voters' => $voters]);
        }

        $filename = 'voters_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($voters) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID', 'Name', 'Email', 'Student ID', 'Organization', 'Status', 'Votes Cast', 'Created At'
            ]);

            foreach ($voters as $voter) {
                fputcsv($file, [
                    $voter->id,
                    $voter->name,
                    $voter->email,
                    $voter->student_id,
                    $voter->organization->name ?? 'N/A',
                    $voter->is_active ? 'Active' : 'Inactive',
                    $voter->votes_count ?? 0,
                    $voter->created_at ? $voter->created_at->format('Y-m-d H:i:s') : ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Preview uploaded file and return the main view with preview rows.
     * Stores the uploaded file under storage/app/imports for later processing.
     */
    public function importPreview(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:51200',
        ]);

        $file = $request->file('file');

        $sheets = Excel::toCollection(new VoterImport(), $file);
        $rows = $sheets->first() ?? collect();

        $voters = $rows->map(function ($row) {
            return (object) [
                'name' => $row['name'] ?? ($row['full_name'] ?? null),
                'email' => $row['email'] ?? null,
                'student_id' => $row['student_id'] ?? ($row['id_number'] ?? null),
                'date_of_birth' => $row['date_of_birth'] ?? ($row['dob'] ?? null),
                'phone' => $row['phone'] ?? ($row['phone_number'] ?? null),
                'registration_status' => strtolower($row['registration_status'] ?? 'pending'),
                'created_at' => $row['date_registered'] ?? ($row['created_at'] ?? null),
            ];
        });

        $storedPath = $file->store('imports');

        // return view; the blade will handle collection vs paginator
        return view('main-admin.voters', [
            'voters' => $voters,
            'importPath' => $storedPath,
        ]);
    }

    /**
     * Persist stored import file: create users for each valid row.
     */
    public function importStore(Request $request)
    {
        $request->validate([
            'import_path' => 'required|string',
        ]);

        $path = $request->input('import_path');
        $fullPath = storage_path('app/' . ltrim($path, '/'));

        if (!file_exists($fullPath)) {
            return back()->withErrors(['file' => 'Import file not found. Please re-upload.']);
        }

        $sheets = Excel::toCollection(new VoterImport(), $fullPath);
        $rows = $sheets->first() ?? collect();

        $created = 0;
        DB::beginTransaction();
        try {
            foreach ($rows as $row) {
                $email = $row['email'] ?? null;
                if (!$email || User::where('email', $email)->exists()) {
                    continue;
                }

                $data = [
                    'name' => $row['name'] ?? ($row['full_name'] ?? 'Unnamed'),
                    'email' => $email,
                    'student_id' => $row['student_id'] ?? null,
                    'phone' => $row['phone'] ?? null,
                    'is_active' => true,
                    'password' => bcrypt(Str::random(12)),
                ];

                if (!empty($row['date_registered'] ?? $row['created_at'] ?? null)) {
                    $data['created_at'] = $row['date_registered'] ?? $row['created_at'];
                }

                if (Schema::hasColumn('users', 'role')) {
                    $data['role'] = 'voter';
                }
                if (Schema::hasColumn('users', 'is_voter')) {
                    $data['is_voter'] = true;
                }

                $user = User::create($data);

                $this->assignVoterRole($user);

                $created++;
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['general' => 'Import failed: ' . $e->getMessage()]);
        }

        // Optionally delete the stored import file to avoid clutter
        try {
            Storage::delete($path);
        } catch (\Throwable $e) {
            // ignore
        }

        return redirect()->route('admin.voters.index')->with('success', "Imported {$created} voters.");
    }
}
