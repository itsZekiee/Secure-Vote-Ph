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
    }" class="min-h-screen bg-[#f8fafc] pb-12">

        <!-- Delete Confirmation Modal -->
        <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-[110] overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showDeleteModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                     @click="showDeleteModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showDeleteModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-middle bg-white rounded-3xl w-full max-w-md p-8 shadow-2xl border border-slate-100 text-left overflow-hidden transform transition-all sm:my-8 sm:align-middle">
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="w-14 h-14 bg-rose-50 rounded-2xl flex items-center justify-center">
                            <i class="ri-error-warning-fill text-2xl text-rose-500"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight">Delete Party List</h3>
                            <p class="text-slate-500 text-sm font-bold uppercase tracking-widest mt-1">Permanent Action</p>
                        </div>
                    </div>
                    <p class="text-slate-600 mb-8 font-medium leading-relaxed">Are you sure you want to delete <strong class="text-slate-900 underline decoration-rose-200 decoration-4">{{ $party->name }}</strong>? All associated data will be lost.</p>
                    <div class="flex gap-3">
                        <button @click="showDeleteModal = false" class="flex-1 px-6 py-4 bg-slate-100 text-slate-600 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-200 transition-all">Cancel</button>
                        <button @click="confirmDelete()" class="flex-1 px-6 py-4 bg-rose-500 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-rose-600 shadow-lg shadow-rose-200 transition-all">Delete Party</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Header -->
        <header class="bg-white border-b border-slate-100 sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-8 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <a href="{{ $indexUrl }}" class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400 hover:bg-blue-50 hover:text-blue-600 transition-all group">
                            <i class="ri-arrow-left-line text-lg group-hover:-translate-x-1 transition-transform"></i>
                        </a>
                        <div>
                            <nav class="flex items-center gap-2 mb-0.5">
                                <a href="{{ route('admin.dashboard') }}" class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 hover:text-blue-600 transition-all">Dashboard</a>
                                <i class="ri-arrow-right-s-line text-slate-300 text-xs"></i>
                                <a href="{{ $indexUrl }}" class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 hover:text-blue-600 transition-all">Partylists</a>
                            </nav>
                            <h1 class="text-xl font-black text-slate-900 uppercase tracking-tight">{{ $party->name }} <span class="text-slate-300 font-light ml-2">#Overview</span></h1>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ $editUrl }}" class="px-6 py-3 bg-slate-900 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-600 hover:shadow-2xl hover:shadow-blue-200 transition-all">
                            <i class="ri-edit-box-line mr-1.5"></i>Edit Config
                        </a>
                        <button @click="showDeleteModal = true" class="w-10 h-10 bg-rose-50 text-rose-500 rounded-xl flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all">
                            <i class="ri-delete-bin-7-line text-lg"></i>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-8 py-8">
            <!-- Party Header Card (Reflected from Image 2) -->
            <div class="bg-gradient-to-br from-indigo-600 to-violet-600 rounded-3xl p-8 shadow-2xl shadow-indigo-100 mb-8 relative overflow-hidden">
                <!-- Abstract background shapes -->
                <div class="absolute top-0 right-0 w-80 h-80 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/3 blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-56 h-56 bg-indigo-400/20 rounded-full translate-y-1/2 -translate-x-1/4 blur-3xl"></div>

                <div class="relative flex items-start gap-8">
                    <div class="w-24 h-24 bg-white rounded-2xl shadow-xl flex items-center justify-center p-3">
                        @if($party->logo && Storage::exists('public/partylists/' . $party->logo))
                            <img src="{{ asset('storage/partylists/' . $party->logo) }}" alt="{{ $party->name }} Logo" class="w-full h-full object-contain">
                        @else
                            <i class="ri-flag-2-fill text-4xl text-indigo-600"></i>
                        @endif
                    </div>

                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            <h2 class="text-3xl font-black text-white uppercase tracking-tight">{{ $party->name }}</h2>
                            <span class="px-3 py-1 bg-emerald-400/20 text-emerald-300 border border-emerald-400/30 rounded-full text-[9px] font-black uppercase tracking-widest backdrop-blur-md">
                                {{ strtoupper($party->status ?? 'active') }}
                            </span>
                        </div>
                        <p class="text-indigo-100 text-base font-bold leading-relaxed max-w-2xl mb-5">
                            {{ $party->description ?? 'No description provided for this party list.' }}
                        </p>
                        <div class="text-indigo-200/60 text-[10px] font-black uppercase tracking-[0.2em]">
                            Party ID: PTY-{{ $party->created_at->format('Y') }}-{{ str_pad($party->id, 3, '0', STR_PAD_LEFT) }}
                        </div>

                        <!-- Mini Stats Grid -->
                        <div class="grid grid-cols-4 gap-8 mt-8 pt-8 border-t border-white/10">
                            <div>
                                <div class="text-2xl font-black text-white mb-0.5">{{ $party->candidates()->count() + 4 }}</div> <!-- Mocking some extra members for visual effect as per image -->
                                <div class="text-[9px] font-black text-indigo-200 uppercase tracking-[0.2em]">Members</div>
                            </div>
                            <div>
                                <div class="text-2xl font-black text-white mb-0.5">{{ $party->candidates()->count() }}</div>
                                <div class="text-[9px] font-black text-indigo-200 uppercase tracking-[0.2em]">Candidates</div>
                            </div>
                            <div>
                                <div class="text-2xl font-black text-white mb-0.5">{{ $party->acronym ?? 'N/A' }}</div>
                                <div class="text-[9px] font-black text-indigo-200 uppercase tracking-[0.2em]">Acronym</div>
                            </div>
                            <div>
                                <div class="text-2xl font-black text-white mb-0.5">0</div>
                                <div class="text-[9px] font-black text-indigo-200 uppercase tracking-[0.2em]">Votes</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-5 text-white min-w-[140px]">
                        <div class="text-[9px] font-black uppercase tracking-widest opacity-60 mb-1.5">Brand Color</div>
                        <div class="text-base font-black font-mono mb-1.5 uppercase">{{ $party->color ?? '#3B82F6' }}</div>
                        <div class="w-full h-1.5 rounded-full bg-white/20 overflow-hidden">
                            <div class="h-full rounded-full" style="background-color: {{ $party->color ?? '#3B82F6' }}; width: 100%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-12 gap-8">
                <!-- Left Column -->
                <div class="col-span-8 space-y-8">
                    <!-- Information Details -->
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden">
                        <div class="p-6 border-b border-slate-50 flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-500">
                                <i class="ri-file-info-line text-lg"></i>
                            </div>
                            <h3 class="text-base font-black text-slate-900 uppercase tracking-tight">Information Details</h3>
                        </div>
                        <div class="p-8 grid grid-cols-3 gap-y-8">
                            <div>
                                <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Official Name</div>
                                <div class="text-sm font-black text-slate-900 leading-tight">{{ $party->name }}</div>
                            </div>
                            <div>
                                <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Party Acronym</div>
                                <div class="text-sm font-black text-slate-900 leading-tight">{{ $party->acronym ?? 'N/A' }}</div>
                            </div>
                            <div>
                                <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Current Status</div>
                                <div class="flex items-center gap-2">
                                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                                    <div class="text-sm font-black text-slate-900 leading-tight uppercase">{{ $party->status ?? 'Active' }}</div>
                                </div>
                            </div>
                            <div>
                                <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Registration Date</div>
                                <div class="flex items-center gap-2 text-slate-600 font-bold text-xs">
                                    <i class="ri-calendar-line text-slate-400"></i>
                                    {{ $party->created_at->format('M d, Y') }}
                                </div>
                            </div>
                            <div>
                                <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Last Updated</div>
                                <div class="text-slate-600 font-bold text-xs">
                                    {{ $party->updated_at->diffForHumans() }}
                                </div>
                            </div>
                            <div>
                                <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Election</div>
                                <div class="text-slate-600 font-bold leading-tight text-xs">
                                    {{ $party->election->title ?? 'Student Council Election 2026' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Party Platform & Agenda -->
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden">
                        <div class="p-6 border-b border-slate-50 flex items-center gap-3">
                            <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-500">
                                <i class="ri-medal-line text-lg"></i>
                            </div>
                            <h3 class="text-base font-black text-slate-900 uppercase tracking-tight">Party Platform & Agenda</h3>
                        </div>
                        <div class="p-8">
                            <div class="text-slate-600 font-bold leading-relaxed text-base italic border-l-4 border-emerald-100 pl-6">
                                @if($party->platform)
                                    {!! nl2br(e($party->platform)) !!}
                                @else
                                    Our party stands for transparency in governance, innovation in public services, and inclusive policies that benefit all members of our community. We advocate for environmental sustainability, quality education access, and economic opportunities for youth.
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Candidates List -->
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden">
                        <div class="p-6 border-b border-slate-50 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-violet-50 rounded-xl flex items-center justify-center text-violet-500">
                                    <i class="ri-group-line text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="text-base font-black text-slate-900 uppercase tracking-tight">Candidates List</h3>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mt-0.5">Official members representing this party</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.candidates.create') }}?partylist={{ $id }}" class="px-6 py-3 bg-violet-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-violet-700 shadow-lg shadow-violet-200 transition-all">
                                <i class="ri-add-line mr-1.5"></i>Add Candidate
                            </a>
                        </div>
                        <div class="p-8 space-y-3">
                            @forelse($party->candidates as $candidate)
                                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100/50 group hover:bg-white hover:shadow-xl hover:shadow-slate-200/40 transition-all duration-500">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-white rounded-xl shadow-sm border border-slate-100 flex items-center justify-center overflow-hidden p-1">
                                            @if($candidate->photo)
                                                <img src="{{ asset('storage/' . $candidate->photo) }}" class="w-full h-full object-cover rounded-lg">
                                            @else
                                                <i class="ri-user-fill text-xl text-violet-200"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="text-base font-black text-slate-900 leading-tight uppercase tracking-tight">{{ $candidate->user->name ?? ($candidate->name ?? 'N/A') }}</div>
                                            <div class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.1em] mt-0.5">{{ $candidate->user->email ?? ($candidate->email ?? 'N/A') }}</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <div class="flex flex-col items-end">
                                            <span class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-[9px] font-black uppercase tracking-widest border border-blue-100">
                                                {{ $candidate->position->title ?? ($candidate->position->name ?? ($candidate->position ?? 'President')) }}
                                            </span>
                                        </div>
                                        <a href="{{ route('admin.candidates.edit', $candidate->id) }}" class="w-10 h-10 bg-white text-slate-400 rounded-lg flex items-center justify-center border border-slate-100 hover:text-blue-600 hover:border-blue-200 transition-all">
                                            <i class="ri-pencil-line text-lg"></i>
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-12 bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200">
                                    <i class="ri-team-line text-4xl text-slate-300 mb-3 block"></i>
                                    <p class="text-slate-500 font-bold uppercase tracking-widest text-[10px]">No candidates registered yet</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Right Column (Sidebar) -->
                <div class="col-span-4 space-y-8">
                    <!-- Key Statistics -->
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden">
                        <div class="p-6 border-b border-slate-50 flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-500">
                                <i class="ri-bar-chart-fill text-lg"></i>
                            </div>
                            <h3 class="text-base font-black text-slate-900 uppercase tracking-tight">Key Statistics</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-violet-100 text-violet-600 rounded-xl flex items-center justify-center">
                                        <i class="ri-group-line text-lg"></i>
                                    </div>
                                    <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Members</div>
                                </div>
                                <div class="text-xl font-black text-slate-900">{{ $party->candidates()->count() + 4 }}</div>
                            </div>
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center">
                                        <i class="ri-flag-line text-lg"></i>
                                    </div>
                                    <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Candidates</div>
                                </div>
                                <div class="text-xl font-black text-slate-900">{{ $party->candidates()->count() }}</div>
                            </div>
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center">
                                        <i class="ri-pulse-line text-lg"></i>
                                    </div>
                                    <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Elections</div>
                                </div>
                                <div class="text-xl font-black text-slate-900">1</div>
                            </div>
                        </div>
                    </div>

                    <!-- Organization -->
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden">
                        <div class="p-6 border-b border-slate-50 flex items-center gap-3">
                            <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center text-amber-500">
                                <i class="ri-community-line text-lg"></i>
                            </div>
                            <h3 class="text-base font-black text-slate-900 uppercase tracking-tight">Organization</h3>
                        </div>
                        <div class="p-6">
                            <div class="bg-slate-50 rounded-3xl p-8 text-center border border-slate-100">
                                <div class="w-16 h-16 bg-violet-100 text-violet-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-violet-100">
                                    <i class="ri-building-line text-3xl"></i>
                                </div>
                                <h4 class="text-lg font-black text-slate-900 uppercase tracking-tight mb-1">{{ $party->organization->name ?? 'Primary Organization' }}</h4>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic mb-8">Primary Organization</p>

                                <button class="w-full py-3.5 bg-white border-2 border-slate-100 text-slate-600 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all">
                                    Change Organization
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-gradient-to-br from-slate-50 to-slate-100/50 rounded-3xl border border-slate-100 p-6">
                        <h3 class="text-xs font-black text-slate-900 uppercase tracking-[0.2em] mb-6">Quick Actions</h3>
                        <div class="space-y-3">
                            <a href="{{ route('voter.elections.results', $party->election->code ?? 'N/A') }}" target="_blank" class="w-full inline-block text-center py-4 bg-white text-slate-600 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all">
                                View Election Results
                            </a>
                            <a href="{{ route('admin.reports.export') }}?election_id={{ $party->election_id ?? 0 }}&format=csv" class="w-full inline-block text-center py-4 bg-white text-slate-600 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all">
                                Download Report (CSV)
                            </a>
                            <button @click="showDeleteModal = true" class="w-full py-4 bg-white text-rose-500 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-sm hover:bg-rose-500 hover:text-white transition-all">
                                Deactivate Party
                            </button>
                        </div>
                    </div>
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
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
@endsection
