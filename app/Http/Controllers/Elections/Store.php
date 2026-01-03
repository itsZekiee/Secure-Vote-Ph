<?php

namespace App\Http\Controllers\Elections;

use App\Http\Controllers\Controller;
use App\Models\Election;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class Store extends Controller
{
    public function __invoke(Request $request)
    {
        Log::info('Election creation attempt', [
            'user_id' => auth()->id(),
            'request_data' => $request->except(['_token'])
        ]);

        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'voting_start' => 'required|date',
                'voting_end' => 'required|date|after:voting_start',
                'registration_deadline' => 'nullable|date|before:voting_start',
                'positions' => 'required|array|min:1',
                'positions.*.name' => 'required|string|max:255',
                'positions.*.candidates' => 'required|array|min:1',
                'positions.*.candidates.*' => 'required|string|max:255',
            ]);

            DB::beginTransaction();

            $code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));

            $election = Election::create([
                'title' => $validated['title'],
                'start_date' => $validated['voting_start'],
                'end_date' => $validated['voting_end'],
                'registration_deadline' => $validated['registration_deadline'] ?? $validated['voting_start'],
                'status' => 'draft',
                'code' => $code,
                'created_by' => auth()->id(),
            ]);

            Log::info('Election created successfully', [
                'election_id' => $election->id,
                'title' => $election->title,
                'code' => $code
            ]);

            foreach ($validated['positions'] as $index => $positionData) {
                $position = $election->positions()->create([
                    'title' => $positionData['name'],
                    'order' => $index + 1,
                    'max_selection' => 1,
                ]);

                foreach ($positionData['candidates'] as $candidateIndex => $candidateName) {
                    if (!empty(trim($candidateName))) {
                        $nameParts = preg_split('/\s+/', trim($candidateName));

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

                        $position->candidates()->create([
                            'first_name' => $firstName,
                            'middle_name' => $middleName,
                            'last_name' => $lastName,
                            'election_id' => $election->id,
                            'position_id' => $position->id,
                            'partylist_id' => null,
                            'photo' => null,
                            'platform' => null,
                            'order' => $candidateIndex + 1,
                            'created_by' => auth()->id(),
                        ]);
                    }
                }
            }

            DB::commit();

            $registrationUrl = url('/elections/register/' . $code);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Election created successfully',
                    'election' => [
                        'id' => $election->id,
                        'code' => $code,
                        'title' => $election->title
                    ],
                    'registration_url' => $registrationUrl
                ]);
            }

            return redirect()
                ->route('admin.elections.index')
                ->with('success', 'Election created successfully!');

        } catch (ValidationException $e) {
            DB::rollBack();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }

            throw $e;

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Election creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create election: ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create election: ' . $e->getMessage()]);
        }
    }
}
