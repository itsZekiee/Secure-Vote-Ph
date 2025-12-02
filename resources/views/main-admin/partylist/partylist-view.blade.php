@extends('layouts.app-main-admin')

@php
    // Defensive guard: support both `$party` and `$partylist`
    $party = $party ?? ($partylist ?? null);
    if (is_null($party)) {
        abort(404, 'Partylist not found.');
    }

    $id = $party->id ?? 0;

    $indexUrl = Route::has('admin.partylists.index') ? route('admin.partylists.index') :
                (Route::has('partylists.index') ? route('partylists.index') : url('/admin/partylists'));

    if (Route::has('admin.partylists.edit')) {
        $editUrl = route('admin.partylists.edit', $id);
    } elseif (Route::has('partylists.edit')) {
        $editUrl = route('partylists.edit', $id);
    } else {
        $editUrl = url('/admin/partylists/'.$id.'/edit');
    }

    if (Route::has('admin.partylists.destroy')) {
        $destroyAction = route('admin.partylists.destroy', $id);
    } elseif (Route::has('partylists.destroy')) {
        $destroyAction = route('partylists.destroy', $id);
    } else {
        $destroyAction = url('/admin/partylists/'.$id);
    }
@endphp

@section('title', ($party->name ?? 'Partylist') . ' — Overview')

