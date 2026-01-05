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
    }" class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 pb-12">

        <!-- Delete Confirmation Modal -->
        <div x-show="showDeleteModal" x-cloak class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div x-show="showDeleteModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="bg-white rounded-2xl w-full max-w-md p-6 shadow-2xl border border-gray-200">
                <div class="flex items-center space-x-4 mb-6">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="ri-error-warning-line text-xl text-red-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Delete Party List</h3>
                        <p class="text-gray-500 text-sm">This action cannot be undone.</p>
                    </div>
                </div>
                <p class="text-gray-600 mb-6">Are you sure you want to delete <strong class="text-gray-900">{{ $party->name }}</strong>?</p>
                <div class="flex justify-end space-x-3">
                    <button @click="showDeleteModal = false" class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors font-medium">Cancel</button>
                    <button @click="confirmDelete()" class="px-6 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg hover:from-red-700 hover:to-red-800 transition-all font-medium shadow-lg">Delete</button>
                </div>
            </div>
        </div>

        <!-- Enhanced Header -->
        <div class="bg-white/90 backdrop-blur-md border-b border-gray-200/50 shadow-sm sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-6 py-4">
                <div class="flex items-center justify-between">
                    <!-- Breadcrumb -->
                    <div class="flex items-center space-x-4">
                        <a href="{{ $indexUrl }}" class="p-2 hover:bg-gray-100 rounded-xl transition-all duration-200 hover:scale-105 group">
                            <i class="ri-arrow-left-line text-gray-600 text-lg group-hover:text-blue-600"></i>
                        </a>
                        <nav class="flex items-center space-x-2 text-sm text-gray-600">
                            <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 transition-colors font-medium">Dashboard</a>
                            <i class="ri-arrow-right-s-line text-gray-400"></i>
                            <a href="{{ $indexUrl }}" class="hover:text-blue-600 transition-colors font-medium">Partylists</a>
                            <i class="ri-arrow-right-s-line text-gray-400"></i>
                            <span class="text-gray-900 font-semibold">{{ $party->name }}</span>
                        </nav>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-3">
                        <a href="{{ $editUrl }}" class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-lg font-medium">
                            <i class="ri-edit-line mr-2"></i>Edit Party
                        </a>
                        <button @click="showDeleteModal = true" class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-xl hover:from-red-700 hover:to-red-800 transition-all duration-200 shadow-lg font-medium">
                            <i class="ri-delete-bin-line mr-2"></i>Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-6 py-8">
            <!-- Party Header Card -->
            <div class="bg-white/95 backdrop-blur-sm rounded-3xl border border-gray-200/60 shadow-xl overflow-hidden mb-8">
                <div class="h-32 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 relative">
                    <div class="absolute -bottom-12 left-8">
                        <div class="w-32 h-32 bg-white rounded-2xl shadow-xl border-4 border-white flex items-center justify-center overflow-hidden">
                            @if($party->logo && Storage::exists('public/partylists/' . $party->logo))
                                <img src="{{ asset('storage/partylists/' . $party->logo) }}"
                                     alt="{{ $party->name }} Logo"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center">
                                    <i class="ri-flag-line text-4xl text-blue-600"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                    @if($party->color)
                        <div class="absolute top-4 right-4 px-3 py-1 bg-white/20 backdrop-blur-md rounded-full border border-white/30 text-white text-xs font-medium flex items-center">
                            <div class="w-2 h-2 rounded-full mr-2" style="background-color: {{ $party->color }}"></div>
                            Brand Color: {{ strtoupper($party->color) }}
                        </div>
                    @endif
                </div>

                <div class="pt-16 pb-8 px-8">
                    <div class="flex flex-wrap items-start justify-between gap-6">
                        <div class="flex-1 min-w-[300px]">
                            <div class="flex items-center space-x-3 mb-3">
                                <h1 class="text-3xl font-extrabold text-gray-900">{{ $party->name }}</h1>
                                <span class="px-3 py-1 text-xs font-bold rounded-full tracking-wide {{ $party->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ strtoupper($party->status ?? 'active') }}
                                </span>
                            </div>

                            @if($party->description)
                                <p class="text-gray-600 text-lg leading-relaxed max-w-3xl">{{ $party->description }}</p>
                            @else
                                <p class="text-gray-400 italic">No description provided for this party list.</p>
                            @endif
                        </div>

                        <div class="flex flex-wrap gap-4">
                            <div class="bg-blue-50 px-6 py-3 rounded-2xl border border-blue-100 text-center">
                                <div class="text-2xl font-black text-blue-700">{{ $party->candidates()->count() }}</div>
                                <div class="text-xs font-bold text-blue-600 uppercase tracking-tighter">Candidates</div>
                            </div>
                            @if($party->acronym)
                                <div class="bg-purple-50 px-6 py-3 rounded-2xl border border-purple-100 text-center">
                                    <div class="text-2xl font-black text-purple-700">{{ $party->acronym }}</div>
                                    <div class="text-xs font-bold text-purple-600 uppercase tracking-tighter">Acronym</div>
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
                    <!-- Party Details Section -->
                    <div class="bg-white/95 backdrop-blur-sm rounded-2xl border border-gray-200/60 shadow-xl overflow-hidden transition-all hover:shadow-2xl">
                        <div class="px-6 py-4 border-b border-gray-200/50 bg-gradient-to-r from-gray-50 to-white">
                            <h2 class="text-xl font-bold text-gray-900 flex items-center">
                                <i class="ri-information-line mr-2 text-blue-600"></i>
                                Information Details
                            </h2>
                        </div>
                        <div class="p-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Official Name</label>
                                    <p class="text-gray-900 font-semibold text-lg">{{ $party->name ?? 'N/A' }}</p>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Party Acronym</label>
                                    <p class="text-gray-900 font-semibold text-lg">{{ $party->acronym ?? 'N/A' }}</p>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Current Status</label>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-2 h-2 rounded-full {{ $party->status === 'active' ? 'bg-emerald-500' : 'bg-rose-500' }}"></div>
                                        <p class="text-gray-900 font-semibold text-lg">{{ ucfirst($party->status ?? 'Active') }}</p>
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Registration Date</label>
                                    <p class="text-gray-900 font-semibold text-lg">{{ $party->created_at ? $party->created_at->format('M d, Y') : 'Unknown' }}</p>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Last Updated</label>
                                    <p class="text-gray-900 font-semibold text-lg">{{ $party->updated_at ? $party->updated_at->diffForHumans() : 'N/A' }}</p>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Election ID</label>
                                    <p class="text-gray-900 font-semibold text-lg">#{{ $party->election_id ?? 'Not Assigned' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Platform Section -->
                    <div class="bg-white/95 backdrop-blur-sm rounded-2xl border border-gray-200/60 shadow-xl overflow-hidden transition-all hover:shadow-2xl">
                        <div class="px-6 py-4 border-b border-gray-200/50 bg-gradient-to-r from-emerald-50 to-white">
                            <h2 class="text-xl font-bold text-gray-900 flex items-center">
                                <i class="ri-flag-line mr-2 text-emerald-600"></i>
                                Party Platform & Agenda
                            </h2>
                        </div>
                        <div class="p-8">
                            @if($party->platform)
                                <div class="prose prose-indigo max-w-none">
                                    <div class="text-gray-700 leading-relaxed text-lg italic border-l-4 border-emerald-200 pl-6">
                                        {!! nl2br(e($party->platform)) !!}
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-10 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                                    <i class="ri-file-list-3-line text-4xl text-gray-300 mb-2"></i>
                                    <p class="text-gray-500">No platform details have been recorded for this party.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Candidates Table Section -->
                    <div class="bg-white/95 backdrop-blur-sm rounded-2xl border border-gray-200/60 shadow-xl overflow-hidden">
                        <div class="px-8 py-6 border-b border-gray-200/50 flex items-center justify-between bg-gradient-to-r from-blue-50 to-white">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                                    <i class="ri-team-line mr-2 text-blue-600"></i>
                                    Candidates List
                                </h2>
                                <p class="text-sm text-gray-500 mt-1">Official members representing this party list</p>
                            </div>
                            <a href="{{ route('admin.candidates.create') }}?partylist={{ $id }}"
                               class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white text-sm font-bold rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all">
                                <i class="ri-add-line mr-2"></i>New Candidate
                            </a>
                        </div>
                        <div class="overflow-x-auto">
                            @if($party->candidates && $party->candidates->count() > 0)
                                <table class="w-full">
                                    <thead>
                                    <tr class="bg-gray-50/50">
                                        <th class="px-8 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Candidate</th>
                                        <th class="px-8 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Position</th>
                                        <th class="px-8 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Status</th>
                                        <th class="px-8 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-widest">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                    @foreach($party->candidates as $candidate)
                                        <tr class="hover:bg-gray-50/50 transition-colors group">
                                            <td class="px-8 py-5">
                                                <div class="flex items-center">
                                                    <div class="w-12 h-12 rounded-xl bg-gray-100 border border-gray-200 flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                                                        @if($candidate->photo)
                                                            <img src="{{ asset('storage/' . $candidate->photo) }}" class="w-full h-full object-cover rounded-xl">
                                                        @else
                                                            <i class="ri-user-line text-xl text-gray-400"></i>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <div class="text-base font-bold text-gray-900">{{ $candidate->user->name ?? ($candidate->name ?? 'N/A') }}</div>
                                                        <div class="text-xs text-gray-500 font-medium">{{ $candidate->user->email ?? ($candidate->email ?? 'N/A') }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-8 py-5">
                                                <span class="inline-flex items-center px-3 py-1 bg-blue-50 text-blue-700 text-xs font-bold rounded-lg border border-blue-100 uppercase tracking-wide">
                                                    {{ $candidate->position->name ?? ($candidate->position ?? 'N/A') }}
                                                </span>
                                            </td>
                                            <td class="px-8 py-5">
                                                <div class="flex items-center space-x-2">
                                                    <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                                    <span class="text-sm font-bold text-gray-700">{{ ucfirst($candidate->status ?? 'active') }}</span>
                                                </div>
                                            </td>
                                            <td class="px-8 py-5 text-right space-x-3">
                                                <a href="{{ route('admin.candidates.show', $candidate->id) }}" class="p-2 text-gray-400 hover:text-blue-600 transition-colors">
                                                    <i class="ri-eye-line text-lg"></i>
                                                </a>
                                                <a href="{{ route('admin.candidates.edit', $candidate->id) }}" class="p-2 text-gray-400 hover:text-indigo-600 transition-colors">
                                                    <i class="ri-edit-line text-lg"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="px-8 py-20 text-center">
                                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 border-2 border-dashed border-gray-200">
                                        <i class="ri-team-line text-3xl text-gray-300"></i>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-900 mb-2">No candidates found</h3>
                                    <p class="text-gray-500 max-w-sm mx-auto mb-8">This party list currently has no official candidates registered for the upcoming election.</p>
                                    <a href="{{ route('admin.candidates.create') }}?partylist={{ $id }}"
                                       class="inline-flex items-center px-8 py-3 bg-blue-600 text-white font-bold rounded-2xl hover:bg-blue-700 shadow-xl shadow-blue-200 transition-all">
                                        <i class="ri-user-add-line mr-2"></i>Register First Candidate
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar Content -->
                <div class="lg:col-span-1 space-y-8">
                    <!-- Quick Stats Card -->
                    <div class="bg-white/95 backdrop-blur-sm rounded-2xl border border-gray-200/60 p-8 shadow-xl">
                        <h3 class="text-lg font-bold text-gray-900 mb-8 flex items-center">
                            <i class="ri-pie-chart-2-line mr-2 text-blue-600"></i>
                            Key Statistics
                        </h3>

                        <div class="space-y-8">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center mr-4">
                                        <i class="ri-team-line text-blue-600"></i>
                                    </div>
                                    <span class="text-sm font-bold text-gray-500 uppercase">Members</span>
                                </div>
                                <span class="text-2xl font-black text-gray-900">{{ $party->candidates()->count() }}</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center mr-4">
                                        <i class="ri-bar-chart-line text-emerald-600"></i>
                                    </div>
                                    <span class="text-sm font-bold text-gray-500 uppercase">Elections</span>
                                </div>
                                <span class="text-2xl font-black text-gray-900">0</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center mr-4">
                                        <i class="ri-check-double-line text-purple-600"></i>
                                    </div>
                                    <span class="text-sm font-bold text-gray-500 uppercase">Votes Cast</span>
                                </div>
                                <span class="text-2xl font-black text-gray-900">0</span>
                            </div>
                        </div>

                        <div class="mt-10 pt-8 border-t border-gray-100">
                            <a href="{{ route('admin.partylists.edit', $id) }}" class="w-full flex items-center justify-center px-6 py-4 bg-gray-900 text-white rounded-2xl font-bold hover:bg-black shadow-xl shadow-gray-200 transition-all group">
                                <i class="ri-settings-4-line mr-2 group-hover:rotate-90 transition-transform"></i>
                                Party Settings
                            </a>
                        </div>
                    </div>

                    <!-- Organization Info -->
                    @if($party->organization)
                        <div class="bg-white/95 backdrop-blur-sm rounded-2xl border border-gray-200/60 p-8 shadow-xl">
                            <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                                <i class="ri-building-line mr-2 text-indigo-600"></i>
                                Organization
                            </h3>
                            <div class="bg-indigo-50/50 rounded-2xl p-6 border border-indigo-100 mb-6 text-center">
                                <div class="w-16 h-16 bg-white rounded-2xl shadow-sm border border-indigo-100 flex items-center justify-center mx-auto mb-4">
                                    <i class="ri-building-line text-3xl text-indigo-600"></i>
                                </div>
                                <p class="text-xl font-black text-gray-900">{{ $party->organization->name }}</p>
                            </div>
                            @if($party->organization->description)
                                <p class="text-gray-500 text-sm leading-relaxed text-center italic">{{ Str::limit($party->organization->description, 120) }}</p>
                            @endif
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
