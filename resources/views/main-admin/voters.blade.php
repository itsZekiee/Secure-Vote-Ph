@extends('layouts.app-main-admin')

@section('content')

    @php
        use Illuminate\Pagination\LengthAwarePaginator;
        use Illuminate\Pagination\Paginator;
        use Illuminate\Support\Collection;

        if (!isset($voters) || $voters === null) {
            $voters = collect();
        }

        // Ensure forms variable exists (fallback to empty collection)
        if (!isset($forms) || $forms === null) {
            $forms = collect();
        }

        $isPaginated = $voters instanceof LengthAwarePaginator || $voters instanceof Paginator;
        $collection = $isPaginated ? $voters->getCollection() : ($voters instanceof Collection ? $voters : collect($voters));
        $forms = $forms instanceof Collection ? $forms : collect($forms);

        $total = $isPaginated ? $voters->total() : $collection->count();
        $firstItem = $isPaginated ? $voters->firstItem() : ($collection->count() ? 1 : 0);
        $lastItem = $isPaginated ? $voters->lastItem() : $collection->count();

        $verifiedCount = $collection->where('registration_status', 'approved')->count();
        $pendingCount  = $collection->where('registration_status', 'pending')->count();
        $declinedCount = $collection->where('registration_status', 'declined')->count();
    @endphp

    <div x-data="{
        search: '',
        filterBy: 'all',
        selectedForm: 'all',
        perPage: 15,
        showPasswordModal: {{ session('temp_password_display') ? 'true' : 'false' }},

        // Import
        showImportModal: false,
        importing: false,
        importFile: null,
        importPreviewData: [],
        importPath: '',
        selectedImportElection: '',
        registrationStatus: 'approved',
        tempPassword: 'password',

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
            fetch('{{ route('admin.voters.import.preview') }}', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.importPreviewData = data.data;
                    this.importPath = data.importPath;
                    this.showImportModal = true;
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
            fetch('{{ route('admin.voters.import.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    import_path: this.importPath,
                    election_id: this.selectedImportElection,
                    registration_status: this.registrationStatus
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

        confirmAction(id, action) {
            if (confirm(`Are you sure you want to ${action} this voter?`)) {
                document.getElementById(`${action}-form-${id}`).submit();
            }
        }
    }"
         class="flex-1 flex flex-col">

        <!-- Temporary Password Modal -->
        <template x-if="showPasswordModal">
            <div class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="showPasswordModal = false"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-white/20">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-xl bg-amber-50 sm:mx-0 sm:h-10 sm:w-10">
                                    <i class="ri-shield-keyhole-line text-amber-600 text-xl"></i>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                                        Import Successful!
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500 font-medium">
                                            The voters have been imported successfully. They will receive an email containing their login credentials and the election details.
                                        </p>
                                        <div class="mt-4 flex items-start gap-2 p-3 bg-indigo-50 rounded-lg border border-indigo-100">
                                            <i class="ri-information-line text-indigo-500 mt-0.5"></i>
                                            <p class="text-[11px] text-indigo-700 font-bold leading-tight uppercase tracking-wider">
                                                Unique keys have been automatically generated for each voter and sent to their registered emails.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="button"
                                    @click="showPasswordModal = false"
                                    class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-2.5 bg-indigo-600 text-xs font-bold text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto uppercase tracking-widest transition-all">
                                I Have Recorded the Password
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- Main content -->
        <main class="flex-1">
            <!-- Mobile Header (if needed) -->
            <header class="bg-white/80 backdrop-blur-sm border-b lg:hidden px-4 py-4 flex items-center justify-between sticky top-0 z-40">
                <button @click="collapsed = false" class="p-2 -ml-2 rounded-lg text-slate-600 hover:bg-slate-100">
                    <i class="ri-menu-2-fill text-xl"></i>
                </button>
                <h1 class="text-lg font-bold text-slate-800">Voters</h1>
                <div class="w-8"></div>
            </header>

            <!-- Top bar (Desktop & Tablet) -->
            <div class="bg-white/80 backdrop-blur-sm border-b sticky top-0 z-40 hidden md:block">
                <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-600 to-blue-500 flex items-center justify-center shadow flex-shrink-0">
                            <i class="ri-user-3-line text-white text-xl"></i>
                        </div>
                        <div class="min-w-0">
                            <h1 class="text-2xl font-semibold leading-tight truncate">Voter Directory</h1>
                            <p class="text-sm text-gray-500 hidden sm:block">Manage registered voters and import/export data</p>
                        </div>
                    </div>

                    <div class="hidden lg:flex items-center gap-4 text-sm text-gray-600">
                        <div>
                            <span class="font-medium text-gray-900">Admin</span>
                            <i class="ri-arrow-right-s-line mx-2"></i>
                            <span>Voters</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-6">
                <!-- Search & Filters -->
                <div class="bg-white rounded-xl border border-gray-200/60 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b bg-gradient-to-r from-white via-indigo-50/30 to-white">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm border border-slate-200">
                                    <i class="ri-search-line text-indigo-600"></i>
                                </div>
                                <div>
                                    <h2 class="text-base font-bold text-gray-900">Search & Filter</h2>
                                    <p class="text-[11px] text-gray-500 font-medium">Refine voter results</p>
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                                <form method="GET" action="{{ route('admin.voters.export') }}" class="flex-1 sm:flex-none">
                                    <input type="hidden" name="format" value="csv" />
                                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold hover:bg-slate-50 transition-colors">
                                        <i class="ri-download-2-line"></i>
                                        Export CSV
                                    </button>
                                </form>

                                <div class="flex-1 sm:flex-none inline-flex items-center justify-between gap-2 bg-white border border-slate-200 rounded-lg px-3 py-1.5 shadow-sm hover:border-indigo-300 transition-all group">
                                    <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-600 group-hover:text-indigo-600">
                                        <i class="ri-upload-cloud-line text-lg text-indigo-500 group-hover:scale-110 transition-transform"></i>
                                        <span class="sm:inline" x-text="importFile ? importFile.name : 'Import Voters'"></span>
                                        <input type="file" @change="handleFileSelect" accept=".csv,.xml,.xlsx,.xls,.tsv" required class="sr-only" />
                                    </label>
                                    <div x-show="importing" class="ml-2">
                                        <i class="ri-loader-4-line animate-spin text-indigo-600"></i>
                                    </div>
                                </div>

                                <a href="{{ route('admin.voters.create') }}" class="hidden lg:inline-flex items-center gap-2 px-4 py-2 bg-slate-900 text-white rounded-lg text-xs font-bold hover:bg-indigo-600 transition-all shadow-md">
                                    <i class="ri-user-add-line text-lg"></i>
                                    Add Voter
                                </a>


                            </div>
                        </div>
                    </div>

                    <div class="p-4 sm:p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                            <div class="md:col-span-2">
                                <label class="sr-only" for="global-search">Search voters</label>
                                <div class="relative">
                                    <input id="global-search" x-model="search" type="search" placeholder="Search by name, email, or student ID"
                                           @keyup.enter="$dispatch('search', { q: search, filter: filterBy, form: selectedForm })"
                                           class="w-full border rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                                           aria-label="Search voters" />
                                    <button @click="$dispatch('search', { q: search, filter: filterBy, form: selectedForm })"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-indigo-600 hover:text-indigo-800" aria-label="Execute search">
                                        <i class="ri-search-line text-lg"></i>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="sr-only" for="filter-by">Filter by</label>
                                <select id="filter-by" x-model="filterBy"
                                        class="w-full border rounded-lg px-3 py-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-200">
                                    <option value="all">Filter: All</option>
                                    <option value="email">Filter: Email</option>
                                    <option value="registration_status">Filter: Registration Status</option>
                                </select>
                            </div>

                            <div>
                                <label class="sr-only" for="filter-form">Form</label>
                                <select id="filter-form" x-model="selectedForm"
                                        class="w-full border rounded-lg px-3 py-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-200">
                                    <option value="all">All Forms</option>
                                    @foreach($forms as $form)
                                        <option value="{{ data_get($form, 'id') }}">
                                            {{ data_get($form, 'title') ?? data_get($form, 'name') ?? 'Form '.data_get($form, 'id') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex items-center justify-end">
                                <button @click="$dispatch('search', { q: search, filter: filterBy, form: selectedForm })"
                                        class="w-full bg-slate-900 text-white rounded-lg px-6 py-3 font-bold text-xs tracking-widest hover:bg-indigo-600 transition-all shadow-md">
                                    REFINE
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            <!-- Table -->
            <div class="bg-white rounded-xl border border-gray-200/60 shadow-sm overflow-hidden mb-12">
                <div class="px-4 sm:px-6 py-4 border-b border-gray-200/60 bg-gradient-to-r from-white via-indigo-50/30 to-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Registered Voters</h3>
                        <p class="text-[11px] text-gray-500 font-medium">Complete list of registered voters</p>
                    </div>
                    <div class="flex items-center justify-between sm:justify-end gap-4 w-full sm:w-auto">
                        <div class="text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-lg">
                            <span x-text="total"></span> Total
                        </div>
                        <a href="{{ route('admin.voters.create') }}" class="lg:hidden inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-bold hover:bg-indigo-700">
                            <i class="ri-add-line mr-2"></i> New Voter
                        </a>
                    </div>
                </div>

                <div class="responsive-table-container">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">Voter Identity</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">Contact Details</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">Registration Info</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">Status</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 text-right">Management</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($collection as $voter)
                                @php
                                    $id = data_get($voter, 'id');
                                    $name = data_get($voter, 'name') ?? '—';
                                    $student_id = data_get($voter, 'student_id') ?? 'N/A';
                                    $email = data_get($voter, 'email') ?? '—';
                                    $phone = data_get($voter, 'phone') ?? data_get($voter, 'phone_number') ?? '—';
                                    $created_at = data_get($voter, 'created_at');
                                    $status = data_get($voter, 'registration_status') ?? 'pending';

                                    $statusConfig = [
                                        'approved' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'dot' => 'bg-emerald-500', 'icon' => 'ri-checkbox-circle-line'],
                                        'pending' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'dot' => 'bg-amber-500', 'icon' => 'ri-time-line'],
                                        'declined' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'dot' => 'bg-red-500', 'icon' => 'ri-close-circle-line'],
                                    ];
                                    $cfg = $statusConfig[$status] ?? $statusConfig['pending'];
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition-all group">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center text-slate-500 font-bold text-xs group-hover:scale-110 transition-transform">
                                                {{ substr($name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-900 leading-tight text-sm">{{ $name }}</p>
                                                <div class="flex items-center gap-2 mt-0.5">
                                                    <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-wider">{{ $student_id }}</p>
                                                    @if(data_get($voter, 'id_photo'))
                                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-blue-50 text-blue-600 rounded text-[8px] font-black uppercase tracking-widest border border-blue-100" title="ID Photo Uploaded">
                                                            <i class="ri-image-line"></i>
                                                            ID
                                                        </span>
                                                        @php
                                                            $isDuplicate = false;
                                                            if (data_get($voter, 'id_photo_hash')) {
                                                                $isDuplicate = \App\Models\Voter::where('election_id', data_get($voter, 'election_id'))
                                                                    ->where('id', '!=', data_get($voter, 'id'))
                                                                    ->whereNotNull('id_photo_hash')
                                                                    ->get()
                                                                    ->contains(function($v) use ($voter) {
                                                                        return \App\Helpers\ImageHash::distance(data_get($voter, 'id_photo_hash'), $v->id_photo_hash) <= 5;
                                                                    });
                                                            }
                                                        @endphp
                                                        @if($isDuplicate)
                                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-red-50 text-red-600 rounded text-[8px] font-black uppercase tracking-widest border border-red-100" title="Potential Duplicate ID Photo Detected">
                                                                <i class="ri-error-warning-line"></i>
                                                                FLAGGED
                                                            </span>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="space-y-0.5">
                                            <div class="flex items-center gap-2 text-xs font-bold text-slate-600">
                                                <i class="ri-mail-line text-slate-400"></i>
                                                {{ $email }}
                                            </div>
                                            <div class="flex items-center gap-2 text-[10px] font-bold text-slate-400">
                                                <i class="ri-phone-line"></i>
                                                {{ $phone }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="space-y-0.5">
                                            <p class="text-xs font-bold text-slate-700">{{ optional(data_get($voter, 'election'))->title ?? 'No Form Assigned' }}</p>
                                            <p class="text-[10px] font-bold text-slate-400 italic">
                                                Joined: {{ $created_at && method_exists($created_at, 'format') ? $created_at->format('M d, Y') : ($created_at ?? '—') }}
                                            </p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-tight {{ $cfg['bg'] }} {{ $cfg['text'] }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $cfg['dot'] }}"></span>
                                            {{ $status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2 transition-all">
                                            <a href="{{ route('admin.voters.show', $id) }}"
                                               class="w-8 h-8 bg-white border border-slate-200 rounded-lg flex items-center justify-center text-slate-500 hover:text-indigo-600 hover:border-indigo-200 hover:shadow transition-all"
                                               title="View Details">
                                                <i class="ri-eye-line text-sm"></i>
                                            </a>
                                            <a href="{{ route('admin.voters.edit', $id) }}"
                                               class="w-8 h-8 bg-white border border-slate-200 rounded-lg flex items-center justify-center text-slate-500 hover:text-blue-600 hover:border-blue-200 hover:shadow transition-all"
                                               title="Edit Voter">
                                                <i class="ri-edit-line text-sm"></i>
                                            </a>
                                            @if($status === 'pending')
                                                <form method="POST" action="{{ route('admin.voters.approve', $id) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" onclick="return confirm('Approve this voter?')"
                                                            class="w-8 h-8 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center hover:bg-emerald-500 hover:text-white transition-all shadow-sm"
                                                            title="Approve">
                                                        <i class="ri-check-line text-sm font-bold"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-300">
                                                <i class="ri-user-search-line text-3xl"></i>
                                            </div>
                                            <div>
                                                <p class="text-base font-bold text-slate-900 uppercase tracking-tight">No Voters Found</p>
                                                <p class="text-[11px] text-slate-500 font-bold">Try adjusting your filters or import a new list.</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                        Showing <span class="text-slate-900">{{ $firstItem }}</span> - <span class="text-slate-900">{{ $lastItem }}</span> of <span class="text-slate-900">{{ $total }}</span> Results
                    </p>
                    <div>
                        @if ($isPaginated)
                            {{ $voters->links() }}
                        @endif
                    </div>
                </div>
            </div>

                @if (session('success'))
                    <div class="rounded-md bg-green-50 border-l-4 border-green-600 p-4">
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="rounded-md bg-red-50 border-l-4 border-red-600 p-4">
                        <ul class="text-sm text-red-700 list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </main>

        <!-- Voter Import Preview Modal -->
        <div x-show="showImportModal"
             class="fixed inset-0 z-[110] overflow-y-auto"
             x-cloak>
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showImportModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                     @click="showImportModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showImportModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-white/20">

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <div class="flex items-center justify-between mb-6">
                                    <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                                        <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600">
                                            <i class="ri-file-list-3-line"></i>
                                        </div>
                                        Import Preview
                                    </h3>
                                    <button @click="showImportModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                                        <i class="ri-close-line text-2xl"></i>
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Assign to Election</label>
                                        <select x-model="selectedImportElection"
                                                class="w-full bg-slate-50 border-0 rounded-xl px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-indigo-500/20 transition-all">
                                            <option value="">Select Election</option>
                                            @foreach($forms as $form)
                                                <option value="{{ data_get($form, 'id') }}">
                                                    {{ data_get($form, 'title') ?? data_get($form, 'name') ?? 'Form '.data_get($form, 'id') }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Initial Status</label>
                                        <select x-model="registrationStatus"
                                                class="w-full bg-slate-50 border-0 rounded-xl px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-indigo-500/20 transition-all">
                                            <option value="approved">Approved</option>
                                            <option value="pending">Pending</option>
                                        </select>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Notification</label>
                                        <div class="w-full bg-indigo-50 border border-indigo-100 rounded-xl px-4 py-2.5 flex items-center gap-2">
                                            <i class="ri-mail-send-line text-indigo-600"></i>
                                            <span class="text-[11px] text-indigo-700 font-bold leading-tight uppercase tracking-wider">
                                                Credentials will be automatically generated and emailed
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="relative overflow-hidden rounded-2xl border border-slate-100 bg-slate-50/50">
                                    <div class="max-h-[400px] overflow-y-auto custom-scrollbar">
                                        <table class="w-full text-left border-collapse">
                                            <thead class="sticky top-0 z-10 bg-slate-100/80 backdrop-blur-md">
                                                <tr>
                                                    <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Voter</th>
                                                    <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Contact</th>
                                                    <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-right">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-100 bg-white/50">
                                                <template x-for="(row, index) in importPreviewData" :key="index">
                                                    <tr class="hover:bg-indigo-50/30 transition-colors group">
                                                        <td class="px-6 py-4">
                                                            <div class="flex items-center gap-3">
                                                                <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center text-slate-500 font-bold text-[10px]" x-text="row.name ? row.name.charAt(0) : '?'"></div>
                                                                <div>
                                                                    <p class="font-bold text-slate-900 text-sm" x-text="row.name"></p>
                                                                    <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-wider" x-text="row.student_id"></p>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4">
                                                            <div class="text-xs font-medium text-slate-600" x-text="row.email"></div>
                                                            <div class="text-[10px] text-slate-400" x-text="row.phone"></div>
                                                        </td>
                                                        <td class="px-6 py-4 text-right">
                                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-tight"
                                                                  :class="row.is_duplicate ? 'bg-red-50 text-red-700' : 'bg-emerald-50 text-emerald-700'">
                                                                <span class="w-1.5 h-1.5 rounded-full" :class="row.is_duplicate ? 'bg-red-500' : 'bg-emerald-500'"></span>
                                                                <span x-text="row.status"></span>
                                                            </span>
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
                    <div class="bg-slate-50 px-6 py-6 flex items-center justify-between gap-4">
                        <div class="text-xs font-medium text-slate-500">
                            <span class="font-bold text-slate-900" x-text="importPreviewData.length"></span> voters detected
                        </div>
                        <div class="flex items-center gap-3">
                            <button type="button"
                                    @click="showImportModal = false"
                                    class="px-6 py-2.5 text-xs font-bold text-slate-600 hover:text-slate-900 uppercase tracking-widest transition-all">
                                Cancel
                            </button>
                            <button type="button"
                                    @click="processImport"
                                    :disabled="importing || !selectedImportElection"
                                    class="inline-flex items-center gap-2 px-8 py-2.5 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-indigo-600 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-slate-900/10 transition-all active:scale-95">
                                <i x-show="importing" class="ri-loader-4-line animate-spin"></i>
                                <span x-text="importing ? 'Processing...' : 'Complete Import'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
