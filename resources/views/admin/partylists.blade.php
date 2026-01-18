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
        organizations: @js(isset($organizations) ? $organizations->toArray() : []),

        // Import CSV
        showImportModal: false,
        showDeleteModal: false,
        partylistToDelete: null,

        // Export
        showExportModal: false,
        selectedPartylistsForExport: [],

        togglePartylistSelection(id) {
            if (this.selectedPartylistsForExport.includes(id)) {
                this.selectedPartylistsForExport = this.selectedPartylistsForExport.filter(pId => pId !== id);
            } else {
                this.selectedPartylistsForExport.push(id);
            }
        },

        selectAllForExport() {
            if (this.selectedPartylistsForExport.length === this.allPartylists.length) {
                this.selectedPartylistsForExport = [];
            } else {
                this.selectedPartylistsForExport = this.allPartylists.map(p => p.id);
            }
        },

        exportSelected() {
            if (this.selectedPartylistsForExport.length === 0) {
                alert('Please select at least one party list to export.');
                return;
            }
            const ids = this.selectedPartylistsForExport.join(',');
            window.location.href = `{{ route('admin.partylists.export') }}?ids=${ids}`;
            this.showExportModal = false;
        },

        confirmDelete(id) {
            this.partylistToDelete = id;
            this.showDeleteModal = true;
        },

        async deletePartylist() {
            if (!this.partylistToDelete) return;
            try {
                const response = await fetch(`/admin/partylists/${this.partylistToDelete}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ _method: 'DELETE' })
                });
                const data = await response.json();
                if (data.success) {
                    this.showDeleteModal = false;
                    this.allPartylists = this.allPartylists.filter(p => p.id !== this.partylistToDelete);
                    this.filterPartylists();
                    // Optionally show a notification
                } else {
                    alert(data.message || 'Failed to delete partylist');
                }
            } catch (error) {
                alert('An error occurred while deleting');
            }
        },
        importing: false,
        importFile: null,
        importPreviewData: [],
        availableOrganizations: [],
        importPath: '',
        selectedImportElection: '',
        globalOrganizationId: '',

        applyToAll() {
            if (this.importPreviewData.length === 0) return;
            this.importPreviewData.forEach(row => {
                if (this.globalOrganizationId) {
                    row.organization_id = this.globalOrganizationId;
                }
            });
        },

        handleFileSelect(e) {
            this.importFile = e.target.files[0];
            if (this.importFile) {
                this.previewImport();
            }
        },

        previewImport() {
            const formData = new FormData();
            formData.append('file', this.importFile);
            formData.append('_token', '{{ csrf_token() }}');

            this.importing = true;
            fetch('{{ route('admin.partylists.import.preview') }}', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.importPreviewData = data.data;
                    this.availableOrganizations = data.organizations;
                    this.importPath = data.importPath;
                } else {
                    alert(data.message || 'Error processing file');
                }
            })
            .catch(err => alert('Upload failed'))
            .finally(() => this.importing = false);
        },

        processImport() {
            if (!this.importPath) return;

            // Prepare overrides
            const overrides = {};
            this.importPreviewData.forEach(row => {
                overrides[row.index] = {
                    organization_id: row.organization_id
                };
            });

            this.importing = true;
            fetch('{{ route('admin.partylists.import.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    import_path: this.importPath,
                    election_id: this.selectedImportElection,
                    overrides: overrides
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert(data.message || 'Import failed');
                }
            })
            .catch(err => alert('Import failed'))
            .finally(() => this.importing = false);
        },

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
                <div class="px-4 lg:px-8 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                                    <i class="ri-stack-line text-white text-lg"></i>
                                </div>
                                <div class="min-w-0">
                                    <h2 class="text-xl font-bold text-gray-900 truncate">Party Lists</h2>
                                    <p class="text-sm text-gray-600 hidden sm:block">Manage your organization's party lists</p>
                                </div>
                            </div>
                            <div class="h-8 w-px bg-gray-200 hidden md:block"></div>
                            <nav class="hidden md:flex items-center space-x-2 text-sm">
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
                                    <span class="text-gray-600 uppercase text-[10px] tracking-tight">Active</span>
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
                        <div class="px-6 py-4 border-b border-gray-200/60 bg-gradient-to-r from-purple-50/50 via-indigo-50/50 to-blue-50/50">
                                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-white/80 rounded-lg flex items-center justify-center shadow-sm border border-gray-200/50">
                                                <i class="ri-search-line text-gray-600"></i>
                                            </div>
                                            <div>
                                                <h3 class="text-base font-bold text-gray-900">Search & Filter</h3>
                                                <p class="text-[11px] text-gray-500 font-medium">Find and manage party lists</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2 w-full sm:w-auto">
                                            <button @click="showImportModal = true"
                                                    class="inline-flex items-center justify-center px-4 py-2.5 bg-green-600 text-white text-xs font-bold rounded-lg hover:bg-green-700 transition-all shadow-md active:scale-95">
                                                <i class="ri-upload-2-line mr-1.5"></i>
                                                Import CSV
                                            </button>
                                            <button @click="showExportModal = true"
                                                    class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 text-white text-xs font-bold rounded-lg hover:bg-blue-700 transition-all shadow-md active:scale-95">
                                                <i class="ri-download-2-line mr-1.5"></i>
                                                Export CSV
                                            </button>
                                            <a href="{{ route('admin.partylists.create') }}"
                                               class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition-all shadow-md active:scale-95">
                                                <i class="ri-add-line mr-1.5 text-sm"></i>
                                                New Party List
                                            </a>
                                        </div>
                                    </div>
                        </div>

                        <!-- Filter Controls -->
                        <div class="p-4 sm:p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                                <!-- Search Box -->
                                <div class="md:col-span-2 lg:col-span-1">
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
                                <div>
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
                                <div>
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
                                <div>
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

                                <div class="mt-6 pt-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div class="flex items-center space-x-2">
                                    <i class="ri-file-list-3-line text-purple-600 text-sm"></i>
                                    <span class="text-xs font-semibold text-gray-600">
                                        Showing <span x-text="filteredPartylists.length" class="text-gray-900 font-bold"></span> of <span x-text="allPartylists.length"></span> results
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
                                                <button @click="confirmDelete(partylist.id)"
                                                        class="w-8 h-8 bg-white border border-slate-200 rounded-lg flex items-center justify-center text-slate-500 hover:text-red-600 hover:border-red-200 transition-all">
                                                    <i class="ri-delete-bin-line text-sm"></i>
                                                </button>
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

                                <div class="px-6 py-4 border-t border-gray-100 bg-white flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-600">
                                    <div>Showing <span class="font-semibold" x-text="filteredPartylists.length"></span> of <span x-text="allPartylists.length"></span> results</div>
                                    <div class="flex items-center space-x-3 w-full sm:w-auto">
                                        <button @click="filteredPartylists = filteredPartylists.slice().reverse()"
                                                class="w-full sm:w-auto px-4 py-2 bg-white border border-gray-200 rounded-lg text-xs font-medium hover:bg-gray-50 transition-colors">
                                            <i class="ri-sort-desc mr-1"></i>
                                            Toggle Order
                                        </button>
                                    </div>
                                </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Import Modal -->
        <div x-show="showImportModal"
             class="fixed inset-0 z-[100] overflow-y-auto"
             x-cloak>
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showImportModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                     @click="showImportModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showImportModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-xl leading-6 font-bold text-gray-900 mb-4 flex items-center">
                                    <i class="ri-upload-2-line mr-2 text-green-600"></i>
                                    Import Party Lists via CSV
                                </h3>

                                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <i class="ri-information-line text-blue-400"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-xs text-blue-700 font-medium">
                                                Required CSV Header Format: <br>
                                                <code class="bg-white/50 px-1 rounded">Party Name, Acronym, Description, Platform & Agenda, Logo (Image), Organization</code>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <!-- Election Selection -->
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 mb-1">Assign to Election (Optional)</label>
                                        <select x-model="selectedImportElection"
                                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white">
                                            <option value="">None</option>
                                            <template x-for="election in elections" :key="election.id">
                                                <option :value="election.id" x-text="election.name || election.title"></option>
                                            </template>
                                        </select>
                                    </div>

                                    <!-- Global Fallbacks -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end bg-slate-50 p-4 rounded-xl border border-slate-200">
                                        <div>
                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Global Organization</label>
                                            <select x-model="globalOrganizationId"
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white">
                                                <option value="">Select Organization</option>
                                                <template x-for="org in availableOrganizations" :key="org.id">
                                                    <option :value="org.id" x-text="org.name"></option>
                                                </template>
                                            </select>
                                        </div>
                                        <div>
                                            <button @click="applyToAll" type="button"
                                                    class="w-full px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition-colors flex items-center justify-center h-[38px]">
                                                <i class="ri-check-double-line mr-2"></i>
                                                APPLY TO ALL
                                            </button>
                                        </div>
                                    </div>

                                    <!-- File Upload -->
                                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-indigo-400 transition-colors bg-slate-50/50">
                                        <input type="file" @change="handleFileSelect" class="hidden" id="csvFileUpload" accept=".csv,.xml,.xlsx,.xls,.tsv">
                                        <label for="csvFileUpload" class="cursor-pointer">
                                            <div class="w-12 h-12 bg-white rounded-full shadow-sm border border-gray-200 flex items-center justify-center mx-auto mb-3">
                                                <i class="ri-file-upload-line text-gray-400 text-xl"></i>
                                            </div>
                                            <span class="text-sm font-bold text-gray-900" x-text="importFile ? importFile.name : 'Choose file'"></span>
                                            <p class="text-xs text-gray-500 mt-1">Maximum file size: 50MB</p>
                                        </label>
                                    </div>

                                    <!-- Preview Table -->
                                    <div x-show="importPreviewData.length > 0" class="mt-6">
                                        <h4 class="text-sm font-bold text-gray-900 mb-3 flex items-center justify-between">
                                            Data Preview
                                            <span class="text-[10px] bg-slate-100 px-2 py-0.5 rounded-full" x-text="importPreviewData.length + ' rows found'"></span>
                                        </h4>

                                        <template x-if="importPreviewData.some(row => !row.organization_id)">
                                            <div class="bg-amber-50 border-l-4 border-amber-400 p-3 mb-4">
                                                <div class="flex">
                                                    <div class="flex-shrink-0">
                                                        <i class="ri-alert-line text-amber-400"></i>
                                                    </div>
                                                    <div class="ml-3">
                                                        <p class="text-[10px] text-amber-700 font-medium">
                                                            Some party lists are missing organization assignment. Please select an organization for them below.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        <div class="max-h-[300px] overflow-y-auto border border-gray-200 rounded-xl">
                                            <table class="min-w-full divide-y divide-gray-200 text-xs">
                                                <thead class="bg-gray-50 sticky top-0">
                                                    <tr>
                                                        <th class="px-4 py-2 text-left font-bold text-gray-500">Name</th>
                                                        <th class="px-4 py-2 text-left font-bold text-gray-500">Acronym</th>
                                                        <th class="px-4 py-2 text-left font-bold text-gray-500">Organization</th>
                                                        <th class="px-4 py-2 text-left font-bold text-gray-500">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-100">
                                                    <template x-for="(row, index) in importPreviewData" :key="index">
                                                        <tr>
                                                            <td class="px-4 py-2">
                                                                <div x-text="row.name"></div>
                                                                <template x-for="alert in (row.alerts || [])">
                                                                    <div class="text-[9px] text-red-500 font-bold mt-1" x-text="alert"></div>
                                                                </template>
                                                            </td>
                                                            <td class="px-4 py-2" x-text="row.acronym"></td>
                                                            <td class="px-4 py-2">
                                                                <select x-model="row.organization_id"
                                                                        class="w-full text-[10px] border border-gray-300 rounded-md py-1 px-2 focus:ring-indigo-500 focus:border-indigo-500"
                                                                        :class="!row.organization_id ? 'border-amber-500 bg-amber-50' : ''">
                                                                    <option value="">Select Organization</option>
                                                                    <template x-for="org in availableOrganizations" :key="org.id">
                                                                        <option :value="org.id" x-text="org.name" :selected="row.organization_id == org.id"></option>
                                                                    </template>
                                                                </select>
                                                            </td>
                                                            <td class="px-4 py-2">
                                                                <span :class="row.is_duplicate ? 'text-red-600 font-bold' : 'text-green-600 font-bold'"
                                                                      x-text="row.status"></span>
                                                            </td>
                                                        </tr>
                                                    </template>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                        <button type="button"
                                @click="processImport"
                                :disabled="importing || !importPath"
                                class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-sm font-bold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-auto active:scale-95 transition-all disabled:opacity-50">
                            <i x-show="importing" class="ri-loader-4-line animate-spin mr-2"></i>
                            Confirm Import
                        </button>
                        <button type="button"
                                @click="showImportModal = false"
                                class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-bold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto active:scale-95 transition-all">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Delete Confirmation Modal -->
        <div x-show="showDeleteModal"
             class="fixed inset-0 z-[110] overflow-y-auto"
             x-cloak>
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showDeleteModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                     @click="showDeleteModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showDeleteModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-middle bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="ri-error-warning-line text-red-600 text-xl"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-bold text-gray-900">Delete Party List</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Are you sure you want to delete this party list? This action cannot be undone and will remove all associated data.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button"
                                @click="deletePartylist()"
                                class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Delete
                        </button>
                        <button type="button"
                                @click="showDeleteModal = false"
                                class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div x-show="showExportModal"
             class="fixed inset-0 z-[110] overflow-y-auto"
             x-cloak>
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showExportModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                     @click="showExportModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showExportModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-middle bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="ri-download-2-line text-blue-600 text-xl"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-bold text-gray-900">Export Party Lists</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 mb-4">
                                        Select the party lists you want to export.
                                    </p>

                                    <div class="max-h-60 overflow-y-auto space-y-2 pr-2">
                                        <div class="flex items-center justify-between p-2 bg-slate-50 rounded-lg mb-2">
                                            <span class="text-xs font-bold text-slate-700">Select All</span>
                                            <input type="checkbox"
                                                   @click="selectAllForExport()"
                                                   :checked="selectedPartylistsForExport.length === allPartylists.length && allPartylists.length > 0"
                                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        </div>
                                        <template x-for="party in allPartylists" :key="party.id">
                                            <div class="flex items-center justify-between p-2 hover:bg-slate-50 rounded-lg border border-transparent hover:border-slate-200 transition-all">
                                                <div class="flex items-center gap-2">
                                                    <div class="w-6 h-6 rounded bg-slate-100 flex items-center justify-center">
                                                        <i class="ri-flag-2-line text-xs text-slate-400"></i>
                                                    </div>
                                                    <span class="text-xs font-medium text-slate-700" x-text="party.name"></span>
                                                </div>
                                                <input type="checkbox"
                                                       :value="party.id"
                                                       :checked="selectedPartylistsForExport.includes(party.id)"
                                                       @change="togglePartylistSelection(party.id)"
                                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button"
                                @click="exportSelected()"
                                :disabled="selectedPartylistsForExport.length === 0"
                                class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                            Export Selected
                        </button>
                        <button type="button"
                                @click="showExportModal = false"
                                class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
