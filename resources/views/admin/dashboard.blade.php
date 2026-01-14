@extends('layouts.app-main-admin')

@section('content')
    <script>
        function dashboard() {
            return {
                selectedElection: null,
                searchQuery: '',
                statusFilter: 'all',
                showShareModal: false,
                shareElectionId: null,
                shareEmail: '',
                adminSuggestions: [],
                sharing: false,
                auditSearchQuery: '',
                elections: (() => {
                    const raw = @json($elections ?? []);
                    return (raw || []).map(e => ({
                        ...e,
                        organization: (e.organization && e.organization.name) ? e.organization.name : (e.organization_name ?? 'N/A')
                    }));
                })(),
                formatDate(dateString) {
                    const date = new Date(dateString);
                    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
                },
                getStatusColor(status) {
                    const colors = {
                        'active': 'bg-emerald-50 text-emerald-700 ring-emerald-100',
                        'scheduled': 'bg-sky-50 text-sky-700 ring-sky-100',
                        'completed': 'bg-slate-50 text-slate-700 ring-slate-100'
                    };
                    return colors[status] || 'bg-slate-50 text-slate-700';
                },
                getStatusIcon(status) {
                    const icons = {
                        'active': '<circle cx="12" cy="12" r="3" fill="currentColor"/><circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="2" fill="none"/>',
                        'scheduled': '<circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/><path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>',
                        'completed': '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/><polyline points="22 4 12 14.01 9 11.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>'
                    };
                    return icons[status] || '';
                },
                editElection(id) {
                    window.location.href = `/admin/elections/${id}/edit`;
                },
                viewReports(id) {
                    window.location.href = `/admin/reports?form_id=${id}`;
                },
                openShareModal(id) {
                    this.shareElectionId = id;
                    this.shareEmail = '';
                    this.adminSuggestions = [];
                    this.showShareModal = true;
                },
                async searchAdmins() {
                    if (this.shareEmail.length < 2) {
                        this.adminSuggestions = [];
                        return;
                    }
                    const response = await fetch(`/admin/elections/search-admins?q=${this.shareEmail}`);
                    this.adminSuggestions = await response.json();
                },
                async shareAccess() {
                    if (!this.shareEmail) return;
                    this.sharing = true;
                    try {
                        const response = await fetch(`/admin/elections/${this.shareElectionId}/assign-sub-admin`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ email: this.shareEmail })
                        });
                        const data = await response.json();
                        if (data.success) {
                            alert(data.message);
                            this.showShareModal = false;
                        } else {
                            alert(data.error || 'Failed to share access');
                        }
                    } catch (e) {
                        alert('An error occurred');
                    } finally {
                        this.sharing = false;
                    }
                },
                get filteredElections() {
                    const q = this.searchQuery.trim().toLowerCase();
                    const allowedStatuses = ['active', 'scheduled', 'completed'];
                    return this.elections.filter(e => {
                        if (!allowedStatuses.includes(e.status)) return false;
                        if (this.statusFilter !== 'all' && e.status !== this.statusFilter) return false;
                        if (!q) return true;
                        return e.name.toLowerCase().includes(q) || e.organization.toLowerCase().includes(q);
                    });
                },
                get currentElection() {
                    if (this.selectedElection) {
                        return this.elections.find(e => e.id === this.selectedElection) || null;
                    }
                    return null;
                },
                getActionColor(action) {
                    const colors = {
                        'CREATE': 'bg-emerald-50 text-emerald-700 border-emerald-100',
                        'UPDATE': 'bg-amber-50 text-amber-700 border-amber-100',
                        'DELETE': 'bg-rose-50 text-rose-700 border-rose-100',
                        'LOGIN': 'bg-sky-50 text-sky-700 border-sky-100',
                        'VOTE_CAST': 'bg-purple-50 text-purple-700 border-purple-100'
                    };
                    return colors[action] || 'bg-slate-50 text-slate-700 border-slate-100';
                },
                get currentStats() {
                    if (this.currentElection) {
                        return this.currentElection;
                    }
                    return {
                        totalVotes: this.elections.reduce((sum, e) => sum + (e.totalVotes || 0), 0),
                        registeredVoters: this.elections.reduce((sum, e) => sum + (e.registeredVoters || 0), 0),
                        turnoutRate: this.elections.length > 0
                            ? (this.elections.reduce((sum, e) => sum + (e.turnoutRate || 0), 0) / this.elections.length).toFixed(1)
                            : 0
                    };
                },
                get turnoutPercentage() {
                    const denom = this.currentStats.registeredVoters || 1;
                    return ((this.currentStats.totalVotes / denom) * 100).toFixed(1);
                },
                get realtimeMetrics() {
                    return this.currentElection?.realtimeMetrics || {
                        votesPerMinute: Array(10).fill(0),
                        avgTimeToVote: 0,
                        activeSessions: 0,
                        failedLogins: 0,
                        suspiciousIPs: 0,
                        verificationSuccessRate: 0,
                        ghostRegistrations: 0
                    };
                },
                get demographicData() {
                    return this.currentElection?.demographicData || {
                        ageGroups: [],
                        regions: [],
                        submissionMethods: []
                    };
                },
                init() {
                    // Initialize if needed
                }
            };
        }
    </script>

    <div x-data="dashboard()" @election-selected.window="selectedElection = $event.detail.id" class="flex-1 flex flex-col">
        <x-admin-header title="Dashboard Overview" />

        <!-- Mobile Header -->
        <header class="lg:hidden bg-white shadow-sm border-b px-4 py-3 flex items-center justify-between">
            <button @click="collapsed = false"
                    class="p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                <i class="ri-menu-fold-line text-lg rotate-180"></i>
            </button>
            <h1 class="text-lg font-semibold text-slate-800">Dashboard</h1>
            <div class="w-10"></div>
        </header>

            <main class="flex-1 p-4 sm:p-6 pb-10">
                <div class="max-w-7xl mx-auto space-y-6 sm:space-y-8">
                    <section>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 items-start lg:items-center justify-between gap-4 mb-4">
                            <div class="md:col-span-2 lg:col-span-1">
                                <h2 class="text-2xl font-extrabold text-slate-900">Election Overview</h2>
                                <p class="text-sm text-slate-500 mt-1 hidden sm:block">Manage and inspect election analytics in one place</p>
                            </div>
                            <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:col-span-2 lg:col-span-3 lg:justify-end">
                                <div class="flex items-center gap-2 bg-white border border-gray-200 px-3 py-2 rounded-lg shadow-sm w-full sm:max-w-xs">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    <input x-model="searchQuery" type="text" placeholder="Search elections or organizations..." class="flex-1 text-sm outline-none border-none focus:ring-0 p-0" />
                                </div>

                                <div class="flex items-center gap-3 w-full sm:w-auto">
                                    <select x-model="statusFilter" class="bg-white border border-gray-200 px-3 py-2 rounded-lg text-sm shadow-sm flex-1 sm:flex-none">
                                        <option value="all">All Status</option>
                                        <option value="active">Active</option>
                                        <option value="scheduled">Scheduled</option>
                                        <option value="completed">Completed</option>
                                    </select>

                                    <a href="/admin/elections/create" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium transition-colors shadow flex-1 sm:flex-none whitespace-nowrap">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        <span class="sm:hidden lg:inline">New Election</span>
                                        <span class="hidden sm:inline lg:hidden">New</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white border border-gray-200 rounded-2xl shadow overflow-hidden">
                            <div class="responsive-table-container">
                                <div class="max-h-[420px] overflow-y-auto">
                                    <table class="w-full text-sm">
                                        <thead class="bg-slate-50 sticky top-0 z-10">
                                        <tr class="text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                            <th class="px-6 py-4 border-b border-gray-100">Election Name</th>
                                            <th class="px-6 py-4 border-b border-gray-100">Organization</th>
                                            <th class="px-6 py-4 border-b border-gray-100">Created</th>
                                            <th class="px-6 py-4 border-b border-gray-100">Status</th>
                                            <th class="px-6 py-4 border-b border-gray-100">Votes</th>
                                            <th class="px-6 py-4 border-b border-gray-100 text-center">Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                        <template x-for="election in filteredElections" :key="election.id">
                                            <tr @click="selectedElection = election.id" class="hover:bg-slate-50 cursor-pointer transition-colors">
                                                <td class="px-6 py-4">
                                                    <div class="font-medium text-slate-900" x-text="election.name"></div>
                                                </td>
                                                <td class="px-6 py-4 text-slate-600" x-text="election.organization"></td>
                                                <td class="px-6 py-4 text-slate-600" x-text="formatDate(election.createdDate)"></td>
                                                <td class="px-6 py-4">
                                                    <span :class="getStatusColor(election.status)" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium ring-1">
                                                        <svg class="w-4 h-4" viewBox="0 0 24 24" x-html="getStatusIcon(election.status)"></svg>
                                                        <span x-text="election.status.charAt(0).toUpperCase() + election.status.slice(1)"></span>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <div class="text-slate-900 font-semibold" x-text="election.totalVotes"></div>
                                                    <div class="text-xs text-slate-500" x-text="`of ${election.registeredVoters}`"></div>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <div class="flex items-center justify-center gap-2">
                                                        <button @click.stop="viewReports(election.id)" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs bg-sky-50 text-sky-700 rounded-lg hover:bg-sky-100 transition-colors">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                            </svg>
                                                            Reports
                                                        </button>
                                                        <button @click.stop="openShareModal(election.id)" class="p-1.5 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded transition-colors" title="Share Access">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                                                            </svg>
                                                        </button>
                                                        <button @click.stop="editElection(election.id)" class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded transition-colors">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="px-6 py-4 bg-slate-50 border-t border-gray-100 flex items-center justify-between text-sm text-slate-600">
                                <div>
                                    Showing <span class="font-medium text-slate-900" x-text="filteredElections.length"></span> election(s)
                                </div>
                                <div class="text-xs">
                                    Click a row to view analytics • Scroll for more elections
                                </div>
                            </div>
                        </div>

                        <!-- Selected Election Header -->
                        <div x-show="selectedElection" x-transition class="mt-6 rounded-2xl overflow-hidden shadow-lg">
                            <div class="bg-gradient-to-r from-indigo-600 via-indigo-500 to-purple-600 px-6 py-5 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <div>
                                    <h3 class="text-xl font-bold" x-text="currentElection?.name"></h3>
                                    <p class="text-sm text-indigo-100 mt-1" x-text="currentElection?.organization"></p>
                                </div>
                                <div class="flex items-center gap-3 w-full sm:w-auto">
                                    <button @click="viewReports(selectedElection)" class="flex-1 sm:flex-none px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg text-sm font-medium transition-colors backdrop-blur-sm">
                                        View Full Report
                                    </button>
                                    <button @click="selectedElection = null" class="p-2 hover:bg-white/20 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="bg-white px-6 py-4 border-t border-gray-100">
                                <div class="flex items-center justify-between gap-4 overflow-x-auto pb-2 sm:pb-0">
                                    <div class="flex items-center gap-6 min-w-max">
                                        <div>
                                            <div class="text-xs text-slate-500">Created</div>
                                            <div class="text-sm font-medium text-slate-900" x-text="formatDate(currentElection?.createdDate)"></div>
                                        </div>
                                        <div class="w-px h-8 bg-gray-200"></div>
                                        <div>
                                            <div class="text-xs text-slate-500">Turnout Rate</div>
                                            <div class="text-sm font-semibold text-emerald-600" x-text="`${currentElection?.turnoutRate}%`"></div>
                                        </div>
                                        <div class="w-px h-8 bg-gray-200"></div>
                                        <div>
                                            <div class="text-xs text-slate-500">Election Code</div>
                                            <div class="text-sm font-mono font-semibold text-indigo-600" x-text="currentElection?.code"></div>
                                        </div>
                                        <div class="w-px h-8 bg-gray-200"></div>
                                        <div>
                                            <div class="text-xs text-slate-500">Election Link</div>
                                            <div class="flex items-center gap-2">
                                                <a :href="currentElection?.link" target="_blank" class="text-sm text-sky-600 hover:underline truncate max-w-[150px] sm:max-w-xs" x-text="currentElection?.link"></a>
                                                <button @click="navigator.clipboard.writeText(currentElection?.link)" class="p-1 hover:bg-slate-100 rounded transition-colors" title="Copy link">
                                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <div x-show="selectedElection" x-transition class="space-y-8">
                        <section>
                            <div class="flex items-center gap-3 mb-6">
                                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-indigo-100">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">Real-Time Metrics</h3>
                                    <p class="text-sm text-slate-500">Live voting activity and engagement</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                                <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="text-sm font-medium text-slate-600">Active Sessions</div>
                                        <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="text-3xl font-bold text-slate-900" x-text="realtimeMetrics.activeSessions"></div>
                                    <div class="text-xs text-slate-500 mt-1">Voters currently active</div>
                                </div>

                                <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="text-sm font-medium text-slate-600">Avg. Vote Time</div>
                                        <div class="w-10 h-10 rounded-lg bg-sky-50 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="text-3xl font-bold text-slate-900" x-text="`${realtimeMetrics.avgTimeToVote}m`"></div>
                                    <div class="text-xs text-slate-500 mt-1">Time per ballot</div>
                                </div>

                                <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="text-sm font-medium text-slate-600">Verification Rate</div>
                                        <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="text-3xl font-bold text-slate-900" x-text="`${realtimeMetrics.verificationSuccessRate}%`"></div>
                                    <div class="text-xs text-slate-500 mt-1">ID verification success</div>
                                </div>

                                <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="text-sm font-medium text-slate-600">Ghost Registrations</div>
                                        <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="text-3xl font-bold text-slate-900" x-text="realtimeMetrics.ghostRegistrations"></div>
                                    <div class="text-xs text-slate-500 mt-1">Unverified accounts</div>
                                </div>
                            </div>

                            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                                <h3 class="text-lg font-semibold mb-4">Votes Per Minute Trend</h3>
                                <div class="h-64">
                                    <canvas id="vpmChart"></canvas>
                                </div>
                                <p class="mt-3 text-xs text-slate-500 flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Real-time data updates every minute
                                </p>
                            </div>
                        </section>

                        <section>
                            <div class="flex items-center gap-3 mb-6">
                                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-purple-100">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">Demographic Insights</h3>
                                    <p class="text-sm text-slate-500">Voter distribution and participation</p>
                                </div>
                            </div>

                            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm mb-6">
                                <h3 class="text-lg font-semibold mb-4">Voter Turnout by Age Group</h3>
                                <div class="h-64 mb-4">
                                    <canvas id="ageChart"></canvas>
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                    <template x-for="group in demographicData.ageGroups" :key="group.label">
                                        <div class="text-center p-3 bg-slate-50 rounded-lg">
                                            <div class="text-xs text-slate-600" x-text="group.label"></div>
                                            <div class="text-lg font-semibold text-slate-900 mt-1" x-text="group.votes"></div>
                                            <div class="text-xs text-slate-500" x-text="`of ${group.total}`"></div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                                    <h3 class="text-lg font-semibold mb-4">Regional Distribution</h3>
                                    <div class="space-y-3">
                                        <template x-for="region in demographicData.regions" :key="region.name">
                                            <div>
                                                <div class="flex items-center justify-between mb-1">
                                                    <span class="text-sm font-medium text-slate-700" x-text="region.name"></span>
                                                    <span class="text-sm text-slate-600" x-text="`${region.votes} (${region.percent}%)`"></span>
                                                </div>
                                                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                                    <div class="h-full bg-indigo-500 rounded-full transition-all" :style="`width: ${region.percent}%`"></div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                                    <h3 class="text-lg font-semibold mb-4">Ballot Collection Channels</h3>
                                    <div class="h-64">
                                        <canvas id="channelsChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section>
                            <div class="flex items-center gap-3 mb-6">
                                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-red-100">
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">Security Monitoring</h3>
                                    <p class="text-sm text-slate-500">Detect and prevent fraudulent activity</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                                <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="text-sm font-medium text-slate-600">Failed Logins</div>
                                        <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="text-3xl font-bold text-slate-900" x-text="realtimeMetrics.failedLogins"></div>
                                    <div class="text-xs text-slate-500 mt-1">Unsuccessful attempts</div>
                                </div>

                                <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="text-sm font-medium text-slate-600">Suspicious IPs</div>
                                        <div class="w-10 h-10 rounded-lg bg-orange-50 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="text-3xl font-bold text-slate-900" x-text="realtimeMetrics.suspiciousIPs"></div>
                                    <div class="text-xs text-slate-500 mt-1">Flagged addresses</div>
                                </div>

                                <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm col-span-2">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="text-sm font-medium text-slate-600">System Status</div>
                                        <div class="flex items-center gap-2">
                                            <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                                            <span class="text-xs font-medium text-emerald-600">All Systems Operational</span>
                                        </div>
                                    </div>
                                    <div class="text-sm text-slate-600">No security threats detected. All verification systems running normally.</div>
                                </div>
                            </div>

                            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                                <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                    Security Audit Log
                                </h3>
                                <div class="space-y-3">
                                    <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-lg">
                                        <div class="w-2 h-2 rounded-full bg-emerald-500 mt-1.5"></div>
                                        <div class="flex-1">
                                            <div class="text-sm font-medium text-slate-900">All verification checks passed</div>
                                            <div class="text-xs text-slate-500 mt-0.5">Last checked: 2 minutes ago</div>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-lg">
                                        <div class="w-2 h-2 rounded-full bg-sky-500 mt-1.5"></div>
                                        <div class="flex-1">
                                            <div class="text-sm font-medium text-slate-900">Automated security scan completed</div>
                                            <div class="text-xs text-slate-500 mt-0.5">15 minutes ago</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>

                    <div x-show="!selectedElection" class="text-center py-20">
                        <svg class="w-20 h-20 mx-auto mb-4 text-slate-300" viewBox="0 0 24 24" fill="none">
                            <path d="M9 11l3 3L22 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <h3 class="text-2xl font-semibold text-slate-900 mb-2">Select an Election to view analytics</h3>
                        <p class="text-sm text-slate-500">Choose an election from the table above to view detailed real-time and historical dashboards</p>
                    </div>
                </div>
            </main>

            <!-- Share Access Modal -->
            <div x-show="showShareModal"
                 class="fixed inset-0 z-[9999] overflow-y-auto"
                 x-cloak>
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="showShareModal"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 transition-opacity bg-slate-900/50 backdrop-blur-sm"
                         @click="showShareModal = false"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                    <div x-show="showShareModal"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="inline-block w-full max-w-md p-8 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-3xl sm:align-middle">

                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Share Access</h3>
                            <button @click="showShareModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                                <i class="ri-close-line text-2xl"></i>
                            </button>
                        </div>

                        <p class="text-sm text-slate-500 mb-6">Enter the email of an administrator you want to share this election with.</p>

                        <div class="space-y-4">
                            <div class="relative">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Admin Email</label>
                                <div class="relative">
                                    <i class="ri-mail-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                    <input type="email"
                                           x-model="shareEmail"
                                           @input.debounce.300ms="searchAdmins()"
                                           placeholder="admin@example.com"
                                           class="w-full pl-12 pr-4 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:ring-0 focus:border-indigo-500/20 focus:bg-white transition-all">
                                </div>

                                <!-- Suggestions Dropdown -->
                                <div x-show="adminSuggestions.length > 0"
                                     class="absolute z-10 w-full mt-2 bg-white border border-slate-100 rounded-2xl shadow-xl overflow-hidden"
                                     x-cloak>
                                    <template x-for="admin in adminSuggestions" :key="admin.id">
                                        <button @click="shareEmail = admin.email; adminSuggestions = []"
                                                class="w-full px-4 py-3 text-left text-sm hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-0">
                                            <div class="font-bold text-slate-900" x-text="admin.name"></div>
                                            <div class="text-xs text-slate-500" x-text="admin.email"></div>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <button @click="shareAccess()"
                                    :disabled="sharing || !shareEmail"
                                    class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                <template x-if="!sharing">
                                    <span>Share Access</span>
                                </template>
                                <template x-if="sharing">
                                    <i class="ri-loader-4-line animate-spin text-lg"></i>
                                </template>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                let vpmChart = null;
                let ageChart = null;
                let channelsChart = null;

                const buildCharts = (data) => {
                    if (vpmChart) vpmChart.destroy();
                    if (ageChart) ageChart.destroy();
                    if (channelsChart) channelsChart.destroy();

                    const vpmCtx = document.getElementById('vpmChart')?.getContext('2d');
                    if (vpmCtx) {
                        vpmChart = new Chart(vpmCtx, {
                            type: 'line',
                            data: {
                                labels: ['10m','9m','8m','7m','6m','5m','4m','3m','2m','Now'],
                                datasets: [{
                                    label: 'Votes/Minute',
                                    data: data.realtimeMetrics.votesPerMinute,
                                    borderColor: '#6366F1',
                                    backgroundColor: 'rgba(99,102,241,0.12)',
                                    tension: 0.35,
                                    fill: true,
                                    pointRadius: 2
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false } },
                                scales: { y: { beginAtZero: true }, x: { grid: { display: false } } }
                            }
                        });
                    }

                    const ageCtx = document.getElementById('ageChart')?.getContext('2d');
                    if (ageCtx) {
                        ageChart = new Chart(ageCtx, {
                            type: 'bar',
                            data: {
                                labels: data.demographicData.ageGroups.map(g => g.label),
                                datasets: [{
                                    label: 'Votes',
                                    data: data.demographicData.ageGroups.map(g => g.votes),
                                    backgroundColor: '#6366F1'
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false } },
                                scales: { x: { grid: { display: false } }, y: { beginAtZero: true } }
                            }
                        });
                    }

                    const channelsCtx = document.getElementById('channelsChart')?.getContext('2d');
                    if (channelsCtx) {
                        channelsChart = new Chart(channelsCtx, {
                            type: 'doughnut',
                            data: {
                                labels: data.demographicData.submissionMethods.map(s => s.method),
                                datasets: [{
                                    data: data.demographicData.submissionMethods.map(s => s.count),
                                    backgroundColor: ['#6366F1', '#8B5CF6', '#10B981']
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { position: 'bottom' } }
                            }
                        });
                    }
                };

                const updateCharts = () => {
                    const component = document.querySelector('[x-data]');
                    if (!component) return;

                    // Alpine.js v3 uses Alpine.$data(el)
                    let data;
                    try {
                        data = window.Alpine ? window.Alpine.$data(component) : (component.__x?.$data);
                    } catch (e) {
                        return;
                    }

                    if (!data || !data.currentElection) {
                        if (vpmChart) vpmChart.destroy(); vpmChart = null;
                        if (ageChart) ageChart.destroy(); ageChart = null;
                        if (channelsChart) channelsChart.destroy(); channelsChart = null;
                        return;
                    }
                    buildCharts(data.currentElection);
                };

                document.addEventListener('alpine:initialized', () => {
                    setTimeout(updateCharts, 120);

                    const component = document.querySelector('[x-data]');
                    if (component) {
                        component.addEventListener('click', () => {
                            setTimeout(updateCharts, 120);
                        });

                        const obs = new MutationObserver(() => setTimeout(updateCharts, 80));
                        obs.observe(component, { attributes: true, subtree: true, childList: true });
                    }
                });
            });
        </script>
    @endpush
@endsection
