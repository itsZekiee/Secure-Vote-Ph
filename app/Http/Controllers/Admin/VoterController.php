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
                'phone' => $validated['phone'] ?? null,
                'student_id' => $validated['student_id'] ?? null,
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

    public function show(string $id)
    {
        $voter = \App\Models\Voter::with(['election'])->findOrFail($id);
        return view('main-admin.voter.view', compact('voter'));
    }

    public function edit(string $id)
    {
        $voter = \App\Models\Voter::with(['election'])->findOrFail($id);
        $forms = \App\Models\Election::where('created_by', auth()->id())->get();
        return view('main-admin.voter.edit', compact('voter', 'forms'));
    }

    public function update(Request $request, string $id)
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

    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $voter = Voter::findOrFail($id);

            if ($voter->votes()->count() > 0) {
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

    public function approve(string $id)
    {
        $voter = Voter::findOrFail($id);
        $voter->registration_status = 'approved';
        $voter->save();

        return back()->with('success', 'Voter approved.');
    }

    public function decline(string $id)
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

        try {
            $sheets = Excel::toCollection(new VoterImport(), $file);
            $rows = $sheets->first() ?? collect();
        } catch (\Throwable $e) {
            return back()->withErrors(['file' => 'Error reading file: ' . $e->getMessage()]);
        }

        if ($rows->isEmpty()) {
            return back()->withErrors(['file' => 'The uploaded file is empty or has no data.']);
        }

        $voters = $rows->map(function ($row) {
            $studentId = $row['id'] ?? ($row['student_id'] ?? ($row['id_number'] ?? ($row['employee_id'] ?? ($row['student id'] ?? null))));
            $email = $row['email'] ?? ($row['email_address'] ?? ($row['email address'] ?? null));
            $name = $row['full_name'] ?? ($row['name'] ?? ($row['full name'] ?? null));

            if (!$email && !$name) return null;

            return (object) [
                'name' => $name,
                'email' => $email,
                'student_id' => $studentId,
                'phone' => $row['phone'] ?? ($row['phone_number'] ?? ($row['phone number'] ?? null)),
                'registration_status' => 'approved',
                'created_at' => now(),
            ];
        })->filter();

        $storedPath = $file->store('imports');

        // Fetch forms/elections for selection
        $forms = \App\Models\Election::where('created_by', auth()->id())->get();

        // return view; the blade will handle collection vs paginator
        return view('main-admin.voter.show', [
            'voters' => $voters,
            'importPath' => $storedPath,
            'forms' => $forms
        ]);
    }

    /**
     * Persist stored import file: create users for each valid row.
     */
    public function importStore(Request $request)
    {
        $request->validate([
            'import_path' => 'required|string',
            'election_id' => 'required|exists:elections,id',
            'registration_status' => 'required|in:approved,pending,declined',
            'temp_password' => 'required|string|min:4'
        ]);

        $path = $request->input('import_path');
        $electionId = $request->input('election_id');
        $registrationStatus = $request->input('registration_status', 'approved');
        $tempPassword = $request->input('temp_password');
        $hashedPassword = \Illuminate\Support\Facades\Hash::make($tempPassword);

        if (!Storage::disk('local')->exists($path)) {
            return back()->withErrors(['file' => 'Import file not found. Please re-upload.']);
        }

        $fullPath = Storage::disk('local')->path($path);

        try {
            $sheets = Excel::toCollection(new VoterImport(), $fullPath);
            $rows = $sheets->first() ?? collect();
        } catch (\Throwable $e) {
            return back()->withErrors(['file' => 'Error reading stored file: ' . $e->getMessage()]);
        }

        $created = 0;
        $skipped = 0;
        DB::beginTransaction();
        try {
            foreach ($rows as $row) {
                $email = $row['email'] ?? ($row['email_address'] ?? ($row['email address'] ?? null));
                $name = $row['full_name'] ?? ($row['name'] ?? ($row['full name'] ?? null));

                if (!$email || !$name) {
                    $skipped++;
                    continue;
                }

                $studentId = $row['id'] ?? ($row['student_id'] ?? ($row['id_number'] ?? ($row['employee_id'] ?? ($row['student id'] ?? null))));

                // Check if voter already exists
                $existingVoter = \App\Models\Voter::where('email', $email)->first();
                if ($existingVoter) {
                    $skipped++;
                    continue;
                }

                $data = [
                    'name' => $name,
                    'email' => $email,
                    'student_id' => $studentId,
                    'phone' => $row['phone'] ?? ($row['phone_number'] ?? ($row['phone number'] ?? null)),
                    'password' => $hashedPassword,
                    'election_id' => $electionId,
                    'registration_status' => $registrationStatus,
                ];

                \App\Models\Voter::create($data);

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

        return redirect()->route('admin.voters.index')
            ->with('success', "Imported {$created} voters. Skipped {$skipped} invalid or duplicate rows.")
            ->with('temp_password_display', $tempPassword);
    }
}
