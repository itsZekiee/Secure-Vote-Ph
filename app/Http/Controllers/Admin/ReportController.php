<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Models\Organization;
use App\Models\User;
use App\Models\Candidate;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReportController extends Controller
{
    /**
     * Display reports dashboard
     */
    public function index()
    {
        $stats = [
            'total_elections' => Election::count(),
            'active_elections' => Election::where('status', 'active')->count(),
            'total_voters' => $this->voterQuery()->count(),
            'total_candidates' => Candidate::count(),
            'total_votes' => Vote::count(),
            'organizations_count' => Organization::count(),
        ];

        $recent_elections = Election::with(['organization'])
            ->withCount(['candidates', 'votes'])
            ->latest()
            ->take(5)
            ->get();

        // Organizations for the dropdown on the reports page
        $organizations = Organization::orderBy('name')->get();

        return view('main-admin.reports', compact('stats', 'recent_elections', 'organizations'));
    }

    /**
     * Elections reports
     */
    public function elections()
    {
        $elections = Election::with(['organization'])
            ->withCount(['candidates', 'votes'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('main-admin.reports.elections', compact('elections'));
    }

    /**
     * Voters reports
     */
    public function voters()
    {
        $voters = $this->voterQuery()
            ->with(['organization'])
            ->withCount(['votes'])
            ->orderBy('created_at', 'desc')
            ->get();

        $voter_stats = [
            'total_voters' => $voters->count(),
            'active_voters' => $voters->where('is_active', true)->count(),
            'voters_with_votes' => $voters->where('votes_count', '>', 0)->count(),
        ];

        return view('main-admin.reports.voters', compact('voters', 'voter_stats'));
    }

    /**
     * Candidates reports
     */
    public function candidates()
    {
        $candidates = Candidate::with(['user', 'election', 'position', 'partylist'])
            ->withCount(['votes'])
            ->orderBy('created_at', 'desc')
            ->get();

        $candidate_stats = [
            'total_candidates' => $candidates->count(),
            'active_candidates' => $candidates->where('status', 'active')->count(),
            'candidates_with_votes' => $candidates->where('votes_count', '>', 0)->count(),
        ];

        return view('main-admin.reports.candidates', compact('candidates', 'candidate_stats'));
    }

    /**
     * Export comprehensive report
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'overview');
        $format = $request->get('format', 'csv');

        switch ($type) {
            case 'elections':
                return $this->exportElections($format);
            case 'voters':
                return $this->exportVoters($format);
            case 'candidates':
                return $this->exportCandidates($format);
            default:
                return $this->exportOverview($format);
        }
    }

    private function exportOverview($format)
    {
        $data = [
            'elections' => Election::withCount(['candidates', 'votes'])->get(),
            'voters' => $this->voterQuery()->withCount(['votes'])->get(),
            'candidates' => Candidate::with(['user', 'election'])->withCount(['votes'])->get(),
        ];

        if ($format === 'json') {
            return response()->json($data);
        }

        $filename = 'system_overview_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');

            // Elections section
            fputcsv($file, ['ELECTIONS REPORT']);
            fputcsv($file, ['ID', 'Title', 'Status', 'Candidates', 'Votes', 'Created At']);
            foreach ($data['elections'] as $election) {
                fputcsv($file, [
                    $election->id,
                    $election->title,
                    $election->status,
                    $election->candidates_count,
                    $election->votes_count,
                    $election->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['VOTERS REPORT']);
            fputcsv($file, ['ID', 'Name', 'Email', 'Status', 'Votes Cast', 'Created At']);
            foreach ($data['voters'] as $voter) {
                fputcsv($file, [
                    $voter->id,
                    $voter->name,
                    $voter->email,
                    $voter->is_active ? 'Active' : 'Inactive',
                    $voter->votes_count,
                    $voter->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportElections($format)
    {
        $elections = Election::with(['organization'])->withCount(['candidates', 'votes'])->get();

        if ($format === 'json') {
            return response()->json(['elections' => $elections]);
        }

        $filename = 'elections_report_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($elections) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID', 'Title', 'Organization', 'Status', 'Start Date', 'End Date', 'Candidates', 'Votes', 'Created At'
            ]);

            foreach ($elections as $election) {
                fputcsv($file, [
                    $election->id,
                    $election->title,
                    $election->organization->name ?? 'N/A',
                    $election->status,
                    $election->start_date,
                    $election->end_date,
                    $election->candidates_count,
                    $election->votes_count,
                    $election->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportVoters($format)
    {
        $voters = $this->voterQuery()
            ->with(['organization'])
            ->withCount(['votes'])
            ->get();

        if ($format === 'json') {
            return response()->json(['voters' => $voters]);
        }

        $filename = 'voters_report_' . now()->format('Y-m-d_H-i-s') . '.csv';

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
                    $voter->votes_count,
                    $voter->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Build a safe query for "voters" using available schema/roles.
     * Falls back to an empty result when no voter indicator is present.
     */
    private function voterQuery()
    {
        $query = User::query();

        if (Schema::hasColumn('users', 'role')) {
            return $query->where('role', 'voter');
        }

        if (Schema::hasColumn('users', 'type')) {
            return $query->where('type', 'voter');
        }

        if (Schema::hasColumn('users', 'role_id')) {
            if (Schema::hasTable('roles')) {
                $roleId = DB::table('roles')->where('name', 'voter')->value('id');
                if ($roleId) {
                    return $query->where('role_id', $roleId);
                }
            }
            // roles table missing or no matching role => return empty query
            return $query->whereRaw('0 = 1');
        }

        if (Schema::hasColumn('users', 'is_voter')) {
            return $query->where('is_voter', 1);
        }

        // Unknown schema: avoid SQL errors by returning an empty query
        return $query->whereRaw('0 = 1');
    }
}
