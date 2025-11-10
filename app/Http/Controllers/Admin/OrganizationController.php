<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class OrganizationController extends Controller
{
    /**
     * Display a listing of organizations
     */
    public function index()
    {
        $organizations = Organization::withCount(['users', 'elections'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('main-admin.organizations', compact('organizations'));
    }

    /**
     * Show the form for creating a new organization
     */
    public function create()
    {
        return view('main-admin.organizations.create');
    }

    /**
     * Store a newly created organization
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive,pending',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        $organization = Organization::create($data);

        return response()->json([
            'success' => true,
            'organization' => $organization
        ], 201);
    }

    /**
     * Display the specified organization
     */
    public function show(Organization $organization)
    {
        $organization->load(['users' => function($query) {
            $query->orderBy('created_at', 'desc');
        }, 'elections' => function($query) {
            $query->orderBy('created_at', 'desc');
        }]);

        if (request()->expectsJson()) {
            return response()->json([
                'organization' => $organization,
                'stats' => [
                    'total_users' => $organization->users()->count(),
                    'active_users' => $organization->users()->where('is_active', true)->count(),
                    'total_elections' => $organization->elections()->count(),
                    'active_elections' => $organization->elections()->where('status', 'active')->count()
                ]
            ]);
        }

        return view('main-admin.organizations.organization-view', compact('organization'));
    }

    /**
     * Show the form for editing the specified organization
     */
    public function edit(Organization $organization)
    {
        return view('main-admin.organizations.organization-edit', compact('organization'));
    }

    /**
     * Update the specified organization
     */
    public function update(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('organizations', 'name')->ignore($organization->id)
            ],
            'description' => 'nullable|string|max:1000',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive'
        ]);

        try {
            DB::beginTransaction();

            $organization->update($validated);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Organization updated successfully',
                    'organization' => $organization->fresh()->load('users')
                ]);
            }

            return redirect()->route('admin.organizations.index')
                ->with('success', 'Organization updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update organization',
                    'errors' => ['general' => 'An error occurred while updating the organization']
                ], 422);
            }

            return back()->withErrors(['general' => 'An error occurred while updating the organization'])
                ->withInput();
        }
    }

    /**
     * Remove the specified organization from storage
     */
    public function destroy(Organization $organization)
    {
        try {
            DB::beginTransaction();

            // Check if organization has users
            if ($organization->users()->count() > 0) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete organization with existing members'
                    ], 422);
                }

                return back()->withErrors(['general' => 'Cannot delete organization with existing members']);
            }

            // Check if organization has elections
            if ($organization->elections()->count() > 0) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete organization with existing elections'
                    ], 422);
                }

                return back()->withErrors(['general' => 'Cannot delete organization with existing elections']);
            }

            $organization->delete();

            DB::commit();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Organization deleted successfully'
                ]);
            }

            return redirect()->route('admin.organizations.index')
                ->with('success', 'Organization deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete organization'
                ], 422);
            }

            return back()->withErrors(['general' => 'An error occurred while deleting the organization']);
        }
    }

    /**
     * Get organization members via AJAX
     */
    public function members(Organization $organization)
    {
        $perPage = request()->get('per_page', 15);
        $search = request()->get('search', '');

        $membersQuery = $organization->users()
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc');

        if (request()->has('paginate') && request()->get('paginate') === 'true') {
            $members = $membersQuery->paginate($perPage);
            return response()->json([
                'members' => $members->items(),
                'pagination' => [
                    'current_page' => $members->currentPage(),
                    'last_page' => $members->lastPage(),
                    'total' => $members->total(),
                    'per_page' => $members->perPage(),
                    'from' => $members->firstItem(),
                    'to' => $members->lastItem()
                ]
            ]);
        }

        $members = $membersQuery->get();
        return response()->json(['members' => $members]);
    }

    /**
     * Search organizations
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $status = $request->get('status', '');
        $year = $request->get('year', '');

        $organizations = Organization::withCount(['users', 'elections'])
            ->when($query, function ($q) use ($query) {
                return $q->where(function ($subQuery) use ($query) {
                    $subQuery->where('name', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%")
                        ->orWhere('contact_email', 'like', "%{$query}%");
                });
            })
            ->when($status, function ($q) use ($status) {
                return $q->where('status', $status);
            })
            ->when($year, function ($q) use ($year) {
                return $q->whereYear('created_at', $year);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['organizations' => $organizations]);
    }

    /**
     * Export organizations data
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');

        $organizations = Organization::withCount(['users', 'elections'])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($format === 'json') {
            return response()->json(['organizations' => $organizations]);
        }

        // CSV export
        $filename = 'organizations_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($organizations) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'ID',
                'Name',
                'Description',
                'Status',
                'Contact Email',
                'Contact Phone',
                'Members Count',
                'Elections Count',
                'Created At'
            ]);

            // CSV data
            foreach ($organizations as $org) {
                fputcsv($file, [
                    $org->id,
                    $org->name,
                    $org->description,
                    $org->status,
                    $org->contact_email,
                    $org->contact_phone,
                    $org->users_count,
                    $org->elections_count,
                    $org->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
