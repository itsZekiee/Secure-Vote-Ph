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
    }" class="min-h-screen bg-gradient-to-br from-slate-50 via-indigo-50/30 to-blue-50/20 pb-12">

        <!-- Enhanced Delete Organization Modal -->
        <div x-show="showDeleteModal" x-cloak class="fixed inset-0 bg-gray-900/60 backdrop-blur-md flex items-center justify-center z-50 p-4">
            <div x-show="showDeleteModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="bg-white rounded-3xl w-full max-w-md p-8 shadow-2xl border border-white">
                <div class="w-20 h-20 bg-rose-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="ri-delete-bin-line text-rose-600 text-3xl"></i>
                </div>
                <h3 class="text-2xl font-black text-gray-900 text-center mb-2 tracking-tight">Delete Organization</h3>
                <p class="text-gray-500 text-center mb-8 font-medium italic">"{{ $organization->name }}" will be permanently removed. This action is irreversible.</p>
                <div class="flex gap-4">
                    <button @click="showDeleteModal = false" class="flex-1 px-6 py-3.5 bg-gray-100 text-gray-600 rounded-2xl font-bold hover:bg-gray-200 transition-colors">Cancel</button>
                    <form action="{{ $destroyAction }}" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-6 py-3.5 bg-rose-600 text-white rounded-2xl font-bold hover:bg-rose-700 shadow-lg shadow-rose-200 transition-all">Delete Org</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Enhanced Navigation Bar -->
        <div class="bg-white/80 backdrop-blur-xl border-b border-gray-200/50 shadow-sm sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-8 py-4 flex items-center justify-between">
                <div class="flex items-center space-x-6">
                    <a href="{{ $indexUrl }}" class="p-2.5 bg-gray-50 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-2xl transition-all border border-gray-100">
                        <i class="ri-arrow-left-line text-xl"></i>
                    </a>
                    <nav class="hidden md:flex items-center space-x-3">
                        <a href="{{ route('admin.dashboard') }}" class="text-sm font-bold text-gray-400 hover:text-gray-900 transition-colors">DASHBOARD</a>
                        <i class="ri-arrow-right-s-line text-gray-300"></i>
                        <a href="{{ $indexUrl }}" class="text-sm font-bold text-gray-400 hover:text-gray-900 transition-colors uppercase tracking-widest">ORGANIZATIONS</a>
                        <i class="ri-arrow-right-s-line text-gray-300"></i>
                        <span class="text-sm font-black text-blue-600 tracking-wider uppercase">{{ Str::limit($organization->name, 25) }}</span>
                    </nav>
                </div>

                <div class="flex items-center space-x-4">
                    <a href="{{ $editUrl }}" class="inline-flex items-center px-6 py-2.5 bg-blue-600 text-white rounded-2xl font-bold hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all transform hover:scale-105">
                        <i class="ri-edit-line mr-2"></i>Edit Settings
                    </a>
                    <button @click="confirmDelete()" class="inline-flex items-center px-6 py-2.5 bg-rose-50 text-rose-600 rounded-2xl font-bold hover:bg-rose-100 transition-all border border-rose-100">
                        <i class="ri-delete-bin-line mr-2"></i>Delete
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-8 py-10">
            <!-- Professional Header Card -->
            <div class="bg-white/90 backdrop-blur-xl rounded-[40px] shadow-2xl shadow-indigo-200/40 border border-white overflow-hidden mb-12">
                <div class="h-48 bg-gradient-to-r from-blue-700 via-indigo-700 to-purple-800 relative">
                    <div class="absolute inset-0 bg-black/10"></div>
                    <!-- Decor -->
                    <div class="absolute -top-24 -right-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                    <div class="absolute -bottom-12 -left-12 w-48 h-48 bg-blue-400/20 rounded-full blur-2xl"></div>

                    <div class="absolute -bottom-16 left-12">
                        <div class="w-40 h-40 bg-white rounded-[32px] shadow-2xl border-8 border-white flex items-center justify-center overflow-hidden transform hover:rotate-3 transition-transform duration-500">
                            @if(!empty($organization->logo_url))
                                <img src="{{ $organization->logo_url }}" alt="{{ $organization->name }}" class="object-cover w-full h-full"/>
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-blue-50 to-indigo-50 flex items-center justify-center">
                                    <i class="ri-building-2-line text-6xl text-blue-600"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="pt-24 pb-12 px-12">
                    <div class="flex flex-wrap items-start justify-between gap-10">
                        <div class="flex-1 min-w-[320px]">
                            <div class="flex flex-wrap items-center gap-4 mb-4">
                                <h1 class="text-5xl font-black text-gray-900 tracking-tight">{{ $organization->name }}</h1>
                                <span class="px-5 py-1.5 rounded-full text-xs font-black tracking-widest uppercase border {{ ($organization->status ?? 'inactive') === 'active' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-rose-50 text-rose-600 border-rose-100' }}">
                                    {{ $organization->status ?? 'inactive' }}
                                </span>
                            </div>
                            <p class="text-xl text-gray-500 font-medium leading-relaxed max-w-4xl italic">
                                {{ $organization->description ?? 'Empowering democracy through structured organizational excellence.' }}
                            </p>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 bg-gray-50/50 p-6 rounded-[32px] border border-gray-100">
                            <div class="text-center px-4">
                                <div class="text-3xl font-black text-blue-600">{{ number_format($votersCount) }}</div>
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Members</div>
                            </div>
                            <div class="w-px h-10 bg-gray-200 my-auto hidden sm:block"></div>
                            <div class="text-center px-4">
                                <div class="text-3xl font-black text-emerald-600">{{ number_format($electionsCount) }}</div>
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Elections</div>
                            </div>
                            <div class="w-px h-10 bg-gray-200 my-auto hidden sm:block"></div>
                            <div class="text-center px-4">
                                <div class="text-3xl font-black text-indigo-600">{{ number_format($partylistsCount) }}</div>
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Partylists</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-10">
                <!-- Info Section -->
                <div class="lg:col-span-1 space-y-8">
                    <div class="bg-white rounded-[32px] shadow-xl shadow-gray-200/50 border border-gray-100 p-8">
                        <h3 class="text-lg font-black text-gray-900 mb-8 flex items-center">
                            <i class="ri-information-line mr-3 text-blue-600"></i>
                            CORE DETAILS
                        </h3>

                        <div class="space-y-8">
                            <div class="group">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 block">Organization Slug</label>
                                <div class="text-gray-900 font-bold bg-gray-50 px-4 py-2 rounded-xl group-hover:bg-blue-50 transition-colors">
                                    /{{ $organization->slug ?? '-' }}
                                </div>
                            </div>

                            <div class="group">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 block">Official Contact</label>
                                <div class="text-gray-900 font-bold flex items-center">
                                    <i class="ri-mail-line text-blue-600 mr-2"></i>
                                    {{ $organization->contact_email ?? $organization->email ?? 'no-email@org.com' }}
                                </div>
                            </div>

                            <div class="group">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 block">Hotline</label>
                                <div class="text-gray-900 font-bold flex items-center">
                                    <i class="ri-phone-line text-emerald-600 mr-2"></i>
                                    {{ $organization->contact_phone ?? $organization->phone ?? 'N/A' }}
                                </div>
                            </div>

                            <div class="group">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 block">Established</label>
                                <div class="text-gray-900 font-bold flex items-center">
                                    <i class="ri-calendar-line text-indigo-600 mr-2"></i>
                                    {{ optional($organization->created_at)->format('F d, Y') ?? 'Unknown' }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-10 pt-8 border-t border-gray-100 space-y-4">
                            <a href="{{ $membersUrl }}" class="w-full flex items-center justify-center px-6 py-4 bg-gray-900 text-white rounded-2xl font-bold hover:bg-black shadow-xl shadow-gray-200 transition-all group">
                                <i class="ri-user-settings-line mr-2 group-hover:scale-110 transition-transform"></i>
                                Member Directory
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Primary Content Area -->
                <div class="lg:col-span-3 space-y-10">
                    <!-- Partylists Modern Section -->
                    <div class="bg-white rounded-[40px] shadow-2xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
                        <div class="px-10 py-8 border-b border-gray-50 flex flex-wrap items-center justify-between gap-6">
                            <div>
                                <h2 class="text-2xl font-black text-gray-900 tracking-tight flex items-center">
                                    <i class="ri-flag-2-line mr-3 text-indigo-600 text-3xl"></i>
                                    Registered Partylists
                                </h2>
                                <p class="text-gray-400 font-medium mt-1 uppercase text-[10px] tracking-widest">Active political entities in this organization</p>
                            </div>

                            <a href="{{ $partyCreateUrl }}" class="inline-flex items-center px-8 py-3.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl font-bold shadow-xl shadow-blue-200 hover:shadow-blue-400/50 transform hover:-translate-y-1 transition-all duration-300">
                                <i class="ri-add-circle-line mr-2 text-xl"></i>
                                Register New Party
                            </a>
                        </div>

                        <div class="p-10">
                            @if($partylists->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    @foreach($partylists as $party)
                                        @php
                                            $partyId = $party->id ?? 0;
                                            $partyShow = Route::has('admin.partylists.show') ? route('admin.partylists.show', $partyId) : url('/admin/partylists/'.$partyId);
                                            $partyEdit = Route::has('admin.partylists.edit') ? route('admin.partylists.edit', $partyId) : url('/admin/partylists/'.$partyId.'/edit');
                                            $partyDestroy = Route::has('admin.partylists.destroy') ? route('admin.partylists.destroy', $partyId) : url('/admin/partylists/'.$partyId);
                                        @endphp

                                        <div class="bg-white border border-gray-100 rounded-[32px] p-6 hover:border-indigo-200 hover:shadow-2xl hover:shadow-indigo-100/40 transition-all duration-500 group relative overflow-hidden">
                                            <!-- Brand Accent -->
                                            <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-50 rounded-bl-[100px] -mr-8 -mt-8 transition-all group-hover:bg-indigo-600 group-hover:w-full group-hover:h-full group-hover:rounded-none group-hover:mr-0 group-hover:mt-0 duration-700 opacity-20 group-hover:opacity-5"></div>

                                            <div class="relative z-10">
                                                <div class="flex items-center justify-between mb-6">
                                                    <div class="w-16 h-16 bg-white rounded-2xl shadow-lg border border-gray-50 flex items-center justify-center text-3xl overflow-hidden">
                                                        @if(!empty($party->logo))
                                                            <img src="{{ asset('storage/partylists/' . $party->logo) }}" class="w-full h-full object-cover">
                                                        @else
                                                            <i class="ri-flag-line text-indigo-600"></i>
                                                        @endif
                                                    </div>
                                                    <span class="px-3 py-1 rounded-full text-[10px] font-black tracking-widest uppercase {{ ($party->status ?? '') === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-100 text-gray-600' }}">
                                                        {{ $party->status ?? 'inactive' }}
                                                    </span>
                                                </div>

                                                <h3 class="text-xl font-black text-gray-900 mb-2 group-hover:text-indigo-700 transition-colors">{{ $party->name }}</h3>
                                                <div class="flex items-center text-sm font-bold text-gray-400 uppercase tracking-widest mb-6">
                                                    <i class="ri-price-tag-3-line mr-2"></i>
                                                    {{ $party->acronym ?? 'N/A' }}
                                                </div>

                                                <p class="text-gray-500 text-sm leading-relaxed mb-8 line-clamp-2 h-10 font-medium">
                                                    {{ $party->description ?? 'No specific party agenda or description has been provided yet.' }}
                                                </p>

                                                <div class="flex items-center justify-between pt-6 border-t border-gray-50">
                                                    <div class="flex items-center space-x-4">
                                                        <a href="{{ $partyShow }}" class="text-indigo-600 font-bold text-sm hover:underline">Overview</a>
                                                        <a href="{{ $partyEdit }}" class="text-gray-400 font-bold text-sm hover:text-gray-900">Settings</a>
                                                    </div>
                                                    <button @click="confirmPartyDelete({{ $partyId }}, '{{ addslashes($party->name) }}')" class="w-10 h-10 bg-rose-50 text-rose-500 rounded-xl flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <form action="{{ $partyDestroy }}" method="POST" class="hidden" id="delete-party-{{ $partyId }}">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-24 bg-gray-50/50 rounded-[40px] border-4 border-dashed border-gray-100">
                                    <div class="w-24 h-24 bg-white rounded-[32px] shadow-xl flex items-center justify-center mx-auto mb-8">
                                        <i class="ri-flag-line text-4xl text-gray-200"></i>
                                    </div>
                                    <h3 class="text-2xl font-black text-gray-900 mb-2">Initialize Partylists</h3>
                                    <p class="text-gray-400 font-medium max-w-xs mx-auto mb-10 italic">This organization currently has no registered partylists. Start building your political structure today.</p>
                                    <a href="{{ $partyCreateUrl }}" class="inline-flex items-center px-10 py-4 bg-indigo-600 text-white rounded-[20px] font-black shadow-2xl shadow-indigo-200 hover:bg-indigo-700 transition-all">
                                        <i class="ri-add-line mr-2"></i>
                                        Register First Party
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