@section('content')
    <div x-data="{
        showDeleteModal: false,

        confirmDelete() {
            this.showDeleteModal = false;
            document.getElementById('delete-form').submit();
        }
    }" class="min-h-screen bg-gray-50">

        <!-- Delete Confirmation Modal -->
        <div x-show="showDeleteModal" x-cloak class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div x-show="showDeleteModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="bg-white rounded-lg w-full max-w-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete Party List</h3>
                <p class="text-gray-600 mb-6">Are you sure you want to delete <strong>{{ $party->name }}</strong>? This action cannot be undone.</p>
                <div class="flex justify-end space-x-3">
                    <button @click="showDeleteModal = false" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                    <button @click="confirmDelete()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Delete</button>
                </div>
            </div>
        </div>

        <!-- Header -->
        <div class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-6 py-4">
                <div class="flex items-center justify-between">
                    <!-- Breadcrumb -->
                    <div class="flex items-center space-x-4">
                        <a href="{{ $indexUrl }}" class="p-2 hover:bg-gray-100 rounded-lg">
                            <i class="ri-arrow-left-line text-gray-600"></i>
                        </a>
                        <nav class="flex items-center space-x-2 text-sm text-gray-600">
                            <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Dashboard</a>
                            <i class="ri-arrow-right-s-line text-gray-400"></i>
                            <a href="{{ $indexUrl }}" class="hover:text-gray-900">Partylists</a>
                            <i class="ri-arrow-right-s-line text-gray-400"></i>
                            <span class="text-gray-900 font-medium">{{ $party->name }}</span>
                        </nav>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-3">
                        <a href="{{ $editUrl }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="ri-edit-line mr-2"></i>Edit
                        </a>
                        <button @click="showDeleteModal = true" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            <i class="ri-delete-bin-line mr-2"></i>Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-6 py-8">
            <!-- Party Header -->
            <div class="bg-white rounded-lg border border-gray-200 p-8 mb-8">
                <div class="flex items-start space-x-6">
                    <!-- Party Logo -->
                    <div class="flex-shrink-0">
                        <div class="w-20 h-20 bg-blue-100 rounded-lg flex items-center justify-center">
                            @if($party->logo && Storage::exists('public/partylists/' . $party->logo))
                                <img src="{{ asset('storage/partylists/' . $party->logo) }}"
                                     alt="{{ $party->name }} Logo"
                                     class="w-full h-full object-cover rounded-lg">
                            @else
                                <i class="ri-flag-line text-2xl text-blue-600"></i>
                            @endif
                        </div>
                    </div>

                    <!-- Party Info -->
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-2">
                            <h1 class="text-2xl font-bold text-gray-900">{{ $party->name }}</h1>
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $party->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ ucfirst($party->status ?? 'active') }}
                            </span>
                        </div>

                        @if($party->description)
                            <p class="text-gray-600 mb-4">{{ $party->description }}</p>
                        @else
                            <p class="text-gray-500 mb-4">No description provided.</p>
                        @endif

                        <!-- Stats -->
                        <div class="flex items-center space-x-6 text-sm">
                            <div class="flex items-center space-x-2">
                                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                <span class="text-gray-600">{{ $party->candidates()->count() ?? 0 }} Members</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                <span class="text-gray-600">0 Elections</span>
                            </div>
                            @if($party->acronym)
                                <div class="flex items-center space-x-2">
                                    <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                                    <span class="text-gray-600">{{ $party->acronym }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-3 space-y-8">
                    <!-- Party Details -->
                    <div class="bg-white rounded-lg border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                                <i class="ri-information-line mr-2 text-blue-600"></i>
                                Party Details
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Party Name</label>
                                    <p class="text-gray-900 font-medium">{{ $party->name ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Acronym</label>
                                    <p class="text-gray-900 font-medium">{{ $party->acronym ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Status</label>
                                    <p class="text-gray-900 font-medium">{{ ucfirst($party->status ?? 'Active') }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Created</label>
                                    <p class="text-gray-900 font-medium">{{ $party->created_at ? $party->created_at->format('M d, Y') : 'Unknown' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Platform -->
                    @if($party->platform)
                        <div class="bg-white rounded-lg border border-gray-200">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                                    <i class="ri-flag-line mr-2 text-green-600"></i>
                                    Party Platform
                                </h2>
                            </div>
                            <div class="p-6">
                                <div class="prose max-w-none text-gray-700">
                                    {!! nl2br(e($party->platform)) !!}
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Candidates Section -->
                    <div class="bg-white rounded-lg border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                                <i class="ri-team-line mr-2 text-green-600"></i>
                                Candidates ({{ $party->candidates()->count() }})
                            </h2>
                            <a href="{{ route('admin.candidates.create') }}?partylist={{ $id }}"
                               class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                                <i class="ri-add-line mr-2"></i>Add Candidate
                            </a>
                        </div>
                        <div class="overflow-x-auto">
                            @if($party->candidates && $party->candidates->count() > 0)
                                <table class="w-full">
                                    <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($party->candidates as $candidate)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                                        <i class="ri-user-line text-gray-600"></i>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">{{ $candidate->name ?? 'N/A' }}</div>
                                                        <div class="text-sm text-gray-500">{{ $candidate->email ?? 'N/A' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $candidate->position ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        {{ ucfirst($candidate->status ?? 'active') }}
                                                    </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="#" class="text-blue-600 hover:text-blue-900 mr-4">View</a>
                                                <a href="#" class="text-red-600 hover:text-red-900">Remove</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="px-6 py-12 text-center">
                                    <i class="ri-team-line text-4xl text-gray-400 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No candidates yet</h3>
                                    <p class="text-gray-500 mb-6">Get started by adding the first candidate to this party list.</p>
                                    <a href="{{ route('admin.candidates.create') }}?partylist={{ $id }}"
                                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                        <i class="ri-add-line mr-2"></i>Add First Candidate
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Statistics -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="ri-bar-chart-line mr-2 text-green-600"></i>
                            Statistics
                        </h3>

                        <div class="space-y-6">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-blue-600">{{ $party->candidates()->count() }}</div>
                                <div class="text-sm text-gray-600">Total Members</div>
                            </div>

                            <div class="text-center">
                                <div class="text-3xl font-bold text-green-600">0</div>
                                <div class="text-sm text-gray-600">Elections Held</div>
                            </div>

                            <div class="text-center">
                                <div class="text-3xl font-bold text-purple-600">0</div>
                                <div class="text-sm text-gray-600">Total Votes</div>
                            </div>
                        </div>

                        <button class="w-full mt-6 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center justify-center">
                            <i class="ri-user-settings-line mr-2"></i>
                            Manage Members
                        </button>
                    </div>

                    <!-- Quick Info -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="ri-information-line mr-2 text-orange-600"></i>
                            Quick Info
                        </h3>

                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Status</span>
                                <span class="font-medium {{ $party->status === 'active' ? 'text-green-600' : 'text-gray-600' }}">
                                    {{ ucfirst($party->status ?? 'Active') }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Created</span>
                                <span class="font-medium text-gray-900">{{ $party->created_at ? $party->created_at->diffForHumans() : 'Unknown' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Organization -->
                    @if($party->organization)
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="ri-building-line mr-2 text-purple-600"></i>
                                Organization
                            </h3>
                            <div class="text-center">
                                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                                    <i class="ri-building-line text-xl text-purple-600"></i>
                                </div>
                                <p class="font-semibold text-gray-900">{{ $party->organization->name }}</p>
                                @if($party->organization->description)
                                    <p class="text-gray-600 text-sm mt-2">{{ Str::limit($party->organization->description, 100) }}</p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Hidden Delete Form -->
        <form id="delete-form" action="{{ $destroyAction }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>
@endsection
