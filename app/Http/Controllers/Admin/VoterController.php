<?php
// File: `app/Http/Controllers/Admin/VoterController.php`
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\VoterImport;
use App\Models\User;
use App\Models\Voter;
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
        $query = \App\Models\Voter::with(['election']);

        if ($request->has('q')) {
            $q = $request->q;
            $query->where(function($sq) use ($q) {
                $sq->where('name', 'like', "%$q%")
                   ->orWhere('email', 'like', "%$q%")
                   ->orWhere('student_id', 'like', "%$q%");
            });
        }

        if ($request->has('election_id') && $request->election_id !== 'all') {
            $query->where('election_id', $request->election_id);
        }

        $voters = $query->latest()->paginate(15);
        $forms = \App\Models\Election::where('created_by', auth()->id())->get();

        return view('main-admin.voters', compact('voters', 'forms'));
    }

    public function create()
    {
        $forms = \App\Models\Election::where('created_by', auth()->id())->get();
        return view('main-admin.voter.voter-create', compact('forms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'student_id' => 'nullable|string|max:50',
            'form_id' => 'required|exists:elections,id',
            'registration_status' => 'required|in:approved,pending,declined'
        ]);

        try {
            DB::beginTransaction();

            \App\Models\Voter::create([
                'name' => $validated['full_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'student_id' => $validated['student_id'],
                'election_id' => $validated['form_id'],
                'registration_status' => $validated['registration_status'],
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]);

            DB::commit();

            if ($request->ajax()) {
                return response()->json(['success' => true]);
            }

            return redirect()->route('admin.voters.index')
                ->with('success', 'Voter created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['success' => false, 'errors' => ['general' => [$e->getMessage()]]], 422);
            }
            return back()->withErrors(['general' => 'An error occurred while creating the voter'])
                ->withInput();
        }
    }

    public function show($id)
    {
        $voter = \App\Models\Voter::with(['election'])->findOrFail($id);
        return view('main-admin.voter.view', compact('voter'));
    }

    public function edit($id)
    {
        $voter = \App\Models\Voter::with(['election'])->findOrFail($id);
        $forms = \App\Models\Election::where('created_by', auth()->id())->get();
        return view('main-admin.voter.edit', compact('voter', 'forms'));
    }

    public function update(Request $request, $id)
    {
        $voter = \App\Models\Voter::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:voters,email,' . $id,
            'student_id' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
            'election_id' => 'required|exists:elections,id',
            'registration_status' => 'required|in:approved,pending,declined'
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
        $voter = Voter::findOrFail($id);
        $voter->registration_status = 'approved';
        $voter->save();

        return back()->with('success', 'Voter approved.');
    }

    public function decline($id)
    {
        $voter = Voter::findOrFail($id);
        $voter->registration_status = 'declined';
        $voter->save();

        return back()->with('success', 'Voter declined.');
    }

    public function search(Request $request)
    {
        $queryTerm = $request->get('q', '');
        $election_id = $request->get('election_id', '');

        $query = Voter::with(['election']);

        $query->when($queryTerm, function ($q) use ($queryTerm) {
            return $q->where(function ($subQuery) use ($queryTerm) {
                $subQuery->where('name', 'like', "%{$queryTerm}%")
                    ->orWhere('email', 'like', "%{$queryTerm}%")
                    ->orWhere('student_id', 'like', "%{$queryTerm}%");
            });
        })
            ->when($election_id && $election_id !== 'all', function ($q) use ($election_id) {
                return $q->where('election_id', $election_id);
            });

        $voters = $query->orderBy('created_at', 'desc')->paginate(15);
        $forms = \App\Models\Election::where('created_by', auth()->id())->get();

        if ($request->wantsJson()) {
            return response()->json(['voters' => $voters]);
        }

        return view('main-admin.voters', compact('voters', 'forms'));
    }

    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');

        $query = Voter::with(['election']);
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
                'ID', 'Name', 'Email', 'Student ID', 'Election', 'Status', 'Created At'
            ]);

            foreach ($voters as $voter) {
                fputcsv($file, [
                    $voter->id,
                    $voter->name,
                    $voter->email,
                    $voter->student_id,
                    $voter->election->title ?? 'N/A',
                    $voter->registration_status,
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
