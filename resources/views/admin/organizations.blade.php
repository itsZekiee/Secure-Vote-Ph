@extends('layouts.app-main-admin')

@section('content')
    <div x-data="{
        selectedOrg: null,
        showMembers: false,
        members: [],
        loading: false,
        searchQuery: '',
        sortBy: 'name',
        filterStatus: '',
        filterDateFrom: '',
        filterDateTo: '',
        filterMembersMin: '',
        filterMembersMax: '',
        showAdvancedFilters: false,
        filteredOrganizations: @js($organizations->toArray()),
        allOrganizations: @js($organizations->toArray()),

        clearAllFilters() {
            this.searchQuery = '';
            this.sortBy = 'name';
            this.filterStatus = '';
            this.filterDateFrom = '';
            this.filterDateTo = '';
            this.filterMembersMin = '';
            this.filterMembersMax = '';
            this.filterOrganizations();
        },

        filterOrganizations() {
            let filtered = [...this.allOrganizations];

            // Search filter
            if (this.searchQuery) {
                const query = this.searchQuery.toLowerCase();
                filtered = filtered.filter(org =>
                    (org.name && org.name.toLowerCase().includes(query)) ||
                    (org.description && org.description.toLowerCase().includes(query)) ||
                    (org.email && org.email.toLowerCase().includes(query)) ||
                    (org.slug && org.slug.toLowerCase().includes(query))
                );
            }

            // Status filter
            if (this.filterStatus) {
                filtered = filtered.filter(org => org.status === this.filterStatus);
            }

            // Date range filter
            if (this.filterDateFrom) {
                filtered = filtered.filter(org => new Date(org.created_at) >= new Date(this.filterDateFrom));
            }
            if (this.filterDateTo) {
                filtered = filtered.filter(org => new Date(org.created_at) <= new Date(this.filterDateTo));
            }

            // Member count filter
            if (this.filterMembersMin) {
                filtered = filtered.filter(org => (org.members_count || 0) >= parseInt(this.filterMembersMin));
            }
            if (this.filterMembersMax) {
                filtered = filtered.filter(org => (org.members_count || 0) <= parseInt(this.filterMembersMax));
            }

            // Sort
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
                case 'members_count':
                    filtered.sort((a, b) => (b.members_count || 0) - (a.members_count || 0));
                    break;
                case 'status':
                    filtered.sort((a, b) => a.status.localeCompare(b.status));
                    break;
            }

            this.filteredOrganizations = filtered;
        }
    }"
         x-init="
        $watch('searchQuery', () => filterOrganizations());
        $watch('sortBy', () => filterOrganizations());
        $watch('filterStatus', () => filterOrganizations());
        $watch('filterDateFrom', () => filterOrganizations());
        $watch('filterDateTo', () => filterOrganizations());
        $watch('filterMembersMin', () => filterOrganizations());
        $watch('filterMembersMax', () => filterOrganizations());
    "
         class="flex min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/40 to-indigo-50/30">

        <x-admin-sidebar />

        <!-- Main Content -->
        <main class="flex-1 min-h-screen">

            <!-- Mobile Header -->
            <header class="lg:hidden bg-white shadow-sm border-b px-4 py-3 flex items-center justify-between">
                <button @click="collapsed = false"
                        class="p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                    <i class="ri-menu-fold-line text-lg rotate-180"></i>
                </button>
                <h1 class="text-lg font-semibold text-slate-800">Organizations</h1>
                <div class="w-10"></div>
            </header>

            <!-- Top Navigation Bar -->
            <div class="bg-white/80 backdrop-blur-xl border-b border-gray-200/50 sticky top-0 z-40">
                <div class="px-4 lg:px-8 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-indigo-600 to-blue-600 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                                    <i class="ri-building-line text-white text-lg"></i>
                                </div>
                                <div class="min-w-0">
                                    <h2 class="text-xl font-bold text-gray-900 truncate">Organizations</h2>
                                    <p class="text-sm text-gray-600 hidden sm:block">Manage institutional entities</p>
                                </div>
                            </div>
                            <div class="h-8 w-px bg-gray-200 hidden md:block"></div>
                            <nav class="hidden md:flex items-center space-x-2 text-sm">
                                <span class="text-gray-500">Admin</span>
                                <i class="ri-arrow-right-s-line text-gray-400"></i>
                                <span class="text-gray-900 font-semibold">Organizations</span>
                            </nav>
                        </div>

                        <div class="flex items-center space-x-3">
                            <div class="flex items-center space-x-6 text-sm">
                                <div class="flex items-center space-x-2">
                                    <span class="font-semibold text-gray-900" x-text="filteredOrganizations.length"></span>
                                    <span class="text-gray-600 uppercase text-[10px] font-bold tracking-tight">Total</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="font-semibold text-green-600" x-text="filteredOrganizations.filter(o => o.status === 'active').length"></span>
                                    <span class="text-gray-600 uppercase text-[10px] font-bold tracking-tight">Active</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="px-4 sm:px-6 py-6">
                <div class="max-w-7xl mx-auto">
                    <!-- Search and Filter Section -->
                    <div class="bg-white rounded-xl border border-gray-200/60 shadow-sm overflow-hidden mb-6">
                        <!-- Header with Action Button -->
                        <div class="px-6 py-4 border-b border-gray-200/60 bg-gradient-to-r from-blue-50/50 via-indigo-50/50 to-slate-50/50">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-white/80 rounded-lg flex items-center justify-center shadow-sm border border-gray-200/50">
                                        <i class="ri-search-line text-gray-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-bold text-gray-900">Search & Filter</h3>
                                        <p class="text-[11px] text-gray-500 font-medium">Find organizations efficiently</p>
                                    </div>
                                </div>
                                <a href="{{ route('admin.organizations.create') }}"
                                   class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition-all shadow-md active:scale-95">
                                    <i class="ri-add-line mr-1.5 text-sm"></i>
                                    New Organization
                                </a>
                            </div>
                        </div>

                        <!-- Filter Controls -->
                        <div class="p-4 sm:p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                                <!-- Search Box -->
                                <div class="md:col-span-2 lg:col-span-1">
                                    <label class="block text-xs font-bold text-gray-800 mb-2">
                                        <i class="ri-search-line text-indigo-600 mr-1.5"></i>
                                        Search
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="ri-search-line text-gray-400 text-sm"></i>
                                        </div>
                                        <input type="text"
                                               x-model="searchQuery"
                                               placeholder="Name, email, slug..."
                                               class="w-full pl-10 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all bg-white">
                                    </div>
                                </div>

                                <!-- Sort By Filter -->
                                <div class="lg:col-span-1">
                                    <label class="block text-xs font-bold text-gray-800 mb-2">
                                        <i class="ri-sort-asc text-indigo-600 mr-1.5"></i>
                                        Sort By
                                    </label>
                                    <div class="relative">
                                        <select x-model="sortBy"
                                                class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all bg-white appearance-none">
                                            <option value="name">Name (A-Z)</option>
                                            <option value="created_at_desc">Newest First</option>
                                            <option value="created_at_asc">Oldest First</option>
                                            <option value="members_count">Most Members</option>
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
                                        <i class="ri-checkbox-circle-line text-indigo-600 mr-1.5"></i>
                                        Status
                                    </label>
                                    <div class="relative">
                                        <select x-model="filterStatus"
                                                class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all bg-white appearance-none">
                                            <option value="">All Statuses</option>
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <i class="ri-arrow-down-s-line text-gray-400"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                                <div class="mt-6 pt-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div class="flex items-center space-x-2">
                                    <i class="ri-file-list-3-line text-indigo-600"></i>
                                    <span class="text-xs font-semibold text-gray-600">
                                        Showing <span x-text="filteredOrganizations.length" class="text-gray-900 font-bold"></span> organizations
                                    </span>
                                </div>
                                <button @click="clearAllFilters()"
                                        class="w-full sm:w-auto inline-flex items-center justify-center px-3 py-1.5 text-xs font-bold text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-all">
                                    <i class="ri-refresh-line mr-1.5"></i>
                                    Reset
                                </button>
                                </div>
                        </div>
                    </div>

                    <!-- Organizations Table (Desktop) / Cards (Mobile) -->
                    <div class="bg-white rounded-xl border border-gray-200/60 shadow-sm overflow-hidden">
                        <!-- Desktop View -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-slate-50/50">
                                <tr class="text-left">
                                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Organization Identity</th>
                                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Status</th>
                                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Members</th>
                                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Registration Date</th>
                                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Actions</th>
                                </tr>
                                </thead>

                                <tbody class="divide-y divide-slate-100">
                                <template x-for="org in filteredOrganizations" :key="org.id">
                                    <tr class="hover:bg-indigo-50/30 transition-all group">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center text-slate-500 font-bold text-xs group-hover:scale-110 transition-transform overflow-hidden">
                                                    <template x-if="org.logo_url">
                                                        <img :src="org.logo_url" :alt="org.name" class="w-full h-full object-cover">
                                                    </template>
                                                    <template x-if="!org.logo_url">
                                                        <i class="ri-building-4-line text-lg"></i>
                                                    </template>
                                                </div>
                                                <div>
                                                    <p class="font-bold text-slate-900 leading-tight text-sm" x-text="org.name"></p>
                                                    <p class="text-[10px] font-bold text-indigo-500 mt-0.5 uppercase tracking-wider" x-text="org.slug || 'no-slug'"></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span :class="org.status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600'"
                                                  class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-tight">
                                                <span :class="org.status === 'active' ? 'bg-emerald-500' : 'bg-slate-400'" class="w-1.5 h-1.5 rounded-full"></span>
                                                <span x-text="org.status || 'inactive'"></span>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="inline-flex items-center justify-center px-3 py-1.5 bg-slate-100 rounded-lg font-bold text-slate-700 text-xs" x-text="org.members_count || 0"></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-xs font-bold text-slate-500" x-text="org.created_at ? new Date(org.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '—'"></p>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end gap-2 transition-all">
                                                <a :href="`/admin/organizations/${org.id}`"
                                                   class="w-8 h-8 bg-white border border-slate-200 rounded-lg flex items-center justify-center text-slate-500 hover:text-indigo-600 hover:border-indigo-200 hover:shadow transition-all">
                                                    <i class="ri-eye-line text-sm"></i>
                                                </a>
                                                <a :href="`/admin/organizations/${org.id}/edit`"
                                                   class="w-8 h-8 bg-white border border-slate-200 rounded-lg flex items-center justify-center text-slate-500 hover:text-blue-600 hover:border-blue-200 hover:shadow transition-all">
                                                    <i class="ri-edit-line text-sm"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile View (Cards) -->
                        <div class="md:hidden divide-y divide-gray-100">
                            <template x-for="org in filteredOrganizations" :key="org.id">
                                <div class="p-4 space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 shadow-sm border border-indigo-100 overflow-hidden">
                                                <template x-if="org.logo_url">
                                                    <img :src="org.logo_url" :alt="org.name" class="w-full h-full object-cover">
                                                </template>
                                                <template x-if="!org.logo_url">
                                                    <i class="ri-building-4-line text-xl"></i>
                                                </template>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-bold text-slate-900 text-base truncate" x-text="org.name"></p>
                                                <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest" x-text="org.slug || 'no-slug'"></p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <a :href="`/admin/organizations/${org.id}`"
                                               class="w-9 h-9 bg-white border border-slate-200 rounded-lg flex items-center justify-center text-slate-500 shadow-sm">
                                                <i class="ri-eye-line text-sm"></i>
                                            </a>
                                            <a :href="`/admin/organizations/${org.id}/edit`"
                                               class="w-9 h-9 bg-white border border-slate-200 rounded-lg flex items-center justify-center text-slate-500 shadow-sm">
                                                <i class="ri-edit-line text-sm"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Status</p>
                                            <span :class="org.status === 'active' ? 'text-emerald-600' : 'text-slate-500'"
                                                  class="text-xs font-bold uppercase" x-text="org.status || 'inactive'"></span>
                                        </div>
                                        <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100 text-center">
                                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Members</p>
                                            <p class="text-xs font-bold text-slate-700" x-text="org.members_count || 0"></p>
                                        </div>
                                    </div>
                                    <div class="bg-indigo-50/50 p-2.5 rounded-lg border border-indigo-100">
                                        <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mb-1">Registered On</p>
                                        <p class="text-xs font-bold text-indigo-700" x-text="org.created_at ? new Date(org.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '—'"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </main>

    <script>
        // Minimal members fetch helper — fills console and can be extended to update UI/modal
        function fetchMembers(organizationId) {
            if (!organizationId) return;
            fetch(`/admin/organizations/${organizationId}/members`, {
                headers: { 'Accept': 'application/json' }
            })
                .then(res => {
                    if (!res.ok) throw new Error('Failed to fetch members');
                    return res.json();
                })
                .then(data => {
                    // data expected as array of members
                    console.log('Members for org', organizationId, data);
                    // If desired, find Alpine component and set members/showMembers:
                    try {
                        const root = document.querySelector('[x-data]');
                        if (root) {
                            const comp = root.__x ? root.__x : null;
                            // Avoid depending on private API — keep as console output for now.
                        }
                    } catch (e) { /* ignore */ }
                })
                .catch(err => console.error(err));
        }
    </script>
@endsection
