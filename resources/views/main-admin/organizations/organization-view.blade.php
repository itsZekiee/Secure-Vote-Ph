@extends('layouts.app-main-admin')

@php
    $id = $organization->id ?? 0;

    // Index/back URL fallback
    $indexUrl = Route::has('admin.organizations.index') ? route('admin.organizations.index') :
                (Route::has('organizations.index') ? route('organizations.index') : url('/admin/organizations'));

    // Edit, members, destroy fallbacks
    $editUrl = Route::has('admin.organizations.edit') ? route('admin.organizations.edit', $id) :
               (Route::has('organizations.edit') ? route('organizations.edit', $id) : url('/admin/organizations/'.$id.'/edit'));

    $membersUrl = Route::has('admin.organizations.users.index') ? route('admin.organizations.users.index', $id) :
                  (Route::has('organizations.users.index') ? route('organizations.users.index', $id) : url('/admin/organizations/'.$id.'/users'));

    $destroyAction = Route::has('admin.organizations.destroy') ? route('admin.organizations.destroy', $id) :
                     (Route::has('organizations.destroy') ? route('organizations.destroy', $id) : url('/admin/organizations/'.$id));

    $partylists = $organization->partylists ?? ($partylists ?? collect());

    // Calculate accurate statistics
    $votersCount = $organization->users_count ?? $organization->users()->count() ?? 0;
    $electionsCount = $organization->elections_count ?? $organization->elections()->count() ?? 0;
    $partylistsCount = $partylists->count();
    $membershipDate = $organization->created_at;
@endphp

@section('title', ($organization->name ?? 'Organization') . ' — Overview')

