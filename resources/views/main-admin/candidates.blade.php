<!doctype html>
<!-- blade -->
@php
    use Illuminate\Support\Collection;
    if (!isset($candidates) || $candidates === null) {
        $candidates = collect();
    }
    $candidates = $candidates instanceof Collection ? $candidates : collect($candidates);
@endphp

@extends('layouts.app-main-admin')

@section('content')
    <div x-data="{
        isMobile: window.innerWidth < 1024,
        collapsed: false,
        searchQuery: '',
        sortBy: 'created_at_desc',
        filterElection: '',
        filterPosition: '',
        filterPartylist: '',
        filterStatus: '',
        allCandidates: @js($candidates->toArray() ?? []),
        filteredCandidates: @js($candidates->toArray() ?? []),

        clearFilters() {
            this.searchQuery = '';
            this.sortBy = 'created_at_desc';
            this.filterElection = '';
            this.filterPosition = '';
            this.filterPartylist = '';
            this.filterStatus = '';
            this.filter();
        },

        filter() {
            let list = [...this.allCandidates];

            if (this.searchQuery) {
                const q = this.searchQuery.toLowerCase();
                list = list.filter(c =>
                    (c.user && (c.user.name || '')).toLowerCase().includes(q) ||
                    (c.name || '').toLowerCase().includes(q) ||
                    (c.position && (c.position.title || c.position.name || '')).toLowerCase().includes(q) ||
                    (c.partylist && (c.partylist.name || '')).toLowerCase().includes(q) ||
                    (c.election && (c.election.name || c.election.title || '')).toLowerCase().includes(q)
                );
            }

            if (this.filterElection) {
                list = list.filter(c => String(c.election_id || c.election?.id || '') === String(this.filterElection));
            }
            if (this.filterPosition) {
                list = list.filter(c => String(c.position_id || c.position?.id || '') === String(this.filterPosition));
            }
            if (this.filterPartylist) {
                list = list.filter(c => String(c.partylist_id || c.partylist?.id || '') === String(this.filterPartylist));
            }
            if (this.filterStatus) {
                list = list.filter(c => (c.status || '').toLowerCase() === this.filterStatus.toLowerCase());
            }

            switch (this.sortBy) {
                case 'name':
                    list.sort((a,b) => (a.user?.name || a.name || '').localeCompare(b.user?.name || b.name || ''));
                    break;
                case 'votes_desc':
                    list.sort((a,b) => (b.votes_count || 0) - (a.votes_count || 0));
                    break;
                case 'votes_asc':
                    list.sort((a,b) => (a.votes_count || 0) - (b.votes_count || 0));
                    break;
                case 'created_at_asc':
                    list.sort((a,b) => new Date(a.created_at) - new Date(b.created_at));
                    break;
                default:
                    list.sort((a,b) => new Date(b.created_at) - new Date(a.created_at));
            }

            this.filteredCandidates = list;
        }
    }"
         x-init="
        window.addEventListener('resize', () => { isMobile = window.innerWidth < 1024 });
        $watch('searchQuery', () => filter());
        $watch('sortBy', () => filter());
        $watch('filterElection', () => filter());
        $watch('filterPosition', () => filter());
        $watch('filterPartylist', () => filter());
        $watch('filterStatus', () => filter());
    "
         class="flex min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50">

        <x-admin-sidebar />

        <main class="flex-1">
            <!-- Topbar -->
            <div class="bg-white/80 backdrop-blur-sm border-b sticky top-0 z-40">
                <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-600 to-indigo-600 flex items-center justify-center shadow">
                            <i class="ri-user-3-line text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-semibold text-gray-900">Candidates</h1>
                            <p class="text-sm text-gray-500">Manage candidates across elections, positions and parties</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-6 py-8">
                <!-- Filters & Summary -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-8">
                    <div class="px-6 py-5 bg-gradient-to-r from-white via-purple-50 to-white border-b">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">Search & Filters</h2>
                                <p class="text-sm text-gray-500">Filter candidates by election, position, party and status</p>
                            </div>
                            <div class="text-sm text-gray-600">Total: <span class="font-semibold text-gray-900" x-text="allCandidates.length"></span></div>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 lg:grid-cols-6 gap-4">
                            <div class="lg:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                                <input type="text" x-model="searchQuery" placeholder="Search by name, election, position, party..."
                                       class="w-full px-4 py-3 border rounded-lg bg-white focus:ring-2 focus:ring-purple-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sort</label>
                                <select x-model="sortBy" class="w-full px-4 py-3 border rounded-lg bg-white">
                                    <option value="created_at_desc">Newest</option>
                                    <option value="created_at_asc">Oldest</option>
                                    <option value="name">Name (A-Z)</option>
                                    <option value="votes_desc">Most Votes</option>
                                    <option value="votes_asc">Least Votes</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Election</label>
                                <select x-model="filterElection" class="w-full px-4 py-3 border rounded-lg bg-white">
                                    <option value="">All Elections</option>
                                    <template x-for="c in allCandidates.map(a => a.election).filter(Boolean).filter((v,i,a)=>a.findIndex(x=>x?.id===v.id)===i)" :key="c.id">
                                        <option :value="c.id" x-text="c.name || c.title"></option>
                                    </template>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                                <select x-model="filterPosition" class="w-full px-4 py-3 border rounded-lg bg-white">
                                    <option value="">All Positions</option>
                                    <template x-for="p in allCandidates.map(a => a.position).filter(Boolean).filter((v,i,a)=>a.findIndex(x=>x?.id===v.id)===i)" :key="p.id">
                                        <option :value="p.id" x-text="p.title || p.name"></option>
                                    </template>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Partylist</label>
                                <select x-model="filterPartylist" class="w-full px-4 py-3 border rounded-lg bg-white">
                                    <option value="">All Partylists</option>
                                    <template x-for="pl in allCandidates.map(a => a.partylist).filter(Boolean).filter((v,i,a)=>a.findIndex(x=>x?.id===v.id)===i)" :key="pl.id">
                                        <option :value="pl.id" x-text="pl.name"></option>
                                    </template>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select x-model="filterStatus" class="w-full px-4 py-3 border rounded-lg bg-white">
                                    <option value="">All</option>
                                    <option value="active">Active</option>
                                    <option value="pending">Pending</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-600">
                                <span class="font-medium text-gray-900" x-text="filteredCandidates.length"></span> results shown
                            </div>
                            <div class="flex items-center gap-3">
                                <button @click="clearFilters()" class="px-4 py-2 bg-white border rounded-lg text-sm text-gray-700 hover:bg-gray-50">Clear</button>
                                <a href="{{ route('admin.candidates.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">Create Candidate</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Candidates Table (updated columns) -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b bg-gradient-to-r from-white via-purple-50 to-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Candidates</h3>
                                <p class="text-sm text-gray-600">List of candidates</p>
                            </div>
                            <div class="text-sm text-gray-600">Showing <span class="font-semibold text-gray-900" x-text="filteredCandidates.length"></span></div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-white">
                            <tr class="text-left text-sm font-semibold text-gray-700">
                                <th class="px-6 py-3">Candidate Name</th>
                                <th class="px-6 py-3">Election</th>
                                <th class="px-6 py-3">Position</th>
                                <th class="px-6 py-3">Partylist</th>
                                <th class="px-6 py-3">Created Date</th>
                                <th class="px-6 py-3 text-right">Action</th>
                            </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-100 text-sm text-gray-700">
                            <template x-for="candidate in filteredCandidates" :key="candidate.id">
                                <tr class="hover:bg-purple-50/30 transition-colors duration-150">
                                    <!-- Candidate Name -->
                                    <td class="px-6 py-4 align-middle">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-gray-500">
                                                <i class="ri-user-fill"></i>
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-900" x-text="candidate.user?.name || candidate.name || '—'"></div>
                                                <div class="text-xs text-gray-500" x-text="candidate.user?.email || candidate.email || ''"></div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Election -->
                                    <td class="px-6 py-4 align-middle">
                                        <div class="text-sm text-gray-700" x-text="candidate.election?.name || candidate.election?.title || '—'"></div>
                                    </td>

                                    <!-- Position -->
                                    <td class="px-6 py-4 align-middle text-gray-700" x-text="candidate.position?.title || candidate.position?.name || '—'"></td>

                                    <!-- Partylist -->
                                    <td class="px-6 py-4 align-middle text-gray-700" x-text="candidate.partylist?.name || '—'"></td>

                                    <!-- Created Date -->
                                    <td class="px-6 py-4 align-middle text-gray-600" x-text="candidate.created_at ? (new Date(candidate.created_at)).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '—'"></td>

                                    <!-- Action (Edit | Delete) -->
                                    <td class="px-6 py-4 align-middle text-right">
                                        <div class="inline-flex items-center space-x-2">
                                            <a :href="`/admin/candidates/${candidate.id}/edit`"
                                               class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">
                                                <i class="ri-edit-line mr-2"></i>
                                                Edit
                                            </a>

                                            <button type="button"
                                                    @click="if (confirm('Are you sure you want to delete this candidate?')) { deleteCandidate(candidate.id) }"
                                                    class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700">
                                                <i class="ri-delete-bin-line mr-2"></i>
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>

                            <!-- Empty state -->
                            <tr x-show="filteredCandidates.length === 0">
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <div class="space-y-3">
                                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto">
                                            <i class="ri-inbox-line text-gray-400 text-2xl"></i>
                                        </div>
                                        <p class="font-semibold">No candidates found</p>
                                        <p class="text-sm">Adjust filters or create a new candidate</p>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="px-6 py-4 border-t bg-white flex items-center justify-between text-sm text-gray-600">
                        <div>Showing <span class="font-semibold text-gray-900" x-text="filteredCandidates.length"></span> candidates</div>
                        <div class="flex items-center space-x-3">
                            <button @click="filteredCandidates = filteredCandidates.slice().reverse()" class="px-4 py-2 bg-white border rounded-lg text-xs">Toggle Order</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        function deleteCandidate(id) {
            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            const csrf = tokenMeta ? tokenMeta.getAttribute('content') : '';
            if (!csrf) {
                alert('CSRF token missing.');
                return;
            }

            fetch(`/admin/candidates/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            }).then(res => {
                if (res.ok) {
                    location.reload();
                } else {
                    res.json().then(data => {
                        alert(data.message || 'Failed to delete candidate.');
                    }).catch(()=> alert('Failed to delete candidate.'));
                }
            }).catch(() => alert('Network error while deleting candidate.'));
        }
    </script>
@endsection
