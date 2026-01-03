<?php

namespace App\Http\Controllers\Elections;

use App\Http\Controllers\Controller;
use App\Models\Election;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class store extends Controller
{
    public function __invoke(Request $request)
    {
        // Log incoming request for debugging
        Log::info('Election creation attempt', [
            'user_id' => auth()->id(),
            'request_data' => $request->except(['_token'])
        ]);

        try {
            // Validate the request
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'organization_id' => 'required|exists:organizations,id',
                'description' => 'nullable|string',
                'voting_start' => 'required|date|after:now',
                'voting_end' => 'required|date|after:voting_start',
                'positions' => 'required|array|min:1',
                'positions.*.name' => 'required|string|max:255',
                'positions.*.candidates' => 'required|array|min:1',
                'positions.*.candidates.*' => 'required|string|max:255',
                'enable_geo_location' => 'nullable|boolean',
                'geo_latitude' => 'nullable|numeric|between:-90,90',
                'geo_longitude' => 'nullable|numeric|between:-180,180',
                'geo_radius' => 'nullable|numeric|min:0',
                'allowed_email_domain' => 'nullable|string|max:255',
                'registration_deadline' => 'nullable|date|before:voting_start',
                'max_votes_per_voter' => 'nullable|integer|min:1',
            ]);

            DB::beginTransaction();

            // Generate unique election code
            $code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));

            // Create the election
            $election = Election::create([
                'title' => $validated['title'],
                'organization_id' => $validated['organization_id'],
                'description' => $validated['description'] ?? null,
                'start_date' => $validated['voting_start'],
                'end_date' => $validated['voting_end'],
                'status' => 'draft',
                'code' => $code,
                'created_by' => auth()->id(),
                'geo_latitude' => $request->boolean('enable_geo_location') ? $validated['geo_latitude'] ?? null : null,
                'geo_longitude' => $request->boolean('enable_geo_location') ? $validated['geo_longitude'] ?? null : null,
                'geo_radius_meters' => $request->boolean('enable_geo_location') ? $validated['geo_radius'] ?? null : null,
                'require_geo_verification' => $request->boolean('enable_geo_location'),
            ]);

            Log::info('Election created successfully', [
                'election_id' => $election->id,
                'title' => $election->title,
                'code' => $code
            ]);

            // Create positions and their candidates
            foreach ($validated['positions'] as $index => $positionData) {
                $position = $election->positions()->create([
                    'title' => $positionData['name'],
                    'order' => $index + 1,
                    'max_selection' => 1,
                ]);

                Log::info('Position created', [
                    'position_id' => $position->id,
                    'title' => $position->title,
                    'election_id' => $election->id
                ]);

                foreach ($positionData['candidates'] as $candidateIndex => $candidateName) {
                    if (!empty(trim($candidateName))) {
                        $candidate = $position->candidates()->create([
                            'candidate_name' => trim($candidateName), // Change 'name' to your actual column name
                            'election_id' => $election->id,
                            'position_id' => $position->id,
                            'order' => $candidateIndex + 1,
                            'created_by' => auth()->id(),
                        ]);

                        Log::info('Candidate created', [
                            'candidate_id' => $candidate->id,
                            'name' => $candidate->candidate_name, // Update this too
                            'position_id' => $position->id
                        ]);
                    }
                }
            }

            DB::commit();

            Log::info('Election creation completed successfully', [
                'election_id' => $election->id
            ]);

            // Generate registration URL
            $registrationUrl = route('elections.register', $code);

            // Return JSON for AJAX requests
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

            // Fallback redirect for non-AJAX requests
            return redirect()
                ->route('admin.elections.index')
                ->with('success', 'Election created successfully!');

        } catch (ValidationException $e) {
            DB::rollBack();

            Log::warning('Validation failed during election creation', [
                'errors' => $e->errors()
            ]);

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
