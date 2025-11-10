<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ElectionController;
use App\Http\Controllers\Admin\PartylistController;
use App\Http\Controllers\Admin\VoterController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CandidateController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Custom Authentication Routes for Welcome Page
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.submit');
Route::post('/register', [RegisteredUserController::class, 'store'])->name('register');

// Authenticated user dashboard
Route::get('/dashboard', function () {
    return view('main-admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// User profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

    // Admin Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Direct voter page route (legacy)
    Route::get('/voters', [AdminController::class, 'voters'])->name('voters');

    // Settings Management
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/reset', [SettingsController::class, 'reset'])->name('settings.reset');
    Route::get('/settings/backup', [SettingsController::class, 'backup'])->name('settings.backup');
    Route::post('/settings/restore', [SettingsController::class, 'restore'])->name('settings.restore');

    /*
    |--------------------------------------------------------------------------
    | Organization Management Routes
    |--------------------------------------------------------------------------
    */
    Route::resource('organizations', OrganizationController::class);
    Route::prefix('organizations')->name('organizations.')->group(function () {
        Route::get('search', [OrganizationController::class, 'search'])->name('search');
        Route::get('export', [OrganizationController::class, 'export'])->name('export');
        Route::post('{organization}/toggle-status', [OrganizationController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('{organization}/members', [OrganizationController::class, 'members'])->name('members');
        Route::post('{organization}/add-member', [OrganizationController::class, 'addMember'])->name('add-member');
        Route::delete('{organization}/remove-member/{user}', [OrganizationController::class, 'removeMember'])->name('remove-member');
        Route::get('{organization}/statistics', [OrganizationController::class, 'statistics'])->name('statistics');
        Route::post('admin/organizations', [OrganizationController::class, 'store'])->name('admin.organizations.store');
    });

    /*
    |--------------------------------------------------------------------------
    | Election Management Routes
    |--------------------------------------------------------------------------
    */
    Route::resource('elections', ElectionController::class);
    Route::prefix('elections')->name('elections.')->group(function () {
        Route::get('search', [ElectionController::class, 'search'])->name('search');
        Route::get('export', [ElectionController::class, 'export'])->name('export');
        Route::post('{election}/toggle-status', [ElectionController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('{election}/candidates', [ElectionController::class, 'candidates'])->name('candidates');
        Route::get('{election}/voters', [ElectionController::class, 'voters'])->name('voters');
        Route::get('{election}/results', [ElectionController::class, 'results'])->name('results');
        Route::get('{election}/statistics', [ElectionController::class, 'statistics'])->name('statistics');
        Route::post('{election}/start', [ElectionController::class, 'start'])->name('start');
        Route::post('{election}/end', [ElectionController::class, 'end'])->name('end');
        Route::post('{election}/suspend', [ElectionController::class, 'suspend'])->name('suspend');
        Route::post('{election}/resume', [ElectionController::class, 'resume'])->name('resume');
    });

    /*
    |--------------------------------------------------------------------------
    | Partylist Management Routes
    |--------------------------------------------------------------------------
    */
    Route::resource('partylists', PartylistController::class);
    Route::prefix('partylists')->name('partylists.')->group(function () {
        Route::get('search', [PartylistController::class, 'search'])->name('search');
        Route::get('export', [PartylistController::class, 'export'])->name('export');
        Route::post('{partylist}/toggle-status', [PartylistController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('{partylist}/members', [PartylistController::class, 'members'])->name('members');
        Route::post('{partylist}/add-member', [PartylistController::class, 'addMember'])->name('add-member');
        Route::delete('{partylist}/remove-member/{user}', [PartylistController::class, 'removeMember'])->name('remove-member');
        Route::get('{partylist}/candidates', [PartylistController::class, 'candidates'])->name('candidates');
        Route::get('{partylist}/statistics', [PartylistController::class, 'statistics'])->name('statistics');
    });

    /*
    |--------------------------------------------------------------------------
    | Candidate Management Routes
    |--------------------------------------------------------------------------
    */
    Route::resource('candidates', CandidateController::class);
    Route::prefix('candidates')->name('candidates.')->group(function () {
        Route::get('search', [CandidateController::class, 'search'])->name('search');
        Route::get('export', [CandidateController::class, 'export'])->name('export');
        Route::post('{candidate}/toggle-status', [CandidateController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('{candidate}/approve', [CandidateController::class, 'approve'])->name('approve');
        Route::post('{candidate}/reject', [CandidateController::class, 'reject'])->name('reject');
        Route::get('{candidate}/profile', [CandidateController::class, 'profile'])->name('profile');
        Route::post('{candidate}/upload-photo', [CandidateController::class, 'uploadPhoto'])->name('upload-photo');
        Route::delete('{candidate}/remove-photo', [CandidateController::class, 'removePhoto'])->name('remove-photo');
    });

    /*
    |--------------------------------------------------------------------------
    | Voter Management Routes (Resource Routes)
    |--------------------------------------------------------------------------
    */
    Route::resource('voters', VoterController::class);
    Route::prefix('voters')->name('voters.')->group(function () {
        Route::get('search', [VoterController::class, 'search'])->name('search');
        Route::get('export', [VoterController::class, 'export'])->name('export');
        Route::post('{voter}/toggle-status', [VoterController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('{voter}/verify', [VoterController::class, 'verify'])->name('verify');
        Route::post('{voter}/unverify', [VoterController::class, 'unverify'])->name('unverify');
        Route::get('{voter}/voting-history', [VoterController::class, 'votingHistory'])->name('voting-history');
        Route::post('bulk-import', [VoterController::class, 'bulkImport'])->name('bulk-import');
        Route::get('template-download', [VoterController::class, 'downloadTemplate'])->name('template-download');
        Route::post('bulk-verify', [VoterController::class, 'bulkVerify'])->name('bulk-verify');
        Route::post('bulk-delete', [VoterController::class, 'bulkDelete'])->name('bulk-delete');

        // Import preview & store routes
        Route::post('import-preview', [VoterController::class, 'importPreview'])->name('import.preview');
        Route::post('import-store', [VoterController::class, 'importStore'])->name('import.store');
    });

    /*
    |--------------------------------------------------------------------------
    | User Management Routes
    |--------------------------------------------------------------------------
    */
    Route::resource('users', UserController::class);
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('search', [UserController::class, 'search'])->name('search');
        Route::get('export', [UserController::class, 'export'])->name('export');
        Route::post('{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('{user}/assign-role', [UserController::class, 'assignRole'])->name('assign-role');
        Route::delete('{user}/remove-role', [UserController::class, 'removeRole'])->name('remove-role');
        Route::post('{user}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
        Route::get('{user}/activity', [UserController::class, 'activity'])->name('activity');
        Route::post('bulk-import', [UserController::class, 'bulkImport'])->name('bulk-import');
        Route::get('template-download', [UserController::class, 'downloadTemplate'])->name('template-download');
    });

    /*
    |--------------------------------------------------------------------------
    | Reports & Analytics Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('dashboard', [ReportController::class, 'dashboard'])->name('dashboard');
        Route::get('elections', [ReportController::class, 'elections'])->name('elections');
        Route::get('elections/{election}', [ReportController::class, 'electionDetail'])->name('elections.detail');
        Route::get('elections/{election}/results', [ReportController::class, 'electionResults'])->name('elections.results');
        Route::get('elections/{election}/turnout', [ReportController::class, 'electionTurnout'])->name('elections.turnout');
        Route::get('voters', [ReportController::class, 'voters'])->name('voters');
        Route::get('voters/demographics', [ReportController::class, 'voterDemographics'])->name('voters.demographics');
        Route::get('voters/activity', [ReportController::class, 'voterActivity'])->name('voters.activity');
        Route::get('candidates', [ReportController::class, 'candidates'])->name('candidates');
        Route::get('candidates/performance', [ReportController::class, 'candidatePerformance'])->name('candidates.performance');
        Route::get('organizations', [ReportController::class, 'organizations'])->name('organizations');
        Route::get('organizations/{organization}', [ReportController::class, 'organizationDetail'])->name('organizations.detail');
        Route::get('system', [ReportController::class, 'system'])->name('system');
        Route::get('security', [ReportController::class, 'security'])->name('security');
        Route::get('audit', [ReportController::class, 'audit'])->name('audit');
        Route::post('export', [ReportController::class, 'export'])->name('export');
        Route::get('export/{type}', [ReportController::class, 'exportByType'])->name('export.type');
        Route::get('pdf/{type}', [ReportController::class, 'generatePDF'])->name('pdf');
    });

    /*
    |--------------------------------------------------------------------------
    | System Configuration Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('system')->name('system.')->group(function () {
        Route::get('info', [AdminController::class, 'systemInfo'])->name('info');
        Route::get('logs', [AdminController::class, 'logs'])->name('logs');
        Route::post('cache-clear', [AdminController::class, 'clearCache'])->name('cache-clear');
        Route::post('maintenance', [AdminController::class, 'toggleMaintenance'])->name('maintenance');
    });

    /*
    |--------------------------------------------------------------------------
    | API Routes for AJAX calls
    |--------------------------------------------------------------------------
    */
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('dashboard-stats', [AdminController::class, 'getDashboardStats'])->name('dashboard-stats');
        Route::get('quick-stats', [AdminController::class, 'getQuickStats'])->name('quick-stats');
        Route::get('chart-data/{type}', [AdminController::class, 'getChartData'])->name('chart-data');
        Route::get('recent-activities', [AdminController::class, 'getRecentActivities'])->name('recent-activities');
        Route::get('global-search', [AdminController::class, 'globalSearch'])->name('global-search');
        Route::get('suggestions/{type}', [AdminController::class, 'getSuggestions'])->name('suggestions');
    });

    /*
    |--------------------------------------------------------------------------
    | Notification Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [AdminController::class, 'notifications'])->name('index');
        Route::post('{notification}/read', [AdminController::class, 'markAsRead'])->name('read');
        Route::post('mark-all-read', [AdminController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('{notification}', [AdminController::class, 'deleteNotification'])->name('delete');
    });

    /*
    |--------------------------------------------------------------------------
    | Backup & Restore Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('backup')->name('backup.')->group(function () {
        Route::get('/', [AdminController::class, 'backupIndex'])->name('index');
        Route::post('create', [AdminController::class, 'createBackup'])->name('create');
        Route::get('download/{file}', [AdminController::class, 'downloadBackup'])->name('download');
        Route::delete('{file}', [AdminController::class, 'deleteBackup'])->name('delete');
        Route::post('restore/{file}', [AdminController::class, 'restoreBackup'])->name('restore');
    });
});

/*
|--------------------------------------------------------------------------
| Voter/Candidate Portal Routes
|--------------------------------------------------------------------------
*/
Route::prefix('portal')->name('portal.')->middleware(['auth'])->group(function () {
    Route::prefix('voter')->name('voter.')->group(function () {
        Route::get('dashboard', [\App\Http\Controllers\Portal\VoterController::class, 'dashboard'])->name('dashboard');
        Route::get('elections', [\App\Http\Controllers\Portal\VoterController::class, 'elections'])->name('elections');
        Route::get('elections/{election}/vote', [\App\Http\Controllers\Portal\VoterController::class, 'vote'])->name('vote');
        Route::post('elections/{election}/cast-vote', [\App\Http\Controllers\Portal\VoterController::class, 'castVote'])->name('cast-vote');
        Route::get('voting-history', [\App\Http\Controllers\Portal\VoterController::class, 'votingHistory'])->name('history');
        Route::get('profile', [\App\Http\Controllers\Portal\VoterController::class, 'profile'])->name('profile');
    });

    Route::prefix('candidate')->name('candidate.')->group(function () {
        Route::get('dashboaWeb', [\App\Http\Controllers\Portal\CandidateController::class, 'profile'])->name('profile');
        Route::post('profile', [\App\Http\Controllers\Portal\CandidateController::class, 'updateProfile'])->name('profile.update');
        Route::get('campaigns', [\App\Http\Controllers\Portal\CandidateController::class, 'campaigns'])->name('campaigns');
        Route::get('statistics', [\App\Http\Controllers\Portal\CandidateController::class, 'statistics'])->name('statistics');
    });
});

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('api/v1')->name('api.v1.')->group(function () {
    Route::get('elections', [ElectionController::class, 'apiIndex'])->name('elections.index');
    Route::get('elections/{election}', [ElectionController::class, 'apiShow'])->name('elections.show');
    Route::get('elections/{election}/candidates', [ElectionController::class, 'apiCandidates'])->name('elections.candidates');
    Route::get('elections/{election}/results', [ElectionController::class, 'apiResults'])->name('elections.results');
});

require __DIR__.'/auth.php';
