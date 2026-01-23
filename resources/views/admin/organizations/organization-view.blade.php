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
            <!-- Mobile Header -->
            <header class="lg:hidden bg-white border-b px-4 py-4 flex items-center justify-between">
                <button @click="collapsed = false"
                        class="p-2 -ml-2 rounded-lg text-slate-600 hover:bg-slate-100">
                    <i class="ri-menu-2-fill text-xl"></i>
                </button>
                <h1 class="text-lg font-bold text-slate-800">Organization</h1>
                <div class="w-8"></div>
            </header>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-6">
                <a href="{{ $indexUrl }}" class="p-2.5 bg-slate-50 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-2xl transition-all border border-slate-100 hidden sm:block">
                    <i class="ri-arrow-left-line text-lg"></i>
                </a>
                <nav class="hidden md:flex items-center space-x-3">
                    <a href="{{ route('admin.dashboard') }}" class="text-[9px] font-black text-slate-400 hover:text-slate-900 transition-colors uppercase tracking-widest">DASHBOARD</a>
                    <i class="ri-arrow-right-s-line text-slate-300"></i>
                    <a href="{{ $indexUrl }}" class="text-[9px] font-black text-slate-400 hover:text-slate-900 transition-colors uppercase tracking-widest">ORGANIZATIONS</a>
                    <i class="ri-arrow-right-s-line text-slate-300"></i>
                    <span class="text-[9px] font-black text-blue-600 tracking-widest uppercase">{{ Str::limit($organization->name, 25) }}</span>
                </nav>
            </div>

            <div class="flex items-center space-x-2 sm:space-x-3 w-full sm:w-auto">
                <a href="{{ $editUrl }}" class="flex-1 sm:flex-none justify-center inline-flex items-center px-4 sm:px-6 py-2.5 bg-indigo-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all">
                    <i class="ri-edit-line mr-2"></i><span class="hidden xs:inline">Edit Configuration</span><span class="xs:hidden">Edit</span>
                </a>
                <button @click="confirmDelete()" class="sm:flex-none inline-flex items-center px-4 py-2.5 bg-rose-50 text-rose-600 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-rose-100 transition-all border border-rose-100">
                    <i class="ri-delete-bin-line sm:mr-2"></i><span class="hidden sm:inline">Delete</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-8 py-8">
        <!-- Professional Header Card -->
        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/40 border border-slate-200/60 overflow-hidden mb-8">
            <div class="h-24 sm:h-32 bg-gradient-to-r from-blue-700 via-indigo-700 to-purple-800 relative">
                <div class="absolute inset-0 bg-black/10"></div>
                <!-- Decor -->
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-12 -left-12 w-48 h-48 bg-blue-400/20 rounded-full blur-2xl"></div>

                <div class="absolute -bottom-10 sm:-bottom-12 left-6 sm:left-10">
                    <div class="w-24 h-24 sm:w-32 sm:h-32 bg-white rounded-2xl sm:rounded-3xl shadow-2xl border-4 border-white flex items-center justify-center overflow-hidden transform hover:scale-105 transition-transform duration-500">
                        @if(!empty($organization->logo_url))
                            <img src="{{ $organization->logo_url }}" alt="{{ $organization->name }}" class="object-cover w-full h-full"/>
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-blue-50 to-indigo-50 flex items-center justify-center">
                                <i class="ri-building-2-line text-3xl sm:text-4xl text-blue-600"></i>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="pt-16 sm:pt-16 pb-8 px-6 sm:px-10">
                <div class="flex flex-col lg:flex-row items-start justify-between gap-6 sm:gap-8">
                    <div class="flex-1 min-w-0 w-full text-center sm:text-left">
                        <div class="flex flex-col sm:flex-row items-center gap-3 mb-3">
                            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight uppercase truncate w-full sm:w-auto">{{ $organization->name }}</h1>
                            <span class="px-3 py-1 rounded-full text-[9px] font-black tracking-widest uppercase border {{ ($organization->status ?? 'inactive') === 'active' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-rose-50 text-rose-600 border-rose-100' }}">
                                {{ $organization->status ?? 'inactive' }}
                            </span>
                        </div>
                        <p class="text-sm text-slate-500 font-bold leading-relaxed max-w-3xl">
                            {{ $organization->description ?? 'Empowering democracy through structured organizational excellence.' }}
                        </p>
                        <p class="text-[9px] text-slate-400 font-black uppercase tracking-[0.2em] mt-3">Organization ID: ORG-{{ str_pad($organization->id, 4, '0', STR_PAD_LEFT) }}</p>
                    </div>

                    <div class="grid grid-cols-3 gap-2 sm:gap-6 bg-slate-50 p-4 sm:p-5 rounded-2xl border border-slate-100 w-full lg:w-auto">
                        <div class="text-center px-1 sm:px-2">
                            <div class="text-lg sm:text-2xl font-black text-blue-600">{{ number_format($votersCount) }}</div>
                            <div class="text-[8px] sm:text-[9px] font-black text-slate-400 uppercase tracking-widest">Members</div>
                        </div>
                        <div class="w-px h-8 bg-slate-200 my-auto hidden sm:block"></div>
                        <div class="text-center px-1 sm:px-2">
                            <div class="text-lg sm:text-2xl font-black text-emerald-600">{{ number_format($electionsCount) }}</div>
                            <div class="text-[8px] sm:text-[9px] font-black text-slate-400 uppercase tracking-widest">Elections</div>
                        </div>
                        <div class="w-px h-8 bg-slate-200 my-auto hidden sm:block"></div>
                        <div class="text-center px-1 sm:px-2">
                            <div class="text-lg sm:text-2xl font-black text-indigo-600">{{ number_format($partylistsCount) }}</div>
                            <div class="text-[8px] sm:text-[9px] font-black text-slate-400 uppercase tracking-widest">Partylists</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Info Section -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/40 border border-slate-200/60 p-6">
                    <h3 class="text-xs font-black text-slate-900 mb-6 flex items-center uppercase tracking-widest">
                        <i class="ri-information-line mr-2 text-blue-600 text-lg"></i>
                        Core Details
                    </h3>

                    <div class="space-y-6">
                        <div class="group">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 block">Official Email</label>
                            <div class="text-slate-900 font-bold text-xs flex items-center">
                                <i class="ri-mail-line text-blue-600 mr-2"></i>
                                {{ $organization->email ?? 'no-email@org.com' }}
                            </div>
                        </div>

                        <div class="group">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 block">Phone Number</label>
                            <div class="text-slate-900 font-bold text-xs flex items-center">
                                <i class="ri-phone-line text-emerald-600 mr-2"></i>
                                {{ $organization->contact_number ?? 'N/A' }}
                            </div>
                        </div>

                        <div class="group">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 block">Date Established</label>
                            <div class="text-slate-900 font-bold text-xs flex items-center">
                                <i class="ri-calendar-line text-indigo-600 mr-2"></i>
                                {{ optional($organization->created_at)->format('F d, Y') ?? 'Unknown' }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-slate-100 space-y-3">
                        <a href="{{ route('admin.voters.index', ['organization_id' => $organization->id]) }}" class="w-full flex items-center justify-center px-6 py-3.5 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-black shadow-lg shadow-slate-200 transition-all group">
                            <i class="ri-user-settings-line mr-2 group-hover:scale-110 transition-transform"></i>
                            Member Directory
                        </a>
                    </div>
                </div>

                <!-- Recent Activity Mockup (as per image) -->
                <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/40 border border-slate-200/60 p-6">
                    <h3 class="text-xs font-black text-slate-900 mb-6 flex items-center uppercase tracking-widest">
                        <i class="ri-pulse-line mr-2 text-indigo-600 text-lg"></i>
                        Recent Activity
                    </h3>
                    <div class="space-y-5">
                        <div class="relative pl-6 pb-2 border-l-2 border-slate-100">
                            <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-white border-4 border-indigo-500"></div>
                            <p class="text-xs font-black text-slate-800 uppercase tracking-tight">New partylist registered</p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase mt-0.5">2 hours ago</p>
                        </div>
                        <div class="relative pl-6 pb-2 border-l-2 border-slate-100">
                            <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-white border-4 border-blue-500"></div>
                            <p class="text-xs font-black text-slate-800 uppercase tracking-tight">Election created</p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase mt-0.5">1 day ago</p>
                        </div>
                        <div class="relative pl-6">
                            <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-white border-4 border-emerald-500"></div>
                            <p class="text-xs font-black text-slate-800 uppercase tracking-tight">Voter registration opened</p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase mt-0.5">3 days ago</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Primary Content Area -->
            <div class="lg:col-span-3 space-y-8">
                <!-- Partylists Modern Section -->
                <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/40 border border-slate-200/60 overflow-hidden">
                    <div class="px-6 sm:px-8 py-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4 bg-slate-50/30">
                        <div>
                            <h2 class="text-xl font-black text-slate-900 tracking-tight flex items-center uppercase">
                                <i class="ri-flag-2-line mr-3 text-indigo-600 text-2xl"></i>
                                Registered Partylists
                            </h2>
                            <p class="text-slate-400 font-bold mt-0.5 uppercase text-[9px] tracking-widest">Active political entities in this organization</p>
                        </div>

                        <a href="{{ $partyCreateUrl }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-blue-100 hover:shadow-blue-200/50 transform hover:-translate-y-0.5 transition-all duration-300">
                            <i class="ri-add-circle-line mr-2 text-lg"></i>
                            Register Party
                        </a>
                    </div>

                    <div class="p-4 sm:p-8">
                        @if($partylists->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                                @foreach($partylists as $party)
                                    @php
                                        $partyId = $party->id ?? 0;
                                        $partyShow = Route::has('admin.partylists.show') ? route('admin.partylists.show', $partyId) : url('/admin/partylists/'.$partyId);
                                        $partyEdit = Route::has('admin.partylists.edit') ? route('admin.partylists.edit', $partyId) : url('/admin/partylists/'.$partyId.'/edit');
                                        $partyDestroy = Route::has('admin.partylists.destroy') ? route('admin.partylists.destroy', $partyId) : url('/admin/partylists/'.$partyId);
                                    @endphp

                                    <div class="bg-white border border-slate-100 rounded-3xl p-5 hover:border-indigo-200 hover:shadow-xl hover:shadow-indigo-100/30 transition-all duration-500 group relative overflow-hidden">
                                        <div class="relative z-10">
                                            <div class="flex items-center justify-between mb-5">
                                                <div class="w-14 h-14 bg-white rounded-2xl shadow-md border border-slate-50 flex items-center justify-center text-2xl overflow-hidden">
                                                    @if(!empty($party->logo))
                                                        <img src="{{ asset('storage/partylists/' . $party->logo) }}" class="w-full h-full object-cover">
                                                    @else
                                                        <i class="ri-flag-line text-indigo-600"></i>
                                                    @endif
                                                </div>
                                                <span class="px-2.5 py-1 rounded-full text-[8px] font-black tracking-widest uppercase {{ ($party->status ?? '') === 'active' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                                    {{ $party->status ?? 'inactive' }}
                                                </span>
                                            </div>

                                            <h3 class="text-lg font-black text-slate-900 mb-1.5 group-hover:text-indigo-700 transition-colors uppercase tracking-tight truncate w-full">{{ $party->name }}</h3>
                                            <div class="flex items-center text-[10px] font-black text-slate-400 uppercase tracking-widest mb-5">
                                                <i class="ri-price-tag-3-line mr-1.5 text-indigo-400"></i>
                                                {{ $party->acronym ?? 'N/A' }}
                                            </div>

                                            <p class="text-slate-500 text-xs leading-relaxed mb-6 line-clamp-2 h-8 font-bold">
                                                {{ $party->description ?? 'No specific party agenda or description has been provided yet.' }}
                                            </p>

                                            <div class="flex items-center justify-between pt-5 border-t border-slate-50">
                                                <div class="flex items-center space-x-4">
                                                    <a href="{{ $partyShow }}" class="text-indigo-600 font-black text-[10px] uppercase tracking-widest hover:underline">Overview</a>
                                                    <a href="{{ $partyEdit }}" class="text-slate-400 font-black text-[10px] uppercase tracking-widest hover:text-slate-900">Settings</a>
                                                </div>
                                                <button @click="confirmPartyDelete({{ $partyId }}, '{{ addslashes($party->name) }}')" class="w-9 h-9 bg-rose-50 text-rose-500 rounded-xl flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all border border-rose-100">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-16 bg-slate-50/50 rounded-3xl border-2 border-dashed border-slate-200 px-4">
                                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white rounded-2xl shadow-md flex items-center justify-center mx-auto mb-6">
                                    <i class="ri-flag-line text-2xl sm:text-3xl text-slate-200"></i>
                                </div>
                                <h3 class="text-lg sm:text-xl font-black text-slate-900 mb-2 uppercase tracking-tight">Initialize Partylists</h3>
                                <p class="text-slate-400 font-bold max-w-xs mx-auto mb-8 uppercase text-[10px] tracking-widest leading-relaxed">This organization currently has no registered partylists.</p>
                                <a href="{{ $partyCreateUrl }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 bg-indigo-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all">
                                    <i class="ri-add-line mr-2 text-lg"></i>
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
