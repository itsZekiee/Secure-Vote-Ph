@extends('layouts.app-main-admin')

@section('content')
    <div x-data="{
        collapsed: false,
        isMobile: window.innerWidth < 1024,
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
        window.addEventListener('resize', () => { isMobile = window.innerWidth < 1024 });
        $watch('searchQuery', () => filterOrganizations());
        $watch('sortBy', () => filterOrganizations());
        $watch('filterStatus', () => filterOrganizations());
        $watch('filterDateFrom', () => filterOrganizations());
        $watch('filterDateTo', () => filterOrganizations());
        $watch('filterMembersMin', () => filterOrganizations());
        $watch('filterMembersMax', () => filterOrganizations());
    "
         class="flex min-h-screen bg-gray-50">

        <x-admin-sidebar />

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <!-- Header Section -->
            <div class="mb-10">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center space-x-4 mb-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="ri-building-line text-white text-2xl"></i>
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900">Organizations</h1>
                                <p class="text-gray-600 mt-1">Manage and organize institutional groups</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filter Section -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-8">
                <!-- Header with Action Button -->
                <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-slate-50 via-blue-50 to-indigo-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="ri-filter-3-line text-white text-lg"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Search & Filter</h2>
                                <p class="text-sm text-gray-600 mt-0.5">Find and organize your data efficiently</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.organizations.create') }}"
                           class="inline-flex items-center px-5 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-semibold rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <i class="ri-add-line text-lg mr-2"></i>
                            Create Organization
                        </a>
                    </div>
                </div>

                <!-- Filter Controls -->
                <div class="p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Search Box -->
                        <div class="lg:col-span-1">
                            <label class="block text-sm font-semibold text-gray-800 mb-3">
                                <i class="ri-search-line text-blue-600 mr-2"></i>
                                Search Organizations
                            </label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="ri-search-line text-gray-400 text-lg group-focus-within:text-blue-600 transition-colors duration-200"></i>
                                </div>
                                <input type="text"
                                       x-model="searchQuery"
                                       placeholder="Search by name, email, or description..."
                                       class="w-full pl-12 pr-12 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-sm bg-gray-50 focus:bg-white shadow-sm hover:shadow-md">
                                <div x-show="searchQuery"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     class="absolute inset-y-0 right-0 pr-4 flex items-center">
                                    <button @click="searchQuery = ''"
                                            class="text-gray-400 hover:text-red-500 transition-colors duration-200 p-1 rounded-lg hover:bg-red-50">
                                        <i class="ri-close-circle-line text-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Sort By Filter -->
                        <div class="lg:col-span-1">
                            <label class="block text-sm font-semibold text-gray-800 mb-3">
                                <i class="ri-sort-asc text-blue-600 mr-2"></i>
                                Sort By
                            </label>
                            <div class="relative">
                                <select x-model="sortBy"
                                        class="w-full pl-4 pr-12 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-sm appearance-none bg-gray-50 hover:bg-white shadow-sm hover:shadow-md cursor-pointer">
                                    <option value="name">Organization Name (A-Z)</option>
                                    <option value="created_at_desc">Newest First</option>
                                    <option value="created_at_asc">Oldest First</option>
                                    <option value="members_count">Member Count (High-Low)</option>
                                    <option value="status">Status (Active First)</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                    <i class="ri-arrow-down-s-line text-gray-500 text-lg"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Status Filter -->
                        <div class="lg:col-span-1">
                            <label class="block text-sm font-semibold text-gray-800 mb-3">
                                <i class="ri-filter-line text-blue-600 mr-2"></i>
                                Filter by Status
                            </label>
                            <div class="relative">
                                <select x-model="filterStatus"
                                        class="w-full pl-4 pr-12 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-sm appearance-none bg-gray-50 hover:bg-white shadow-sm hover:shadow-md cursor-pointer">
                                    <option value="">All Statuses</option>
                                    <option value="active">Active Organizations</option>
                                    <option value="inactive">Inactive Organizations</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                    <i class="ri-arrow-down-s-line text-gray-500 text-lg"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Results Summary -->
                    <div class="mt-8 pt-6 border-t border-gray-100">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                            <div class="flex flex-col sm:flex-row sm:items-center space-y-3 sm:space-y-0 sm:space-x-6">
                                <div class="flex items-center text-sm font-medium text-gray-700">
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="ri-bar-chart-line text-blue-600"></i>
                                    </div>
                                    <span>Showing <span class="font-bold text-blue-600" x-text="filteredOrganizations.length"></span> of <span class="font-bold text-gray-900" x-text="allOrganizations.length"></span> organizations</span>
                                </div>
                            </div>

                            <!-- Export Actions -->
                            <div class="flex items-center space-x-3">
                                <button class="inline-flex items-center px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
                                    <i class="ri-download-line mr-2 text-gray-500"></i>
                                    Export
                                </button>

                                <button class="inline-flex items-center px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
                                    <i class="ri-printer-line mr-2 text-gray-500"></i>
                                    Print
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State (show when no organizations exist) -->
            <div x-show="allOrganizations.length === 0" class="text-center py-20">
                <div class="max-w-md mx-auto">
                    <div class="w-24 h-24 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <i class="ri-building-line text-blue-600 text-4xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">No Organizations Yet</h3>
                    <p class="text-gray-600 mb-8">Get started by creating your first organization to manage members and activities.</p>
                    <a href="{{ route('admin.organizations.create') }}"
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <i class="ri-add-line text-lg mr-2"></i>
                        Create First Organization
                    </a>
                </div>
            </div>

            <!-- Organizations Table -->
            <div class="mt-6">
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Organization</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Members</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Created</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-100">
                        <template x-for="org in filteredOrganizations" :key="org.id">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-gray-100 rounded flex items-center justify-center text-gray-500">
                                            <i class="ri-building-line"></i>
                                        </div>
                                        <div class="text-sm">
                                            <div class="font-medium text-gray-900" x-text="org.name"></div>
                                            <div class="text-xs text-gray-500" x-text="org.description || '-'"></div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                            :class="org.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700'">
                                            <span x-text="org.status ? org.status.charAt(0).toUpperCase()+org.status.slice(1) : 'Unknown'"></span>
                                        </span>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm font-semibold text-gray-900" x-text="org.members_count ?? 0"></div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                    <div class="text-sm text-gray-600" x-text="org.created_at ? new Date(org.created_at).toLocaleString() : '-'"></div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <!-- View -->
                                        <a :href="`/admin/organizations/${org.id}`"
                                           class="inline-flex items-center p-2 rounded-lg hover:bg-gray-100 text-gray-600"
                                           :aria-label="`View ${org.name}`" title="View">
                                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </a>

                                        <!-- Edit -->
                                        <a :href="`/admin/organizations/${org.id}/edit`"
                                           class="inline-flex items-center p-2 rounded-lg hover:bg-gray-100 text-indigo-600"
                                           :aria-label="`Edit ${org.name}`" title="Edit">
                                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M3 21v-3.75L14.06 6.19a2.12 2.12 0 013 0L20.81 9.94a2.12 2.12 0 010 3L9.75 23H3z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <!-- Empty row when no results -->
                        <tr x-show="filteredOrganizations.length === 0">
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                                No organizations match the current filters.
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

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
