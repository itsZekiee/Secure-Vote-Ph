<?php

use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ElectionController;
use App\Http\Controllers\Admin\PartylistController;
use App\Http\Controllers\Admin\VoterController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CandidateController;
use App\Http\Controllers\Admin\SubAdminDashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Voter\VoterElectionController;
use App\Http\Controllers\Voter\ElectionAccessController;
use App\Http\Controllers\Voter\AuthController as VoterAuthController;
use App\Http\Controllers\Voter\VoterRegistrationController;
use App\Http\Controllers\Voter\PasswordResetController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Elections\Store as ElectionStoreController;
use App\Http\Controllers\MainAdmin\UserManagementController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Auth\MagicLinkController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Voter\VoterOtpController;
use App\Http\Controllers\Auth\ForgotPasswordController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->isAdmin() || auth()->user()->isElectionOfficer()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('dashboard');
    }
    return view('welcome');
})->name('home');

// Custom Authentication Routes for Welcome Page
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.submit');
Route::post('/register', [RegisteredUserController::class, 'store'])->name('register');
Route::post('auth/google/callback', [GoogleAuthController::class, 'handleCallback'])->name('auth.google.callback');

// Password Recovery Routes
Route::post('/forgot-password/send-otp', [ForgotPasswordController::class, 'sendOtp'])->name('password.otp.send.general');
Route::post('/forgot-password/verify-otp', [ForgotPasswordController::class, 'verifyOtp'])->name('password.otp.verify.general');
Route::post('/forgot-password/reset', [ForgotPasswordController::class, 'resetPassword'])->name('password.update.otp.general');


Route::middleware('guest')->group(function () {
    Route::get('otp', [OtpController::class, 'show'])->name('otp.form');
    Route::post('otp', [OtpController::class, 'verify'])->name('otp.verify');
});

Route::get('/voter/otp', [VoterOtpController::class, 'show'])
    ->name('voter.otp.form');

Route::post('/voter/otp', [VoterOtpController::class, 'verify'])
    ->name('voter.otp.verify');

Route::post('/otp/resend', [OtpController::class, 'resend'])
    ->name('otp.resend');



Route::get('/magic-link/callback', [MagicLinkController::class, 'handleMagicLink'])->name('magiclink.callback');

