{{-- blade --}}
@extends('layouts.app-main-admin')

@section('content')
    <div x-data="{
        collapsed: false,
        isMobile: window.innerWidth < 1024,
        selectedElection: null,
        elections: [
            { id: 1, name: 'Presidential Election 2024', status: 'active', totalVotes: 8542, turnoutRate: 72.5, newVoters: 1234, participationRate: 68.3, votes: [2145, 1876, 1543, 1234, 987, 757], labels: ['Candidate A', 'Candidate B', 'Candidate C', 'Candidate D', 'Candidate E', 'Candidate F'] },
            { id: 2, name: 'Barangay Election 2024', status: 'scheduled', totalVotes: 0, turnoutRate: 0, newVoters: 456, participationRate: 0, votes: [], labels: [] },
            { id: 3, name: 'Municipal Election 2023', status: 'completed', totalVotes: 12456, turnoutRate: 85.2, newVoters: 789, participationRate: 82.1, votes: [3456, 2987, 2345, 1876, 1234, 558], labels: ['Candidate A', 'Candidate B', 'Candidate C', 'Candidate D', 'Candidate E', 'Candidate F'] }
        ],
        get currentStats() {
            if (this.selectedElection) {
                const election = this.elections.find(e => e.id === this.selectedElection);
                return election || this.defaultStats;
            }
            return this.defaultStats;
        },
        get defaultStats() {
            return {
                totalVotes: this.elections.reduce((sum, e) => sum + e.totalVotes, 0),
                turnoutRate: (this.elections.reduce((sum, e) => sum + e.turnoutRate, 0) / this.elections.length).toFixed(1),
                newVoters: this.elections.reduce((sum, e) => sum + e.newVoters, 0),
                participationRate: (this.elections.reduce((sum, e) => sum + e.participationRate, 0) / this.elections.length).toFixed(1)
            };
        }
    }"
         x-init="window.addEventListener('resize', () => { isMobile = window.innerWidth < 1024 })"
         class="flex min-h-screen bg-slate-50 text-slate-800">

        <x-admin-sidebar />

        <div class="flex-1 flex flex-col min-h-screen">
            <x-admin-header />

            <main class="flex-1 p-6 pb-8">
                <div class="max-w-7xl mx-auto">
                    <header class="mb-8 flex items-start justify-between gap-6">
                        <div>
                            <p class="mt-1 text-sm text-slate-500">Executive summary of elections and participation metrics.</p>
                        </div>

                        <div class="flex items-center gap-3">
                            <label for="election-select" class="sr-only">Filter election</label>
                            <select id="election-select" x-model="selectedElection"
                                    class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option :value="null" selected>All elections</option>
                                <template x-for="e in elections" :key="e.id">
                                    <option :value="e.id" x-text="e.name"></option>
                                </template>
                            </select>
                            <button @click="selectedElection = null"
                                    class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm hover:bg-slate-50">
                                <svg class="w-4 h-4 text-slate-600" viewBox="0 0 24 24" fill="none" aria-hidden>
                                    <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Reset
                            </button>
                        </div>
                    </header>

                    <!-- Stats Grid -->
                    <section aria-labelledby="overview-heading" class="mb-8">
                        <h2 id="overview-heading" class="sr-only">Overview statistics</h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            <!-- Total Votes -->
                            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-xs font-medium text-slate-500">Total Votes</p>
                                        <p class="mt-2 text-2xl font-semibold" x-text="currentStats.totalVotes.toLocaleString()">0</p>
                                        <p class="mt-1 text-xs text-slate-500">Across selected elections</p>
                                    </div>
                                    <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-blue-50">
                                        <!-- Replaced `ri-checkbox-multiple-line` with inline SVG for crisp, consistent iconography -->
                                        <svg class="w-6 h-6 text-blue-600" viewBox="0 0 24 24" fill="none" aria-hidden>
                                            <path d="M9 11l2 2 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            <rect x="3" y="3" width="18" height="18" rx="3" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" opacity="0.08" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Turnout Rate -->
                            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-xs font-medium text-slate-500">Turnout Rate</p>
                                        <div class="mt-2 flex items-baseline gap-2">
                                            <p class="text-2xl font-semibold"><span x-text="currentStats.turnoutRate">0</span>%</p>
                                        </div>
                                        <p class="mt-1 text-xs text-slate-500">Voter turnout</p>
                                    </div>
                                    <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-purple-50">
                                        <svg class="w-6 h-6 text-purple-600" viewBox="0 0 24 24" fill="none" aria-hidden>
                                            <path d="M21 12A9 9 0 1 1 3 5.7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M12 7v6l4 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- New Registered Voters -->
                            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-xs font-medium text-slate-500">New Registered Voters</p>
                                        <p class="mt-2 text-2xl font-semibold" x-text="currentStats.newVoters.toLocaleString()">0</p>
                                        <p class="mt-1 text-xs text-slate-500">Since last reporting period</p>
                                    </div>
                                    <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-orange-50">
                                        <svg class="w-6 h-6 text-orange-600" viewBox="0 0 24 24" fill="none" aria-hidden>
                                            <path d="M12 5v14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M5 12h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Participation Rate -->
                            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-xs font-medium text-slate-500">Participation Rate</p>
                                        <p class="mt-2 text-2xl font-semibold"><span x-text="currentStats.participationRate">0</span>%</p>
                                        <p class="mt-1 text-xs text-slate-500">Active participation</p>
                                    </div>
                                    <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-indigo-50">
                                        <svg class="w-6 h-6 text-indigo-600" viewBox="0 0 24 24" fill="none" aria-hidden>
                                            <path d="M3 12h3l3 8 4-16 3 8h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Analytics + Recent Elections -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <section class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                            <h3 class="text-lg font-semibold mb-4">
                                <span x-show="!selectedElection">Overall Analytics</span>
                                <span x-show="selectedElection" x-text="elections.find(e => e.id === selectedElection)?.name">Election Analytics</span>
                            </h3>

                            <div class="space-y-4">
                                <div class="h-64 bg-slate-50 rounded-lg flex items-center justify-center">
                                    <!-- Chart placeholder - Chart.js will mount here -->
                                    <canvas id="electionChart" class="w-full h-full"></canvas>
                                </div>

                                <div x-show="selectedElection && currentStats.votes && currentStats.votes.length > 0" class="space-y-2">
                                    <template x-for="(v, idx) in currentStats.votes" :key="idx">
                                        <div class="flex items-center justify-between">
                                            <div class="text-sm text-slate-700" x-text="currentStats.labels[idx] ?? `Option ${idx+1}`"></div>
                                            <div class="text-sm font-medium text-slate-900" x-text="v.toLocaleString()"></div>
                                        </div>
                                    </template>
                                </div>

                                <div x-show="!selectedElection || !currentStats.votes || currentStats.votes.length === 0" class="text-sm text-slate-500">
                                    No detailed vote distribution available for the selected scope.
                                </div>
                            </div>
                        </section>

                        <aside class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold">Recent Elections</h3>
                                <a href="{{ route('admin.elections.index') }}" class="text-sm text-indigo-600 hover:underline">Manage</a>
                            </div>

                            <ul class="space-y-3">
                                <template x-for="e in elections" :key="e.id">
                                    <li class="flex items-center justify-between">
                                        <div>
                                            <div class="text-sm font-medium" x-text="e.name"></div>
                                            <div class="text-xs text-slate-500 mt-1">
                                                <span x-text="e.totalVotes.toLocaleString()"></span> votes ·
                                                <span x-text="e.status"></span>
                                            </div>
                                        </div>
                                        <div class="text-sm text-slate-700">
                                            <span x-text="e.totalVotes ? e.totalVotes.toLocaleString() : '0'"></span>
                                        </div>
                                    </li>
                                </template>
                            </ul>

                            <div class="mt-6">
                                <button @click="selectedElection = null" class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm bg-white hover:bg-slate-50">
                                    Reset selection
                                </button>
                            </div>
                        </aside>
                    </div>
                </div>
            </main>
        </div>

        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('alpine:init', () => {
                    let chart = null;

                    const createChart = (labels, data) => {
                        const ctx = document.getElementById('electionChart').getContext('2d');
                        if (chart) chart.destroy();
                        chart = new Chart(ctx, {
                            type: 'doughnut',
                            data: {
                                labels: labels,
                                datasets: [{ data: data, backgroundColor: ['#6366F1','#8B5CF6','#06B6D4','#F59E0B','#EF4444','#10B981'] }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { position: 'bottom' } }
                            }
                        });
                    };

                    Alpine.effect(() => {
                        const al = document.querySelector('[x-data]').__x.$data;
                        if (!al) return;
                        const stats = al.selectedElection ? al.currentStats : al.defaultStats;
                        if (stats && stats.votes && stats.votes.length) {
                            createChart(stats.labels, stats.votes);
                        } else {
                            if (chart) { chart.destroy(); chart = null; }
                        }
                    });
                });
            </script>
        @endpush
    </div>
@endsection
