<?php
// File: `app/Http/Controllers/Admin/VoterController.php`
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\VoterImport;
use App\Models\User;
use App\Models\Election;
use App\Models\Voter;
use App\Mail\VoterImportedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\RateLimiter;

class VoterController extends Controller
{
    public function resetLoginAttempts(Voter $voter)
    {
        $user = $voter->user;
        if ($user) {
            $user->update([
                'failed_login_attempts' => 0,
                'locked_until' => null,
                'is_permanently_blocked' => false
            ]);

            $key = Str::transliterate(Str::lower($user->email));
            RateLimiter::clear($key);
        }

        return back()->with('success', "Login attempts for {$voter->name} have been reset.");
    }

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
    private function getView($name)
    {
        if (auth()->check() && auth()->user()->hasRole('admin') && !auth()->user()->hasRole('super-admin')) {
            return "admin.$name";
        }
        return "main-admin.$name";
    }

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
        $forms = \App\Models\Election::where('created_by', auth()->id())
            ->whereIn('status', ['active', 'draft'])
            ->get();

        return view($this->getView('voters'), compact('voters', 'forms'));
    }

    public function create()
    {
        $forms = \App\Models\Election::where('created_by', auth()->id())
            ->whereIn('status', ['active', 'draft'])
            ->get();
        return view($this->getView('voter.voter-create'), compact('forms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'voter_code' => 'required|string|max:50',
            'form_id' => 'required|exists:elections,id',
            'registration_status' => 'required|in:approved,pending,declined'
        ]);

        $email = trim(strtolower($validated['email']));

        // Check for duplicates in this election
        $duplicate = \App\Models\Voter::where('election_id', $validated['form_id'])
            ->where(function($q) use ($email, $validated) {
                $q->where('email', $email)
                  ->orWhere('student_id', $validated['voter_code']);
            })
            ->first();

        if ($duplicate) {
            return response()->json(['success' => false, 'errors' => ['general' => ['A voter with this email or Voter ID already exists in this election.']]], 422);
        }

        try {
            DB::beginTransaction();

            $user = User::where('email', $email)->first();

            if (!$user) {
                $nameParts = explode(' ', trim($validated['full_name']));
                $lastName = count($nameParts) > 1 ? array_pop($nameParts) : '';
                $firstName = implode(' ', $nameParts);
                if (empty($firstName)) { $firstName = $lastName; $lastName = ''; }

                // Generate Unique Key / Temporary Password
                $tempPassword = Str::password(10, true, true, true, false);

                $user = User::create([
                    'name' => $validated['full_name'],
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'password' => $tempPassword,
                    'role' => 'voter',
                    'is_active' => true,
                    'is_approved' => ($validated['registration_status'] === 'approved'),
                ]);
            } else {
                // Generate Unique Key / Temporary Password for existing user
                $tempPassword = Str::password(10, true, true, true, false);

                if ($validated['registration_status'] === 'approved') {
                    $user->update([
                        'is_approved' => true,
                        'password' => $tempPassword
                    ]);
                }
            }

            $voter = \App\Models\Voter::create([
                'name' => $validated['full_name'],
                'email' => $email,
                'phone' => $validated['phone'] ?? null,
                'student_id' => $validated['voter_code'] ?? null,
                'election_id' => $validated['form_id'],
                'registration_status' => $validated['registration_status'],
                'password' => $user->password,
                'user_id' => $user->id,
            ]);

            if ($validated['registration_status'] === 'approved') {
                $this->assignVoterRole($user);
            }

            // Send Email Notification
            try {
                $election = \App\Models\Election::findOrFail($validated['form_id']);
                Mail::to($email)->send(new VoterImportedMail($voter, $election, $tempPassword));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send voter email to {$email}: " . $e->getMessage());
            }

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
        return view($this->getView('voter.view'), compact('voter'));
    }

    public function edit(string $id)
    {
        $voter = \App\Models\Voter::with(['election'])->findOrFail($id);
        $forms = \App\Models\Election::where('created_by', auth()->id())
            ->whereIn('status', ['active', 'draft'])
            ->get();
        return view($this->getView('voter.edit'), compact('voter', 'forms'));
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

        $email = trim(strtolower($validated['email']));

        try {
            DB::beginTransaction();

            $voter->update([
                'name' => $validated['name'],
                'email' => $email,
                'student_id' => $validated['student_id'],
                'phone' => $validated['phone'],
                'election_id' => $validated['election_id'],
                'registration_status' => $validated['registration_status'],
            ]);

            // Sync with User record if exists
            if ($voter->user_id) {
                $user = User::find($voter->user_id);
                if ($user) {
                    $user->update([
                        'name' => $validated['name'],
                        'email' => $email,
                        'is_approved' => ($validated['registration_status'] === 'approved')
                    ]);
                }
            }

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

        // Also approve the user account if linked
        if ($voter->user_id) {
            User::where('id', $voter->user_id)->update(['is_approved' => true]);
        }

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
        $forms = \App\Models\Election::where('created_by', auth()->id())
            ->whereIn('status', ['active', 'draft'])
            ->get();

        if ($request->wantsJson()) {
            return response()->json(['voters' => $voters]);
        }

        return view($this->getView('voters'), compact('voters', 'forms'));
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
            if (ob_get_level() > 0) ob_end_clean();
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
            $sheets = Excel::toCollection(new VoterImport(), $file, null, $readerType);
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

            // Check if voter already exists
            $isDuplicate = \App\Models\Voter::where('email', $email)->exists();

            return [
                'name' => $name,
                'email' => $email,
                'student_id' => $studentId,
                'phone' => $row['phone'] ?? ($row['phone_number'] ?? ($row['phone number'] ?? null)),
                'is_duplicate' => $isDuplicate,
                'status' => $isDuplicate ? 'Duplicate' : 'Clear'
            ];
        })->filter()->values();

        $storedPath = $file->store('imports');

        return response()->json([
            'success' => true,
            'data' => $voters,
            'importPath' => $storedPath
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
            'registration_status' => 'required|in:approved,pending,declined'
        ]);

        $path = $request->input('import_path');
        $electionId = $request->input('election_id');
        $registrationStatus = $request->input('registration_status', 'approved');
        $election = Election::findOrFail($electionId);

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
            $sheets = Excel::toCollection(new VoterImport(), $fullPath, null, $readerType);
            $rows = $sheets->first() ?? collect();
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error reading stored file.'], 422);
        }

        $created = 0;
        $skipped = 0;
        DB::beginTransaction();
        try {
            foreach ($rows as $row) {
                $email = trim(strtolower($row['email'] ?? ($row['email_address'] ?? ($row['email address'] ?? null))));
                $name = $row['full_name'] ?? ($row['name'] ?? ($row['full name'] ?? null));

                if (!$email || !$name) {
                    $skipped++;
                    continue;
                }

                $studentId = $row['id'] ?? ($row['student_id'] ?? ($row['id_number'] ?? ($row['employee_id'] ?? ($row['student id'] ?? null))));

                // Check if voter already exists for THIS election
                $existingVoter = \App\Models\Voter::where('email', $email)
                    ->where('election_id', $electionId)
                    ->first();
                if ($existingVoter) {
                    $skipped++;
                    continue;
                }

                $user = User::where('email', $email)->first();

                // Generate Unique Key / Temporary Password
                // Must include: Alphabets, Numbers, and Special Characters
                $tempPassword = Str::password(10, true, true, true, false);
                $hashedPassword = Hash::make($tempPassword);

                $data = [
                    'name' => $name,
                    'email' => $email,
                    'student_id' => $studentId,
                    'phone' => $row['phone'] ?? ($row['phone_number'] ?? ($row['phone number'] ?? null)),
                    'password' => $hashedPassword,
                    'election_id' => $electionId,
                    'registration_status' => $registrationStatus,
                    'user_id' => $user->id ?? null,
                ];

                $voter = \App\Models\Voter::create($data);

                // If approved, create user account if doesn't exist
                if ($registrationStatus === 'approved') {
                    if (!$user) {
                        $nameParts = explode(' ', trim($name));
                        $lastName = count($nameParts) > 1 ? array_pop($nameParts) : '';
                        $firstName = implode(' ', $nameParts);
                        if (empty($firstName)) {
                            $firstName = $lastName;
                            $lastName = '';
                        }

                        $user = User::create([
                            'name' => $name,
                            'first_name' => $firstName,
                            'last_name' => $lastName,
                            'email' => $email,
                            'password' => $tempPassword, // Model cast handles hashing
                            'role' => 'voter',
                            'is_active' => true,
                            'is_approved' => true,
                        ]);
                    } else {
                        // User exists, ensure they are approved and update password for the election
                        $user->update([
                            'is_approved' => true,
                            'password' => $tempPassword
                        ]);
                    }

                    $voter->update(['user_id' => $user->id]);
                    $this->assignVoterRole($user);
                }

                // Always send email if it's newly created (regardless of approved/pending status,
                // but the prompt says "immediately after an Admin or Super Admin successfully imports/uploads a voter data file"
                // and "send an individual email to every voter's registered email address found in the dataset")
                // Wait, if it's pending, they can't login yet?
                // Usually, imported voters are "approved" by default in this flow.
                // The current code only sends if approved. I'll keep it consistent or follow the prompt's "every voter" strictly.
                // Re-reading prompt: "send an individual email to every voter's registered email address found in the dataset."

                try {
                    Mail::to($email)->send(new VoterImportedMail($voter, $election, $tempPassword));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Failed to send voter email to {$email}: " . $e->getMessage());
                }

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
            'message' => "Successfully imported $created voters. Skipped $skipped duplicates or invalid rows."
        ]);
    }
}