// Authenticated user dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Election Registration Route (Public)
|--------------------------------------------------------------------------
*/
Route::get('/elections/register/{election}', [VoterElectionController::class, 'register'])
    ->name('elections.register');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'ip.control'])->group(function () {

    // Admin Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Sub-Admin Dashboard Routes
    Route::prefix('sub-admin')->name('sub-admin.')->group(function () {
        Route::get('/dashboard', [SubAdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/elections', [SubAdminDashboardController::class, 'getAssignedElections'])->name('elections');
        Route::get('/elections/{election}/data', [SubAdminDashboardController::class, 'getElectionData'])->name('election-data');
    });


    // Settings Management
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/reset', [SettingsController::class, 'reset'])->name('settings.reset');
    Route::get('/settings/backup', [SettingsController::class, 'backup'])->name('settings.backup');
    Route::post('/settings/restore', [SettingsController::class, 'restore'])->name('settings.restore');

    // Security Management
    Route::delete('/settings/sessions/{session_id}', [SettingsController::class, 'logoutSession'])->name('settings.sessions.logout');
    Route::post('/settings/sessions/logout-others', [SettingsController::class, 'logoutOtherSessions'])->name('settings.sessions.logout-others');
    Route::post('/settings/recovery-codes/generate', [SettingsController::class, 'generateRecoveryCodes'])->name('settings.recovery-codes.generate');
    Route::post('/settings/recovery-codes/show', [SettingsController::class, 'showRecoveryCodes'])->name('settings.recovery-codes.show');
    Route::post('/settings/security-preferences', [SettingsController::class, 'updateSecurityPreferences'])->name('settings.security-preferences.update');

    // Profile Management
    Route::put('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\Admin\ProfileController::class, 'updatePassword'])->name('profile.password');

    // Organization Management Routes
    Route::prefix('organizations')->name('organizations.')->group(function () {
        Route::get('search', [OrganizationController::class, 'search'])->name('search');
        Route::get('export', [OrganizationController::class, 'export'])->name('export');
        Route::post('{organization}/toggle-status', [OrganizationController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('{organization}/members', [OrganizationController::class, 'members'])->name('members');
        Route::post('{organization}/add-member', [OrganizationController::class, 'addMember'])->name('add-member');
        Route::delete('{organization}/remove-member/{user}', [OrganizationController::class, 'removeMember'])->name('remove-member');
        Route::get('{organization}/statistics', [OrganizationController::class, 'statistics'])->name('statistics');
        Route::post('admin/organizations', [OrganizationController::class, 'store'])->name('admin.organizations.store');
        // Automation Mode: Get partylists for organization
        Route::get('{organization}/partylists', [ElectionController::class, 'getOrganizationPartylists'])->name('partylists');
    });
    Route::resource('organizations', OrganizationController::class);

    // Election Management Routes
    Route::prefix('elections')->name('elections.')->group(function () {
        Route::get('search', [ElectionController::class, 'search'])->name('search');
        Route::get('export', [ElectionController::class, 'export'])->name('export');
        Route::post('{election}/toggle-status', [ElectionController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('{election}/assign-sub-admin', [ElectionController::class, 'assignSubAdmin'])->name('assign-sub-admin');
        Route::get('search-admins', [ElectionController::class, 'searchAdmins'])->name('search-admins');
        Route::post('{election}/remove-sub-admin', [ElectionController::class, 'removeSubAdmin'])->name('remove-sub-admin');
        Route::get('{election}/candidates', [ElectionController::class, 'candidates'])->name('candidates');
        Route::get('{election}/voters', [ElectionController::class, 'voters'])->name('voters');
        Route::get('{election}/results', [ElectionController::class, 'results'])->name('results');
        Route::get('{election}/statistics', [ElectionController::class, 'statistics'])->name('statistics');
        Route::post('{election}/start', [ElectionController::class, 'start'])->name('start');
        Route::post('{election}/end', [ElectionController::class, 'end'])->name('end');
        Route::post('{election}/suspend', [ElectionController::class, 'suspend'])->name('suspend');
        Route::post('{election}/resume', [ElectionController::class, 'resume'])->name('resume');
    });
    Route::resource('elections', ElectionController::class)->except(['store']);
    Route::post('elections', ElectionStoreController::class)->name('elections.store');

    // Partylist Management Routes
    Route::prefix('partylists')->name('partylists.')->group(function () {
        Route::get('search', [PartylistController::class, 'search'])->name('search');
        Route::get('export', [PartylistController::class, 'export'])->name('export');
        Route::post('import-preview', [PartylistController::class, 'importPreview'])->name('import.preview');
        Route::post('import-store', [PartylistController::class, 'importStore'])->name('import.store');
        Route::post('{partylist}/toggle-status', [PartylistController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('{partylist}/members', [PartylistController::class, 'members'])->name('members');
        Route::post('{partylist}/add-member', [PartylistController::class, 'addMember'])->name('add-member');
        Route::delete('{partylist}/remove-member/{user}', [PartylistController::class, 'removeMember'])->name('remove-member');
        // Automation Mode: Get candidates for partylist
        Route::get('{partylist}/candidates', [ElectionController::class, 'getPartylistCandidates'])->name('candidates');
        Route::get('{partylist}/statistics', [PartylistController::class, 'statistics'])->name('statistics');
    });
    Route::resource('partylists', PartylistController::class);

    // Candidate Management Routes
    Route::prefix('candidates')->name('candidates.')->group(function () {
        Route::get('search', [CandidateController::class, 'search'])->name('search');
        Route::get('export', [CandidateController::class, 'export'])->name('export');
        Route::post('import-preview', [CandidateController::class, 'importPreview'])->name('import.preview');
        Route::post('import-store', [CandidateController::class, 'importStore'])->name('import.store');
        Route::post('{candidate}/toggle-status', [CandidateController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('{candidate}/approve', [CandidateController::class, 'approve'])->name('approve');
        Route::post('{candidate}/reject', [CandidateController::class, 'reject'])->name('reject');
        Route::get('{candidate}/profile', [CandidateController::class, 'profile'])->name('profile');
        Route::post('{candidate}/upload-photo', [CandidateController::class, 'uploadPhoto'])->name('upload-photo');
        Route::delete('{candidate}/remove-photo', [CandidateController::class, 'removePhoto'])->name('remove-photo');
    });
    Route::resource('candidates', CandidateController::class);

    // Voter Management Routes
    Route::prefix('voters')->name('voters.')->group(function () {
        Route::get('search', [VoterController::class, 'search'])->name('search');
        Route::get('export', [VoterController::class, 'export'])->name('export');
        Route::post('bulk-import', [VoterController::class, 'bulkImport'])->name('bulk-import');
        Route::get('template-download', [VoterController::class, 'downloadTemplate'])->name('template-download');
        Route::post('bulk-verify', [VoterController::class, 'bulkVerify'])->name('bulk-verify');
        Route::post('bulk-delete', [VoterController::class, 'bulkDelete'])->name('bulk-delete');
        Route::post('import-preview', [VoterController::class, 'importPreview'])->name('import.preview');
        Route::post('import-store', [VoterController::class, 'importStore'])->name('import.store');

        Route::post('{voter}/toggle-status', [VoterController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('{voter}/verify', [VoterController::class, 'verify'])->name('verify');
        Route::post('{voter}/unverify', [VoterController::class, 'unverify'])->name('unverify');
        Route::post('{voter}/approve', [VoterController::class, 'approve'])->name('approve');
        Route::post('{voter}/decline', [VoterController::class, 'decline'])->name('decline');
        Route::get('{voter}/voting-history', [VoterController::class, 'votingHistory'])->name('voting-history');
    });
    Route::resource('voters', VoterController::class);

    // User Management Routes
    Route::resource('users', UserManagementController::class);
    Route::prefix('users')->name('users.')->group(function () {
        Route::post('{user}/approve', [UserManagementController::class, 'approve'])->name('approve');
        Route::post('{user}/reject', [UserManagementController::class, 'reject'])->name('reject');
        Route::get('search', [UserManagementController::class, 'search'])->name('search');
        Route::get('export', [UserManagementController::class, 'export'])->name('export');
        Route::post('{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Reports & Analytics Routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/view/{election}', [ReportController::class, 'viewReport'])->name('view');
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
        Route::match(['get', 'post'], 'export', [ReportController::class, 'export'])->name('export');
        Route::get('export/{type}', [ReportController::class, 'exportByType'])->name('export.type');
        Route::get('pdf/{type}', [ReportController::class, 'generatePDF'])->name('pdf');
    });

    // System Configuration Routes
    Route::prefix('system')->name('system.')->group(function () {
        Route::get('info', [AdminController::class, 'systemInfo'])->name('info');
        Route::get('logs', [AdminController::class, 'logs'])->name('logs');
        Route::post('cache-clear', [SettingsController::class, 'clearSystemCache'])->name('cache-clear');
        Route::post('db-optimize', [SettingsController::class, 'optimizeDatabase'])->name('db-optimize');
        Route::post('force-logout-all', [SettingsController::class, 'forceLogoutAll'])->name('force-logout-all');
        Route::post('check-updates', [SettingsController::class, 'checkGitUpdates'])->name('check-updates');
        Route::post('archive-election', [SettingsController::class, 'archiveElection'])->name('archive-election');
        Route::post('maintenance', [AdminController::class, 'toggleMaintenance'])->name('maintenance');

        // IP Access Control
        Route::post('ip-control', [SettingsController::class, 'storeIpControl'])->name('ip-control.store');
        Route::delete('ip-control/{id}', [SettingsController::class, 'deleteIpControl'])->name('ip-control.delete');
    });

    // API Routes for AJAX calls
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('dashboard-stats', [AdminController::class, 'getDashboardStats'])->name('dashboard-stats');
        Route::get('quick-stats', [AdminController::class, 'getQuickStats'])->name('quick-stats');
        Route::get('chart-data/{type}', [AdminController::class, 'getChartData'])->name('chart-data');
        Route::get('recent-activities', [AdminController::class, 'getRecentActivities'])->name('recent-activities');
        Route::get('global-search', [AdminController::class, 'globalSearch'])->name('global-search');
        Route::get('suggestions/{type}', [AdminController::class, 'getSuggestions'])->name('suggestions');
    });

    // Notification Routes
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [AdminController::class, 'notifications'])->name('index');
        Route::post('{notification}/read', [AdminController::class, 'markAsRead'])->name('read');
        Route::post('mark-all-read', [AdminController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('{notification}', [AdminController::class, 'deleteNotification'])->name('delete');
    });

    // Backup & Restore Routes
    Route::prefix('backup')->name('backup.')->group(function () {
        Route::get('/', [AdminController::class, 'backupIndex'])->name('index');
        Route::post('create', [AdminController::class, 'createBackup'])->name('create');
        Route::get('download/{file}', [AdminController::class, 'downloadBackup'])->name('download');
        Route::delete('{file}', [AdminController::class, 'deleteBackup'])->name('delete');
        Route::post('restore/{file}', [AdminController::class, 'restoreBackup'])->name('restore');
    });
});

// Public API Routes
Route::prefix('api/v1')->name('api.v1.')->group(function () {
    Route::get('elections', [ElectionController::class, 'apiIndex'])->name('elections.index');
    Route::get('elections/{election}', [ElectionController::class, 'apiShow'])->name('elections.show');
    Route::get('elections/{election}/candidates', [ElectionController::class, 'apiCandidates'])->name('elections.candidates');
    Route::get('elections/{election}/results', [ElectionController::class, 'apiResults'])->name('elections.results');
});

/*
|--------------------------------------------------------------------------
| Voter Portal Routes
|--------------------------------------------------------------------------
*/
Route::prefix('voter')->name('voter.')->group(function () {

    // ==========================================
    // PUBLIC ROUTES (No Authentication Required)
    // ==========================================

    // Step 1: Election Access - Enter code/link
    Route::get('/access', [VoterElectionController::class, 'access'])->name('elections.access');
    Route::post('/access/verify', [VoterElectionController::class, 'verify'])->name('elections.verify');

    // Dashboard & Elections List Redirects
    Route::get('/dashboard', [VoterAuthController::class, 'welcome'])->name('dashboard');
    Route::get('/elections', function () {
        return redirect()->route('voter.elections.access');
    })->name('elections.index');

    // Step 2: Voter Registration/Login for specific election
    Route::get('/registration/{election}', [VoterRegistrationController::class, 'index'])->name('registration.index');
    Route::post('/registration/{election}', [VoterRegistrationController::class, 'store'])->name('registration.store');
    Route::post('/registration/{election}/login', [VoterRegistrationController::class, 'login'])->name('registration.login');

    // Public Results (Real-time data)
    Route::get('/elections/{election}/results', [VoterElectionController::class, 'results'])->name('elections.results');
    Route::get('/elections/{election}/results/votes', [VoterElectionController::class, 'getVotes'])->name('elections.results.votes');

    // Voter Password Reset Routes
    Route::get('/registration/{election}/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/registration/{election}/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/registration/{election}/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/registration/{election}/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
    Route::get('/registration/{election}/password/search', [PasswordResetController::class, 'searchEmails'])->name('password.search');
    Route::post('/registration/{election}/password/otp', [PasswordResetController::class, 'sendOTP'])->name('password.otp.send');
    Route::post('/registration/{election}/password/otp/verify', [PasswordResetController::class, 'verifyOTP'])->name('password.otp.verify');

    // ==========================================
    // PROTECTED ROUTES (Voter Session Required)
    // ==========================================
    Route::middleware(['voter.auth'])->group(function () {

        // Step 3: Welcome page with countdown
        Route::get('/elections/{election}/welcome', [VoterElectionController::class, 'welcome'])->name('elections.welcome');

        // Step 4: Voting page
        Route::get('/elections/{election}/vote', [VoterElectionController::class, 'index'])->name('elections.vote');
        Route::post('/elections/{election}/submit', [VoterElectionController::class, 'submitVote'])->name('elections.submit');

        // Voter Profile & History
        Route::get('/profile', [VoterElectionController::class, 'profile'])->name('profile.index');
        Route::get('/history', [VoterElectionController::class, 'history'])->name('history.index');

        // Logout
        Route::post('/logout', [VoterRegistrationController::class, 'logout'])->name('logout');
    });

    // ==========================================
    // LEGACY ROUTES (Redirects for backward compatibility)
    // ==========================================

    // Legacy election access routes
    Route::get('/election/{code}/welcome', [ElectionAccessController::class, 'welcome'])->name('election.welcome');
    Route::get('/elections/welcome', [ElectionAccessController::class, 'welcomeFromSession'])->name('elections.welcome.session');
    Route::get('/welcome', [VoterAuthController::class, 'welcome'])->name('welcome');
    Route::get('/register/{code}', [ElectionAccessController::class, 'register'])->name('register');

    // Legacy join routes - REDIRECT to access page
    Route::get('/elections/join', function () {
        return redirect()->route('voter.elections.access');
    })->name('elections.join');

    Route::post('/elections/join', function () {
        return redirect()->route('voter.elections.access');
    })->name('elections.join.submit');

    // Legacy show and vote
    Route::get('/elections/{election}/confirmation', [VoterElectionController::class, 'confirmation'])->name('elections.confirmation');

    // Legacy registration view
    Route::get('/registration', function () {
        return view('voter.registration.index');
    })->name('registration.legacy');

    // Legacy authentication
    Route::post('/login', [VoterAuthController::class, 'login'])->name('login');
    Route::post('/register', [VoterAuthController::class, 'register'])->name('register.submit');
});

Route::get('/password/reset', function () {
    return view('auth.passwords.email');
})->name('password.request');

require __DIR__.'/auth.php';
