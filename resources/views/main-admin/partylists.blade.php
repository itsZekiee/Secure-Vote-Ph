{{-- resources/views/main-admin/partylists.blade.php --}}
@extends('layouts.app-main-admin')

@section('content')
    <div x-data="{
        selectedPartylist: null,
        showCandidates: false,
        candidates: [],
        loading: false,
        searchQuery: '',
        sortBy: 'name',
        filterStatus: '',
        filterElection: '',
        filterDateFrom: '',
        filterDateTo: '',
        showAdvancedFilters: false,
        filteredPartylists: @js($partylists->toArray() ?? []),
        allPartylists: @js($partylists->toArray() ?? []),
        elections: @js(isset($elections) ? $elections->toArray() : []),

        clearAllFilters() {
            this.searchQuery = '';
            this.sortBy = 'name';
            this.filterStatus = '';
            this.filterElection = '';
            this.filterDateFrom = '';
            this.filterDateTo = '';
            this.filterPartylists();
        },

        filterPartylists() {
            let filtered = [...this.allPartylists];

            if (this.searchQuery) {
                const query = this.searchQuery.toLowerCase();
                filtered = filtered.filter(partylist =>
                    partylist.name.toLowerCase().includes(query) ||
                    (partylist.description && partylist.description.toLowerCase().includes(query)) ||
                    (partylist.platform && partylist.platform.toLowerCase().includes(query))
                );
            }

            if (this.filterStatus) {
                filtered = filtered.filter(partylist => partylist.status === this.filterStatus);
            }

            if (this.filterElection) {
                filtered = filtered.filter(partylist => partylist.election_id == this.filterElection);
            }

            if (this.filterDateFrom) {
                filtered = filtered.filter(partylist => new Date(partylist.created_at) >= new Date(this.filterDateFrom));
            }
            if (this.filterDateTo) {
                filtered = filtered.filter(partylist => new Date(partylist.created_at) <= new Date(this.filterDateTo));
            }

            switch (this.sortBy) {
                case 'name':
                    filtered.sort((a, b) => a.name.localeCompare(b.name));
                    break;
                case 'created_at_desc':
                    filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                    break;
                case 'created_at_asc':
                    filtered.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
                    break;
                case 'candidates_count':
                    filtered.sort((a, b) => (b.candidates_count || 0) - (a.candidates_count || 0));
                    break;
                case 'status':
                    filtered.sort((a, b) => a.status.localeCompare(b.status));
                    break;
            }

            this.filteredPartylists = filtered;
        }
    }"
         x-init="
        $watch('searchQuery', () => filterPartylists());
        $watch('sortBy', () => filterPartylists());
        $watch('filterStatus', () => filterPartylists());
        $watch('filterElection', () => filterPartylists());
        $watch('filterDateFrom', () => filterPartylists());
        $watch('filterDateTo', () => filterPartylists());
    "
         class="flex min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/40 to-indigo-50/30">

        <!-- Sidebar -->
        <x-admin-sidebar />

        <!-- Main Content -->
        <main class="flex-1 min-h-screen">

            <!-- Mobile Header -->
            <header x-show="isMobile"
                    class="lg:hidden bg-white shadow-sm border-b px-4 py-3 flex items-center justify-between">
                <button @click="collapsed = false"
                        class="p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                    <i class="ri-menu-fold-line text-lg rotate-180"></i>
                </button>
                <h1 class="text-lg font-semibold text-slate-800">Partylists</h1>
                <div class="w-10"></div>
            </header>

            <!-- Top Navigation Bar -->
            <div class="bg-white/80 backdrop-blur-xl border-b border-gray-200/50 sticky top-0 z-40">
                <div class="px-8 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <i class="ri-stack-line text-white text-lg"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900">Party Lists</h2>
                                    <p class="text-sm text-gray-600">Manage your organization's party lists</p>
                                </div>
                            </div>
                            <div class="h-8 w-px bg-gray-200"></div>
                            <nav class="flex items-center space-x-2 text-sm">
                                <span class="text-gray-500">Admin</span>
                                <i class="ri-arrow-right-s-line text-gray-400"></i>
                                <span class="text-gray-900 font-semibold">Party Lists</span>
                            </nav>
                        </div>

                        <div class="flex items-center space-x-3">
                            <div class="flex items-center space-x-6 text-sm">
                                <div class="flex items-center space-x-2">
                                    <span class="font-semibold text-gray-900" x-text="filteredPartylists.length"></span>
                                    <span class="text-gray-600">Total</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="font-semibold text-green-600" x-text="filteredPartylists.filter(p => p.status === 'active').length"></span>
                                    <span class="text-gray-600">Active</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="font-semibold text-amber-600" x-text="filteredPartylists.filter(p => p.status === 'pending').length"></span>
                                    <span class="text-gray-600">Pending</span>
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
                        <div class="px-6 py-4 border-b border-gray-200/60 bg-gradient-to-r from-purple-50/50 via-indigo-50/50 to-blue-50/50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-white/80 rounded-lg flex items-center justify-center shadow-sm border border-gray-200/50">
                                        <i class="ri-search-line text-gray-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-bold text-gray-900">Search & Filter</h3>
                                        <p class="text-[11px] text-gray-500 font-medium">Find and manage party lists</p>
                                    </div>
                                </div>
                                <a href="{{ route('admin.partylists.create') }}"
                                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition-all shadow-sm">
                                    <i class="ri-add-line mr-1.5"></i>
                                    New Party List
                                </a>
                            </div>
                        </div>

                        <!-- Filter Controls -->
                        <div class="p-6">
                            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                                <!-- Search Box -->
                                <div class="lg:col-span-1">
                                    <label class="block text-xs font-bold text-gray-800 mb-2">
                                        <i class="ri-search-line text-purple-600 mr-1.5"></i>
                                        Search
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="ri-search-line text-gray-400 text-sm"></i>
                                        </div>
                                        <input type="text"
                                               x-model="searchQuery"
                                               placeholder="Search..."
                                               class="w-full pl-10 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all bg-white">
                                    </div>
                                </div>

                                <!-- Sort By Filter -->
                                <div class="lg:col-span-1">
                                    <label class="block text-xs font-bold text-gray-800 mb-2">
                                        <i class="ri-sort-asc text-purple-600 mr-1.5"></i>
                                        Sort By
                                    </label>
                                    <div class="relative">
                                        <select x-model="sortBy"
                                                class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all bg-white appearance-none">
                                            <option value="name">Name (A-Z)</option>
                                            <option value="created_at_desc">Newest First</option>
                                            <option value="created_at_asc">Oldest First</option>
                                            <option value="candidates_count">Most Candidates</option>
                                            <option value="status">Status</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <i class="ri-arrow-down-s-line text-gray-400"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Filter -->
                                <div class="lg:col-span-1">
                                    <label class="block text-xs font-bold text-gray-800 mb-2">
                                        <i class="ri-checkbox-circle-line text-purple-600 mr-1.5"></i>
                                        Status
                                    </label>
                                    <div class="relative">
                                        <select x-model="filterStatus"
                                                class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all bg-white appearance-none">
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

                                <!-- Election Filter -->
                                <div class="lg:col-span-1">
                                    <label class="block text-xs font-bold text-gray-800 mb-2">
                                        <i class="ri-calendar-event-line text-purple-600 mr-1.5"></i>
                                        Election
                                    </label>
                                    <div class="relative">
                                        <select x-model="filterElection"
                                                class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all bg-white appearance-none">
                                            <option value="">All Elections</option>
                                            <template x-for="election in elections" :key="election.id">
                                                <option :value="election.id" x-text="election.name || election.title"></option>
                                            </template>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <i class="ri-arrow-down-s-line text-gray-400"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <i class="ri-file-list-3-line text-purple-600 text-sm"></i>
                                    <span class="text-xs font-semibold text-gray-600">
                                        Showing <span x-text="filteredPartylists.length" class="text-gray-900 font-bold"></span> of <span x-text="allPartylists.length"></span> results
                                    </span>
                                </div>
                                <button @click="clearAllFilters()"
                                        class="inline-flex items-center px-3 py-1.5 text-xs font-bold text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-all">
                                    <i class="ri-refresh-line mr-1.5"></i>
                                    Reset
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Party Lists Table -->
                    <div class="bg-white rounded-xl border border-gray-200/60 shadow-sm overflow-hidden">
                        <div class="responsive-table-container">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-slate-50/50">
                                <tr class="text-left">
                                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Partylist Name</th>
                                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Organization</th>
                                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Created Date</th>
                                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Actions</th>
                                </tr>
                                </thead>

                                <tbody class="divide-y divide-slate-100 text-sm text-gray-700">
                                <template x-for="partylist in filteredPartylists" :key="partylist.id">
                                    <tr class="hover:bg-purple-50/30 transition-colors duration-150">
                                        <!-- Partylist Name -->
                                        <td class="px-6 py-4 align-middle">
                                            <div class="flex items-center space-x-3">
                                                <img :src="partylist.logo ? ('/storage/' + partylist.logo) : '/images/placeholder-logo.png'"
                                                     :alt="partylist.name + ' logo'"
                                                     class="w-10 h-10 object-cover rounded-lg border border-gray-200" />
                                                <div>
                                                    <div class="font-bold text-slate-900 text-sm" x-text="partylist.name"></div>
                                                    <div class="text-[10px] text-gray-500 font-medium" x-text="partylist.acronym || 'No acronym'"></div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Organization -->
                                        <td class="px-6 py-4 align-middle">
                                            <span class="text-gray-700 font-bold text-xs"
                                                  x-text="partylist.organization ? (partylist.organization.name || partylist.organization_name) : '—'"></span>
                                        </td>

                                        <!-- Created Date -->
                                        <td class="px-6 py-4 align-middle text-gray-500 text-xs font-medium"
                                            x-text="new Date(partylist.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })"></td>

                                        <!-- Actions -->
                                        <td class="px-6 py-4 align-middle text-right">
                                            <div class="inline-flex items-center space-x-2">
                                                <a :href="`/admin/partylists/${partylist.id}`"
                                                   class="w-8 h-8 bg-white border border-slate-200 rounded-lg flex items-center justify-center text-slate-500 hover:text-purple-600 hover:border-purple-200 transition-all">
                                                    <i class="ri-eye-line text-sm"></i>
                                                </a>
                                                <a :href="`/admin/partylists/${partylist.id}/edit`"
                                                   class="w-8 h-8 bg-white border border-slate-200 rounded-lg flex items-center justify-center text-slate-500 hover:text-blue-600 hover:border-blue-200 transition-all">
                                                    <i class="ri-edit-line text-sm"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                </template>

                                <!-- Empty state -->
                                <tr x-show="filteredPartylists.length === 0">
                                    <td class="px-6 py-12 text-center text-gray-500" colspan="4">
                                        <div class="flex flex-col items-center space-y-3">
                                            <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center">
                                                <i class="ri-inbox-line text-gray-400 text-2xl"></i>
                                            </div>
                                            <p class="font-semibold">No party lists found</p>
                                            <p class="text-sm">Try adjusting your search or filters</p>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Footer -->
                        <div class="px-6 py-4 border-t border-gray-100 bg-white flex items-center justify-between text-sm text-gray-600">
                            <div>Showing <span class="font-semibold" x-text="filteredPartylists.length"></span> of <span x-text="allPartylists.length"></span> results</div>
                            <div class="flex items-center space-x-3">
                                <button @click="filteredPartylists = filteredPartylists.slice().reverse()"
                                        class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-xs font-medium hover:bg-gray-50">
                                    <i class="ri-sort-desc mr-1"></i>
                                    Toggle Order
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection
