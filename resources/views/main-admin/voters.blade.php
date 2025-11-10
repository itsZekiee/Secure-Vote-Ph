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
        collapsed: false,
        isMobile: window.innerWidth < 1024,
        search: '',
        filterBy: 'all',
        selectedForm: 'all',
        perPage: 15,
        confirmAction(id, action) {
            if (confirm(`Are you sure you want to ${action} this voter?`)) {
                document.getElementById(`${action}-form-${id}`).submit();
            }
        }
    }"
         x-init="window.addEventListener('resize', () => { isMobile = window.innerWidth < 1024 })"
         class="flex min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50">

        <!-- Sidebar -->
        <x-admin-sidebar />

        <!-- Main content -->
        <main class="flex-1 min-h-screen">
            <!-- Top bar -->
            <div class="bg-white/80 backdrop-blur-sm border-b sticky top-0 z-40">
                <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-600 to-blue-500 flex items-center justify-center shadow">
                            <i class="ri-user-3-line text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-semibold leading-tight">Voter Directory</h1>
                            <p class="text-sm text-gray-500">Manage registered voters and import/export data</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 text-sm text-gray-600">
                        <div>
                            <span class="font-medium text-gray-900">Admin</span>
                            <i class="ri-arrow-right-s-line mx-2"></i>
                            <span>Voters</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-6 py-8 space-y-6">
                <!-- Search & Filters -->
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b bg-gradient-to-r from-white via-indigo-50 to-white">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm border">
                                    <i class="ri-search-line text-indigo-600"></i>
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold text-gray-900">Search & Filter</h2>
                                    <p class="text-sm text-gray-600">Quickly find voters and refine results</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <form method="GET" action="{{ route('admin.voters.export') }}">
                                    <input type="hidden" name="format" value="csv" />
                                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-white border rounded-lg text-sm hover:shadow">
                                        <i class="ri-download-2-line"></i>
                                        Export CSV
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.voters.import.preview') }}" enctype="multipart/form-data" class="inline-flex items-center gap-2 bg-white border rounded-lg px-3 py-2">
                                    @csrf
                                    <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700">
                                        <i class="ri-upload-cloud-line text-lg text-indigo-600"></i>
                                        <span class="hidden sm:inline">Import Excel</span>
                                        <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="sr-only" />
                                    </label>
                                    <button type="submit" class="ml-2 inline-flex items-center gap-2 px-3 py-1 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700">
                                        Upload & Preview
                                    </button>
                                </form>

                                @if (isset($importPath) && $importPath)
                                    <form method="POST" action="{{ route('admin.voters.import.store') }}">
                                        @csrf
                                        <input type="hidden" name="import_path" value="{{ $importPath }}" />
                                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700">
                                            <i class="ri-check-line"></i>
                                            Import All
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
                            <div class="lg:col-span-2">
                                <label class="sr-only" for="global-search">Search voters</label>
                                <div class="relative">
                                    <input id="global-search" x-model="search" type="search" placeholder="Search by name, email, or student ID"
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

                            <div class="hidden lg:flex items-center justify-end">
                                <a href="{{ route('admin.voters.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
                                    <i class="ri-add-line mr-2"></i> New Voter
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="bg-white border rounded-2xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">DOB</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Phone</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Registered</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($collection as $voter)
                                @php
                                    $id = data_get($voter, 'id');
                                    $name = data_get($voter, 'name') ?? '—';
                                    $student_id = data_get($voter, 'student_id') ?? '';
                                    $dob = data_get($voter, 'date_of_birth') ?? data_get($voter, 'dob') ?? null;
                                    $email = data_get($voter, 'email') ?? '—';
                                    $phone = data_get($voter, 'phone') ?? data_get($voter, 'phone_number') ?? '—';
                                    $created_at = data_get($voter, 'created_at');
                                    $status = data_get($voter, 'registration_status') ?? 'pending';
                                    $badgeClass = $status === 'approved' ? 'bg-green-100 text-green-800' : ($status === 'declined' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800');
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $id }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $name }}</div>
                                        <div class="text-xs text-gray-500">{{ $student_id }}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                        {{ $dob && method_exists($dob, 'format') ? $dob->format('Y-m-d') : ($dob ?? '—') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $email }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $phone }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                        {{ $created_at && method_exists($created_at, 'format') ? $created_at->format('Y-m-d') : ($created_at ?? '—') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        <a href="{{ route('admin.voters.show', $id) }}" class="text-indigo-600 hover:underline">View</a>
                                        <a href="{{ route('admin.voters.edit', $id) }}" class="text-gray-600 hover:underline">Edit</a>
                                        @if($status === 'pending')
                                            <form id="approve-form-{{ $id }}" method="POST" action="{{ route('admin.voters.approve', $id) }}" class="inline">
                                                @csrf
                                                <button type="button" onclick="if(confirm('Approve this voter?')){ this.form.submit(); }" class="text-green-600 hover:underline">Approve</button>
                                            </form>
                                            <form id="decline-form-{{ $id }}" method="POST" action="{{ route('admin.voters.decline', $id) }}" class="inline">
                                                @csrf
                                                <button type="button" onclick="if(confirm('Decline this voter?')){ this.form.submit(); }" class="text-red-600 hover:underline ml-2">Decline</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-12 text-center text-sm text-gray-500">
                                        No voters found. Try changing filters or import a list.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="px-6 py-4 bg-white border-t flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing <span class="font-medium">{{ $firstItem }}</span> to <span class="font-medium">{{ $lastItem }}</span> of <span class="font-medium">{{ $total }}</span>
                        </div>

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
    </div>

@endsection