@section('content')
    <div x-data="{
        showDeleteModal: false,
        showPartyDeleteModal: false,
        partyToDelete: null,

        confirmDelete() {
            this.showDeleteModal = true;
        },

        confirmPartyDelete(partyId, partyName) {
            this.partyToDelete = { id: partyId, name: partyName };
            this.showPartyDeleteModal = true;
        }
    }" class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/40 to-indigo-50/30">

        <!-- Delete Organization Modal -->
        <div x-show="showDeleteModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div x-show="showDeleteModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 transform translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 transform translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="bg-white rounded-2xl shadow-2xl max-w-md mx-4 overflow-hidden">
                <div class="p-8 text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-red-500 to-rose-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="ri-delete-bin-line text-white text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Delete Organization</h3>
                    <p class="text-gray-600 mb-6">Are you sure you want to delete "{{ $organization->name }}"? This action cannot be undone and will remove all associated data.</p>
                    <div class="flex space-x-4">
                        <button @click="showDeleteModal = false"
                                class="flex-1 px-6 py-3 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-all duration-200 font-semibold">
                            Cancel
                        </button>
                        <form action="{{ $destroyAction }}" method="POST" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full px-6 py-3 bg-gradient-to-r from-red-600 to-rose-600 text-white rounded-xl hover:from-red-700 hover:to-rose-700 transition-all duration-200 font-semibold">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Partylist Modal -->
        <div x-show="showPartyDeleteModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div x-show="showPartyDeleteModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 transform translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 transform translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="bg-white rounded-2xl shadow-2xl max-w-md mx-4 overflow-hidden">
                <div class="p-8 text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-orange-500 to-red-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="ri-delete-bin-line text-white text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Delete Partylist</h3>
                    <p class="text-gray-600 mb-6">Are you sure you want to delete "<span x-text="partyToDelete?.name"></span>"? This action cannot be undone.</p>
                    <div class="flex space-x-4">
                        <button @click="showPartyDeleteModal = false; partyToDelete = null"
                                class="flex-1 px-6 py-3 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-all duration-200 font-semibold">
                            Cancel
                        </button>
                        <button @click="if(partyToDelete) { document.getElementById('delete-party-' + partyToDelete.id).submit(); }"
                                class="flex-1 px-6 py-3 bg-gradient-to-r from-orange-600 to-red-600 text-white rounded-xl hover:from-orange-700 hover:to-red-700 transition-all duration-200 font-semibold">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Navigation Bar -->
        <div class="bg-gradient-to-r from-white/95 via-blue-50/30 to-white/95 backdrop-blur-xl border-b border-gray-200/50 sticky top-0 z-40 shadow-sm">
            <div class="px-8 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <a href="{{ $indexUrl }}"
                           class="flex items-center justify-center w-10 h-10 rounded-lg bg-gray-50 hover:bg-gray-100 transition-all duration-200 border border-gray-200 hover:border-gray-300">
                            <i class="ri-arrow-left-line text-gray-600"></i>
                        </a>

                        <div class="h-6 w-px bg-gray-300"></div>

                        <nav class="flex items-center space-x-2" aria-label="Breadcrumb">
                            <ol class="flex items-center space-x-2">
                                <li>
                                    <a href="{{ route('admin.dashboard') }}"
                                       class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition-colors duration-200">
                                        Dashboard
                                    </a>
                                </li>
                                <li>
                                    <i class="ri-arrow-right-s-line text-gray-400 text-xs"></i>
                                </li>
                                <li>
                                    <a href="{{ $indexUrl }}"
                                       class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition-colors duration-200">
                                        Organizations
                                    </a>
                                </li>
                                <li>
                                    <i class="ri-arrow-right-s-line text-gray-400 text-xs"></i>
                                </li>
                                <li class="flex items-center">
                                    <span class="text-sm font-semibold text-indigo-700">{{ Str::limit($organization->name, 20) }}</span>
                                </li>
                            </ol>
                        </nav>
                    </div>

                    <div class="flex items-center space-x-3">
                        <a href="{{ $editUrl }}"
                           class="inline-flex items-center px-4 py-2 text-sm font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-all duration-200">
                            <i class="ri-edit-line text-sm mr-2"></i>
                            Edit
                        </a>

                        <button @click="confirmDelete()"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-red-600 rounded-lg hover:bg-red-700 transition-all duration-200">
                            <i class="ri-delete-bin-line text-sm mr-2"></i>
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-8 py-8">
            <!-- Enhanced Header Section -->
            <div class="max-w-7xl mx-auto mb-8">
                <div class="bg-white rounded-3xl shadow-sm border border-gray-200/60 overflow-hidden">
                    <div class="p-8">
                        <div class="flex items-start space-x-6">
                            <!-- Organization Logo -->
                            <div class="flex-shrink-0">
                                <div class="w-24 h-24 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-2xl border-2 border-gray-200/50 flex items-center justify-center overflow-hidden">
                                    @if(!empty($organization->logo_url))
                                        <img src="{{ $organization->logo_url }}" alt="{{ $organization->name }}" class="object-cover w-full h-full"/>
                                    @else
                                        <i class="ri-building-2-line text-3xl text-indigo-600"></i>
                                    @endif
                                </div>
                            </div>

                            <!-- Organization Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <div class="flex items-center space-x-3 mb-2">
                                            <h1 class="text-3xl font-bold text-gray-900">{{ $organization->name }}</h1>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ ($organization->status ?? 'inactive') === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                                                {{ ucfirst($organization->status ?? 'inactive') }}
                                            </span>
                                        </div>
                                        <p class="text-lg text-gray-600 mb-4 max-w-3xl">{{ $organization->description ?? 'No description provided.' }}</p>

                                        <!-- Quick Stats Bar -->
                                        <div class="flex items-center space-x-8">
                                            <div class="flex items-center space-x-2">
                                                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                                <span class="text-sm text-gray-600">{{ $votersCount }} {{ Str::plural('Member', $votersCount) }}</span>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                                <span class="text-sm text-gray-600">{{ $electionsCount }} {{ Str::plural('Election', $electionsCount) }}</span>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                                                <span class="text-sm text-gray-600">{{ $partylistsCount }} {{ Str::plural('Partylist', $partylistsCount) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Primary Content -->
                <div class="lg:col-span-3 space-y-8">
                    <!-- Organization Details Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 overflow-hidden">
                        <div class="px-8 py-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200/60">
                            <h2 class="text-xl font-bold text-gray-900 flex items-center">
                                <i class="ri-information-line text-blue-600 mr-3"></i>
                                Organization Details
                            </h2>
                        </div>

                        <div class="p-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-200/60">
                                        <div class="text-sm font-medium text-gray-500 mb-1">Organization Slug</div>
                                        <div class="text-lg font-semibold text-gray-900">{{ $organization->slug ?? '-' }}</div>
                                    </div>

                                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-200/60">
                                        <div class="text-sm font-medium text-gray-500 mb-1">Contact Email</div>
                                        <div class="text-lg font-semibold text-gray-900">{{ $organization->contact_email ?? $organization->email ?? '-' }}</div>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-200/60">
                                        <div class="text-sm font-medium text-gray-500 mb-1">Contact Phone</div>
                                        <div class="text-lg font-semibold text-gray-900">{{ $organization->contact_phone ?? $organization->phone ?? '-' }}</div>
                                    </div>

                                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-200/60">
                                        <div class="text-sm font-medium text-gray-500 mb-1">Created Date</div>
                                        <div class="text-lg font-semibold text-gray-900">{{ optional($organization->created_at)->format('M d, Y') ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Partylists Section -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 overflow-hidden">
                        <div class="px-8 py-6 bg-gradient-to-r from-purple-50 to-pink-50 border-b border-gray-200/60">
                            <div class="flex items-center justify-between">
                                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                                    <i class="ri-team-line text-purple-600 mr-3"></i>
                                    Partylists ({{ $partylistsCount }})
                                </h2>

                                @php
                                    $partyCreateUrl = Route::has('admin.partylists.create') ? route('admin.partylists.create', ['organization' => $id]) :
                                                     (Route::has('partylists.create') ? route('partylists.create', ['organization' => $id]) : url('/admin/partylists/create?organization='.$id));
                                @endphp

                                <a href="{{ $partyCreateUrl }}"
                                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg text-sm hover:from-purple-700 hover:to-indigo-700 transition-all duration-200">
                                    <i class="ri-add-line mr-2"></i>
                                    New Partylist
                                </a>
                            </div>
                        </div>

                        <div class="p-8">
                            @if($partylists->count() > 0)
                                <div class="space-y-4">
                                    @foreach($partylists as $party)
                                        @php
                                            $partyId = $party->id ?? 0;
                                            $partyShow = Route::has('admin.partylists.show') ? route('admin.partylists.show', $partyId) :
                                                         (Route::has('partylists.show') ? route('partylists.show', $partyId) : url('/admin/partylists/'.$partyId));

                                            $partyEdit = Route::has('admin.partylists.edit') ? route('admin.partylists.edit', $partyId) :
                                                         (Route::has('partylists.edit') ? route('partylists.edit', $partyId) : url('/admin/partylists/'.$partyId.'/edit'));

                                            $partyDestroy = Route::has('admin.partylists.destroy') ? route('admin.partylists.destroy', $partyId) :
                                                            (Route::has('partylists.destroy') ? route('partylists.destroy', $partyId) : url('/admin/partylists/'.$partyId));
                                        @endphp

                                        <div class="p-6 border border-gray-200/60 rounded-xl hover:shadow-md transition-all duration-200 bg-gradient-to-r from-gray-50/50 to-white">
                                            <div class="flex items-center justify-between">
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-start space-x-4">
                                                        <div class="w-12 h-12 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-lg flex items-center justify-center">
                                                            <i class="ri-flag-line text-purple-600 text-xl"></i>
                                                        </div>
                                                        <div class="flex-1">
                                                            <div class="flex items-center space-x-3 mb-2">
                                                                <h3 class="text-lg font-semibold text-gray-900">{{ $party->name }}</h3>
                                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ ($party->status ?? '') === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                                                                    {{ ucfirst($party->status ?? 'inactive') }}
                                                                </span>
                                                            </div>

                                                            @if($party->description)
                                                                <p class="text-sm text-gray-600 mb-3 max-w-2xl">{{ Str::limit($party->description, 120) }}</p>
                                                            @endif

                                                            <div class="flex items-center space-x-6 text-sm text-gray-500">
                                                                @if($party->acronym)
                                                                    <span class="flex items-center space-x-1">
                                                                        <i class="ri-price-tag-3-line"></i>
                                                                        <span>{{ $party->acronym }}</span>
                                                                    </span>
                                                                @endif
                                                                <span class="flex items-center space-x-1">
                                                                    <i class="ri-user-line"></i>
                                                                    <span>{{ $party->seats ?? 0 }} seats</span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="flex items-center space-x-2 ml-4">
                                                    <a href="{{ $partyShow }}"
                                                       class="inline-flex items-center px-3 py-2 text-sm font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-all duration-200">
                                                        <i class="ri-eye-line text-xs mr-1"></i>
                                                        View
                                                    </a>
                                                    <a href="{{ $partyEdit }}"
                                                       class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition-all duration-200">
                                                        <i class="ri-edit-line text-xs mr-1"></i>
                                                        Edit
                                                    </a>

                                                    <form action="{{ $partyDestroy }}" method="POST" class="inline" id="delete-party-{{ $partyId }}">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>

                                                    <button @click="confirmPartyDelete({{ $partyId }}, '{{ addslashes($party->name) }}')"
                                                            class="inline-flex items-center px-3 py-2 text-sm font-medium text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-all duration-200">
                                                        <i class="ri-delete-bin-line text-xs mr-1"></i>
                                                        Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                        <i class="ri-team-line text-2xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Partylists</h3>
                                    <p class="text-gray-600 mb-6">This organization doesn't have any partylists yet.</p>
                                    <a href="{{ $partyCreateUrl }}"
                                       class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg text-sm hover:from-purple-700 hover:to-indigo-700 transition-all duration-200">
                                        <i class="ri-add-line mr-2"></i>
                                        Create First Partylist
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Enhanced Sidebar -->
                <div class="space-y-6">
                    <!-- Quick Stats -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-emerald-50 to-teal-50 border-b border-gray-200/60">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center">
                                <i class="ri-bar-chart-line text-emerald-600 mr-2"></i>
                                Statistics
                            </h3>
                        </div>

                        <div class="p-6 space-y-6">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-indigo-600 mb-1">{{ $votersCount }}</div>
                                <div class="text-sm text-gray-600 font-medium">Total Members</div>
                            </div>

                            <div class="text-center">
                                <div class="text-3xl font-bold text-emerald-600 mb-1">{{ $electionsCount }}</div>
                                <div class="text-sm text-gray-600 font-medium">Elections Held</div>
                            </div>

                            <div class="text-center">
                                <div class="text-3xl font-bold text-purple-600 mb-1">{{ $partylistsCount }}</div>
                                <div class="text-sm text-gray-600 font-medium">Registered Partylists</div>
                            </div>

                            <div class="pt-4 border-t border-gray-200">
                                <a href="{{ $membersUrl }}"
                                   class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl text-sm font-medium hover:from-blue-700 hover:to-indigo-700 transition-all duration-200">
                                    <i class="ri-user-settings-line mr-2"></i>
                                    Manage Members
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Info -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-amber-50 to-orange-50 border-b border-gray-200/60">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center">
                                <i class="ri-information-line text-amber-600 mr-2"></i>
                                Quick Info
                            </h3>
                        </div>

                        <div class="p-6 space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Status</span>
                                <span class="text-sm font-semibold {{ ($organization->status ?? 'inactive') === 'active' ? 'text-green-600' : 'text-gray-600' }}">
                                    {{ ucfirst($organization->status ?? 'inactive') }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Created</span>
                                <span class="text-sm font-semibold text-gray-900">
                                    {{ optional($membershipDate)->diffForHumans() ?? 'Unknown' }}
                                </span>
                            </div>

                            @if($organization->contact_email ?? $organization->email)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Contact</span>
                                    <a href="mailto:{{ $organization->contact_email ?? $organization->email }}"
                                       class="text-sm font-semibold text-blue-600 hover:text-blue-800">
                                        Email
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
