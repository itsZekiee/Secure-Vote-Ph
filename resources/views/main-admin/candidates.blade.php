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
        $watch('searchQuery', () => filter());
        $watch('sortBy', () => filter());
        $watch('filterElection', () => filter());
        $watch('filterPosition', () => filter());
        $watch('filterPartylist', () => filter());
        $watch('filterStatus', () => filter());
    "
         class="flex min-h-screen bg-gradient-to-br from-slate-50 via-purple-50/30 to-indigo-50/20">

        <x-admin-sidebar />

        <main class="flex-1 min-h-screen">
            <!-- Top Navigation Bar -->
            <div class="bg-white/80 backdrop-blur-xl border-b border-gray-200/50 sticky top-0 z-40">
                <div class="px-8 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <i class="ri-user-star-line text-white text-lg"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900">Candidates</h2>
                                    <p class="text-sm text-gray-600">Election nominees & profiles</p>
                                </div>
                            </div>
                            <div class="h-8 w-px bg-gray-200"></div>
                            <nav class="flex items-center space-x-2 text-sm">
                                <span class="text-gray-500">Admin</span>
                                <i class="ri-arrow-right-s-line text-gray-400"></i>
                                <span class="text-gray-900 font-semibold">Candidates</span>
                            </nav>
                        </div>

                        <div class="flex items-center space-x-3">
                            <div class="flex items-center space-x-6 text-sm">
                                <div class="flex items-center space-x-2">
                                    <span class="font-semibold text-gray-900" x-text="filteredCandidates.length"></span>
                                    <span class="text-gray-600">Total</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="px-6 py-6">
                <div class="max-w-7xl mx-auto">
                    <!-- Search and Filter Section -->
                    <div class="bg-white rounded-xl border border-gray-200/60 shadow-sm overflow-hidden mb-6">
                        <!-- Header with Action Button -->
                        <div class="px-6 py-4 border-b border-gray-200/60 bg-gradient-to-r from-purple-50/50 via-indigo-50/50 to-slate-50/50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-white/80 rounded-lg flex items-center justify-center shadow-sm border border-gray-200/50">
                                        <i class="ri-filter-2-line text-gray-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-bold text-gray-900">Search & Filters</h3>
                                        <p class="text-[11px] text-gray-500 font-medium">Manage candidate views efficiently</p>
                                    </div>
                                </div>
                                <a href="{{ route('admin.candidates.create') }}"
                                   class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-xs font-bold rounded-lg hover:bg-purple-700 transition-all shadow-sm">
                                    <i class="ri-user-add-line mr-1.5"></i>
                                    New Candidate
                                </a>
                            </div>
                        </div>

                        <!-- Filter Controls -->
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-gray-800 mb-2">Search Candidate</label>
                                    <div class="relative">
                                        <i class="ri-search-line absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                        <input type="text" x-model="searchQuery"
                                               placeholder="Name, position, party or election..."
                                               class="w-full pl-10 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all bg-white">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-800 mb-2">Sort Results</label>
                                    <div class="relative">
                                        <select x-model="sortBy" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500/20 bg-white appearance-none">
                                            <option value="created_at_desc">Newest First</option>
                                            <option value="name">Name (A-Z)</option>
                                            <option value="votes_desc">Popularity</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <i class="ri-arrow-down-s-line text-gray-400"></i>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-800 mb-2">Status</label>
                                    <div class="relative">
                                        <select x-model="filterStatus" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500/20 bg-white appearance-none">
                                            <option value="">All Statuses</option>
                                            <option value="active">Active</option>
                                            <option value="pending">Pending</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <i class="ri-arrow-down-s-line text-gray-400"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <span class="text-xs font-semibold text-gray-500" x-text="filteredCandidates.length + ' results found'"></span>
                                </div>
                                <button @click="clearFilters()"
                                        class="inline-flex items-center px-3 py-1.5 text-xs font-bold text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-all">
                                    <i class="ri-refresh-line mr-1.5"></i>
                                    Reset
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Candidates Table -->
                    <div class="bg-white rounded-xl border border-gray-200/60 shadow-sm overflow-hidden">
                        <div class="responsive-table-container">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-slate-50/50">
                                <tr>
                                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Candidate Profile</th>
                                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Election & Position</th>
                                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Affiliation</th>
                                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Management</th>
                                </tr>
                                </thead>

                                <tbody class="divide-y divide-slate-100">
                                <template x-for="candidate in filteredCandidates" :key="candidate.id">
                                    <tr class="hover:bg-purple-50/30 transition-all group">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center text-slate-500 font-bold text-xs group-hover:scale-110 transition-transform shadow-sm">
                                                    <i class="ri-user-3-fill text-lg"></i>
                                                </div>
                                                <div>
                                                    <p class="font-bold text-slate-900 leading-tight text-sm" x-text="candidate.user?.name || candidate.name || '—'"></p>
                                                    <p class="text-[10px] font-bold text-purple-500 mt-0.5 uppercase tracking-wider" x-text="candidate.user?.email || candidate.email || ''"></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="space-y-0.5">
                                                <p class="text-xs font-bold text-slate-700" x-text="candidate.position?.title || candidate.position?.name || '—'"></p>
                                                <p class="text-[10px] font-bold text-slate-400 italic" x-text="candidate.election?.name || candidate.election?.title || '—'"></p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 rounded-lg text-[10px] font-bold text-slate-600 uppercase tracking-tight">
                                                <i class="ri-community-line"></i>
                                                <span x-text="candidate.partylist?.name || 'Independent'"></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end gap-2 transition-all">
                                                <a :href="`/admin/candidates/${candidate.id}/edit`"
                                                   class="w-8 h-8 bg-white border border-slate-200 rounded-lg flex items-center justify-center text-slate-500 hover:text-indigo-600 hover:border-indigo-200 hover:shadow transition-all">
                                                    <i class="ri-edit-line text-sm"></i>
                                                </a>
                                                <button type="button" @click="if (confirm('Delete this candidate profile?')) { deleteCandidate(candidate.id) }"
                                                        class="w-8 h-8 bg-red-50 text-red-600 rounded-lg flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-sm">
                                                    <i class="ri-delete-bin-line text-sm"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                </tbody>
                            </table>
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
