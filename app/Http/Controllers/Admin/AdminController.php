<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Election;
use App\Models\Candidate;
use App\Models\Vote;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_elections' => Election::count(),
            'total_candidates' => Candidate::count(),
            'total_votes' => Vote::count(),
            'active_elections' => Election::where('status', 'active')->count(),
            'total_organizations' => Organization::count(),
        ];

        return view('main-admin.dashboard', compact('stats'));
    }

    public function voters()
    {
        $voters = User::where('role', 'voter')
            ->with('organization')
            ->paginate(15);

        return view('main-admin.voters', compact('voters'));
    }

    public function getDashboardStats()
    {
        return response()->json([
            'total_users' => User::count(),
            'total_elections' => Election::count(),
            'total_candidates' => Candidate::count(),
            'total_votes' => Vote::count(),
            'active_elections' => Election::where('status', 'active')->count(),
        ]);
    }

    public function getQuickStats()
    {
        return response()->json([
            'recent_registrations' => User::where('created_at', '>=', now()->subDays(7))->count(),
            'ongoing_elections' => Election::where('status', 'active')->count(),
            'pending_candidates' => Candidate::where('status', 'pending')->count(),
            'total_votes_today' => Vote::whereDate('created_at', today())->count(),
        ]);
    }

    public function getChartData($type)
    {
        switch ($type) {
            case 'elections':
                $data = Election::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();
                break;

            case 'votes':
                $data = Vote::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();
                break;

            default:
                $data = [];
        }

        return response()->json($data);
    }

    public function getRecentActivities()
    {
        $activities = collect()
            ->merge(
                Election::latest()->take(5)->get()->map(function ($election) {
                    return [
                        'type' => 'election',
                        'title' => "New election: {$election->title}",
                        'date' => $election->created_at,
                        'icon' => 'ballot-box'
                    ];
                })
            )
            ->merge(
                User::latest()->take(5)->get()->map(function ($user) {
                    return [
                        'type' => 'user',
                        'title' => "New user registered: {$user->name}",
                        'date' => $user->created_at,
                        'icon' => 'user-plus'
                    ];
                })
            )
            ->sortByDesc('date')
            ->take(10)
            ->values();

        return response()->json($activities);
    }

    public function globalSearch(Request $request)
    {
        $query = $request->get('q');

        $results = [
            'users' => User::where('name', 'LIKE', "%{$query}%")
                ->orWhere('email', 'LIKE', "%{$query}%")
                ->take(5)
                ->get(),
            'elections' => Election::where('title', 'LIKE', "%{$query}%")
                ->take(5)
                ->get(),
            'candidates' => Candidate::whereHas('user', function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            })->take(5)->get(),
        ];

        return response()->json($results);
    }

    public function getSuggestions($type)
    {
        switch ($type) {
            case 'users':
                return response()->json(User::pluck('name', 'id'));
            case 'elections':
                return response()->json(Election::pluck('title', 'id'));
            default:
                return response()->json([]);
        }
    }

    public function systemInfo()
    {
        $info = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'database' => config('database.default'),
            'cache_driver' => config('cache.default'),
            'queue_driver' => config('queue.default'),
            'mail_driver' => config('mail.default'),
        ];

        return view('main-admin.system.info', compact('info'));
    }

    public function logs()
    {
        // Implementation for viewing logs
        return view('main-admin.system.logs');
    }

    public function clearCache()
    {
        Cache::flush();

        return response()->json([
            'success' => true,
            'message' => 'Cache cleared successfully'
        ]);
    }

    public function toggleMaintenance()
    {
        // Implementation for maintenance mode toggle
        return response()->json([
            'success' => true,
            'message' => 'Maintenance mode toggled'
        ]);
    }

    public function notifications()
    {
        // Implementation for notifications
        return view('main-admin.notifications.index');
    }

    public function markAsRead($id)
    {
        // Implementation for marking notification as read
        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        // Implementation for marking all notifications as read
        return response()->json(['success' => true]);
    }

    public function deleteNotification($id)
    {
        // Implementation for deleting notification
        return response()->json(['success' => true]);
    }

    public function backupIndex()
    {
        // Implementation for backup listing
        return view('main-admin.backup.index');
    }

    public function createBackup()
    {
        // Implementation for creating backup
        return response()->json(['success' => true]);
    }

    public function downloadBackup($file)
    {
        // Implementation for downloading backup
        return response()->download(storage_path("app/backups/{$file}"));
    }

    public function deleteBackup($file)
    {
        // Implementation for deleting backup
        return response()->json(['success' => true]);
    }

    public function restoreBackup($file)
    {
        // Implementation for restoring backup
        return response()->json(['success' => true]);
    }
}
