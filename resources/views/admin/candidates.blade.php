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
        elections: @js(isset($elections) ? $elections->toArray() : []),

        // Import CSV
        showImportModal: false,
        importing: false,
        importFile: null,
        importPreviewData: [],
        importPath: '',
        selectedImportElection: '',

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
            fetch('{{ route('admin.candidates.import.preview') }}', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.importPreviewData = data.data;
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

            this.importing = true;
            fetch('{{ route('admin.candidates.import.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    import_path: this.importPath,
                    election_id: this.selectedImportElection
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
                <!-- Mobile Header Toggle -->
                <div class="lg:hidden flex items-center justify-between px-4 py-3 border-b border-gray-100">
                    <button @click="collapsed = false" class="p-2 -ml-2 text-gray-600">
                        <i class="ri-menu-2-fill text-xl"></i>
                    </button>
                    <h1 class="text-lg font-bold text-gray-900">Candidates</h1>
                    <div class="w-8"></div>
                </div>

                <div class="px-4 lg:px-8 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                                    <i class="ri-user-star-line text-white text-lg"></i>
                                </div>
                                <div class="min-w-0">
                                    <h2 class="text-xl font-bold text-gray-900 truncate">Candidates</h2>
                                    <p class="text-sm text-gray-600 hidden sm:block">Nominees & profiles</p>
                                </div>
                            </div>
                            <div class="h-8 w-px bg-gray-200 hidden md:block"></div>
                            <nav class="hidden md:flex items-center space-x-2 text-sm">
                                <span class="text-gray-500">Admin</span>
                                <i class="ri-arrow-right-s-line text-gray-400"></i>
                                <span class="text-gray-900 font-semibold">Candidates</span>
                            </nav>
                        </div>

                        <div class="hidden sm:flex items-center space-x-3">
                            <div class="flex items-center space-x-6 text-sm">
                                <div class="flex items-center space-x-2">
                                    <span class="font-semibold text-gray-900" x-text="filteredCandidates.length"></span>
                                    <span class="text-gray-600 uppercase text-[10px] font-bold tracking-tight">Total</span>
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
                        <div class="px-6 py-4 border-b border-gray-200/60 bg-gradient-to-r from-purple-50/50 via-indigo-50/50 to-slate-50/50">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-white/80 rounded-lg flex items-center justify-center shadow-sm border border-gray-200/50">
                                        <i class="ri-filter-2-line text-gray-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-bold text-gray-900">Search & Filters</h3>
                                        <p class="text-[11px] text-gray-500 font-medium">Manage candidate views efficiently</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2 w-full sm:w-auto">
                                    <button @click="showImportModal = true"
                                            class="inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white text-xs font-bold rounded-lg hover:bg-green-700 transition-all shadow-sm active:scale-95">
                                        <i class="ri-upload-2-line mr-1.5"></i>
                                        Import CSV
                                    </button>
                                    <a href="{{ route('admin.candidates.export') }}"
                                       class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-xs font-bold rounded-lg hover:bg-blue-700 transition-all shadow-sm active:scale-95">
                                        <i class="ri-download-2-line mr-1.5"></i>
                                        Export CSV
                                    </a>
                                    <a href="{{ route('admin.candidates.create') }}"
                                       class="inline-flex items-center justify-center px-4 py-2 bg-purple-600 text-white text-xs font-bold rounded-lg hover:bg-purple-700 transition-all shadow-sm active:scale-95">
                                        <i class="ri-user-add-line mr-1.5"></i>
                                        New Candidate
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Filter Controls -->
                        <div class="p-4 sm:p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
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

                    <!-- Candidates Table (Desktop) / Cards (Mobile) -->
                    <div class="bg-white rounded-xl border border-gray-200/60 shadow-sm overflow-hidden">
                        <!-- Desktop View -->
                        <div class="hidden md:block overflow-x-auto">
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

                        <!-- Mobile View (Cards) -->
                        <div class="md:hidden divide-y divide-gray-100">
                            <template x-for="candidate in filteredCandidates" :key="candidate.id">
                                <div class="p-4 space-y-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center text-purple-600 shadow-sm border border-purple-100">
                                            <i class="ri-user-3-fill text-xl"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="font-bold text-slate-900 text-base truncate" x-text="candidate.user?.name || candidate.name || '—'"></p>
                                            <p class="text-xs text-slate-500 truncate" x-text="candidate.user?.email || candidate.email || ''"></p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <a :href="`/admin/candidates/${candidate.id}/edit`"
                                               class="w-9 h-9 bg-white border border-slate-200 rounded-lg flex items-center justify-center text-slate-500">
                                                <i class="ri-edit-line text-sm"></i>
                                            </a>
                                            <button type="button" @click="if (confirm('Delete this candidate profile?')) { deleteCandidate(candidate.id) }"
                                                    class="w-9 h-9 bg-red-50 text-red-600 rounded-lg flex items-center justify-center">
                                                <i class="ri-delete-bin-line text-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Position</p>
                                            <p class="text-xs font-bold text-slate-700 truncate" x-text="candidate.position?.title || candidate.position?.name || '—'"></p>
                                        </div>
                                        <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Affiliation</p>
                                            <p class="text-xs font-bold text-slate-700 truncate" x-text="candidate.partylist?.name || 'Independent'"></p>
                                        </div>
                                    </div>
                                    <div class="bg-purple-50/50 p-2.5 rounded-lg border border-purple-100">
                                        <p class="text-[9px] font-black text-purple-400 uppercase tracking-widest mb-1">Election</p>
                                        <p class="text-xs font-bold text-purple-700 truncate" x-text="candidate.election?.name || candidate.election?.title || '—'"></p>
                                    </div>
                                </div>
                            </template>
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
                                    Import Candidates via CSV
                                </h3>

                                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <i class="ri-information-line text-blue-400"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-xs text-blue-700 font-medium">
                                                Required CSV Header Format: <br>
                                                <code class="bg-white/50 px-1 rounded">Full Name, Email, Organization, Political Affiliation, Designated Position, Platform Statement, Profile Photo</code>
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

                                    <!-- File Upload -->
                                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-indigo-400 transition-colors bg-slate-50/50">
                                        <input type="file" @change="handleFileSelect" class="hidden" id="csvFileUpload" accept=".csv">
                                        <label for="csvFileUpload" class="cursor-pointer">
                                            <div class="w-12 h-12 bg-white rounded-full shadow-sm border border-gray-200 flex items-center justify-center mx-auto mb-3">
                                                <i class="ri-file-upload-line text-gray-400 text-xl"></i>
                                            </div>
                                            <span class="text-sm font-bold text-gray-900" x-text="importFile ? importFile.name : 'Choose CSV file'"></span>
                                            <p class="text-xs text-gray-500 mt-1">Maximum file size: 50MB</p>
                                        </label>
                                    </div>

                                    <!-- Preview Table -->
                                    <div x-show="importPreviewData.length > 0" class="mt-6">
                                        <h4 class="text-sm font-bold text-gray-900 mb-3 flex items-center justify-between">
                                            Data Preview
                                            <span class="text-[10px] bg-slate-100 px-2 py-0.5 rounded-full" x-text="importPreviewData.length + ' rows found'"></span>
                                        </h4>
                                        <div class="max-h-[300px] overflow-y-auto border border-gray-200 rounded-xl">
                                            <table class="min-w-full divide-y divide-gray-200 text-xs">
                                                <thead class="bg-gray-50 sticky top-0">
                                                    <tr>
                                                        <th class="px-4 py-2 text-left font-bold text-gray-500">Name</th>
                                                        <th class="px-4 py-2 text-left font-bold text-gray-500">Email</th>
                                                        <th class="px-4 py-2 text-left font-bold text-gray-500">Position</th>
                                                        <th class="px-4 py-2 text-left font-bold text-gray-500">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-100">
                                                    <template x-for="(row, index) in importPreviewData" :key="index">
                                                        <tr>
                                                            <td class="px-4 py-2" x-text="row.full_name"></td>
                                                            <td class="px-4 py-2" x-text="row.email"></td>
                                                            <td class="px-4 py-2" x-text="row.designated_position"></td>
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
