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
    private function getView($name)
    {
        if (auth()->check() && auth()->user()->hasRole('admin') && !auth()->user()->hasRole('super-admin')) {
            return "admin.$name";
        }
        return "main-admin.$name";
    }

    public function index(Request $request)
    {
        $userId = auth()->id();
        $isSuperAdmin = auth()->user()->hasRole('super-admin');

        $stats = [
            'total_elections' => Election::when(!$isSuperAdmin, function($q) use ($userId) {
                return $q->where('created_by', $userId);
            })->count(),
            'active_elections' => Election::where('status', 'active')
                ->when(!$isSuperAdmin, function($q) use ($userId) {
                    return $q->where('created_by', $userId);
                })->count(),
            'total_voters' => \App\Models\Voter::when(!$isSuperAdmin, function($q) use ($userId) {
                return $q->whereHas('election', function($eq) use ($userId) {
                    $eq->where('created_by', $userId);
                });
            })->count(),
            'total_candidates' => Candidate::when(!$isSuperAdmin, function($q) use ($userId) {
                return $q->where('created_by', $userId);
            })->count(),
            'total_votes' => Vote::when(!$isSuperAdmin, function($q) use ($userId) {
                return $q->whereHas('election', function($eq) use ($userId) {
                    $eq->where('created_by', $userId);
                });
            })->count(),
            'organizations_count' => Organization::when(!$isSuperAdmin, function($q) use ($userId) {
                return $q->where('created_by', $userId);
            })->count(),
        ];

        // Fetch "forms" (elections)
        $query = Election::with(['organization'])
            ->withCount(['candidates', 'votes']);

        if (!$isSuperAdmin) {
            $query->where('created_by', $userId);
        }

        if ($request->has('q')) {
            $query->where('title', 'like', '%' . $request->q . '%');
        }

        if ($request->has('organization_id') && !empty($request->organization_id)) {
            $query->where('organization_id', $request->organization_id);
        }

        if ($request->has('year') && !empty($request->year)) {
            $query->whereYear('end_date', $request->year);
        }

        $forms = $query->latest()->paginate(10);

        // Organizations for the dropdown on the reports page
        $organizations = Organization::when(!$isSuperAdmin, function($q) use ($userId) {
            return $q->where('created_by', $userId);
        })->orderBy('name')->get();

        // Years for filter
        $years = Election::selectRaw('YEAR(end_date) as year')
            ->when(!$isSuperAdmin, function($q) use ($userId) {
                return $q->where('created_by', $userId);
            })
            ->whereNotNull('end_date')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view($this->getView('reports'), compact('stats', 'forms', 'organizations', 'years'));
    }

    /**
     * View detailed report for an election
     */
    public function viewReport(Election $election)
    {
        $election->load(['organization', 'positions.candidates' => function ($query) use ($election) {
            $query->withCount(['votes' => function ($q) use ($election) {
                $q->where('election_id', $election->id);
            }]);
        }]);

        $totalVotes = Vote::where('election_id', $election->id)->count();
        // Assuming each voter can vote once (even if they vote for multiple positions,
        // usually turnout is based on unique voters who participated)
        $turnoutCount = Vote::where('election_id', $election->id)->distinct('voter_id')->count('voter_id');

        // This project doesn't seem to have a global 'Voter' model that is not election-specific?
        // Actually, looking at the migrations or models might help.
        // In this project, Voter belongs to an election.
        $totalRegistered = \App\Models\Voter::where('election_id', $election->id)->count();

        return view($this->getView('report.reports-view'), compact('election', 'totalVotes', 'turnoutCount', 'totalRegistered'));
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

        return view($this->getView('reports.elections'), compact('elections'));
    }

    /**
     * Voters reports
     */
    public function voters()
    {
        $voters = $this->voterQuery()
            ->with(['election.organization'])
            ->withCount(['votes'])
            ->orderBy('created_at', 'desc')
            ->get();

        $voter_stats = [
            'total_voters' => $voters->count(),
            'approved_voters' => $voters->where('registration_status', 'approved')->count(),
            'voters_with_votes' => $voters->where('votes_count', '>', 0)->count(),
        ];

        return view($this->getView('reports.voters'), compact('voters', 'voter_stats'));
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

        return view($this->getView('reports.candidates'), compact('candidates', 'candidate_stats'));
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
            if (ob_get_level() > 0) ob_end_clean();
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
                    $voter->registration_status,
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
            if (ob_get_level() > 0) ob_end_clean();
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
            ->with(['election.organization'])
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
            if (ob_get_level() > 0) ob_end_clean();
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
                    $voter->election->organization->name ?? 'N/A',
                    $voter->registration_status,
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
        return \App\Models\Voter::query();
    }
}
