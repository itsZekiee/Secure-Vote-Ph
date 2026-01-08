@extends('layouts.app-main-admin')

@section('content')
<div class="min-h-screen bg-slate-50 flex flex-col">
    <x-admin-header title="Candidate Profile" />

    <!-- Mobile Header -->
    <header class="lg:hidden bg-white shadow-sm border-b px-4 py-3 flex items-center justify-between">
        <button @click="collapsed = false"
                class="p-2 rounded-lg text-slate-600 hover:bg-slate-100">
            <i class="ri-menu-fold-line text-lg rotate-180"></i>
        </button>
        <h1 class="text-lg font-semibold text-slate-800">Candidate Profile</h1>
        <div class="w-10"></div>
    </header>

    <main class="flex-1 p-4 lg:p-8">
        <div class="max-w-7xl mx-auto space-y-6">

            <!-- Profile Header Card -->
            <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-200/40 overflow-hidden">
                <!-- Gradient Banner -->
                <div class="h-24 bg-gradient-to-r from-indigo-600 via-blue-500 to-indigo-600"></div>

                <div class="px-6 pb-6">
                    <div class="relative flex flex-col md:flex-row md:items-end gap-5 -mt-10">
                        <!-- Avatar -->
                        <div class="relative">
                            <div class="w-24 h-24 bg-white rounded-2xl p-1.5 shadow-xl">
                                <div class="w-full h-full bg-slate-100 rounded-xl flex items-center justify-center text-slate-400 overflow-hidden">
                                    @if($candidate->photo)
                                        <img src="{{ asset('storage/' . $candidate->photo) }}" alt="{{ $candidate->name }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="ri-user-3-line text-4xl"></i>
                                    @endif
                                </div>
                            </div>
                            @if($candidate->status === 'active')
                                <div class="absolute -bottom-1 -right-1 w-7 h-7 bg-emerald-500 border-4 border-white rounded-full flex items-center justify-center text-white">
                                    <i class="ri-check-line text-[10px]"></i>
                                </div>
                            @endif
                        </div>

                        <!-- Name & Position -->
                        <div class="flex-1 mb-1">
                            <div class="flex items-center gap-3 flex-wrap">
                                <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ $candidate->name }}</h1>
                                <span class="px-2.5 py-0.5 bg-indigo-50 text-indigo-600 rounded-lg text-[10px] font-black uppercase tracking-widest flex items-center gap-1.5 border border-indigo-100">
                                    <i class="ri-shield-user-fill"></i>
                                    {{ $candidate->position ? $candidate->position->title : 'No Position' }}
                                </span>
                                @if($candidate->partylist)
                                    <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-600 rounded-lg text-[10px] font-black uppercase tracking-widest flex items-center gap-1.5 border border-emerald-100">
                                        <i class="ri-flag-fill"></i>
                                        {{ $candidate->partylist->name }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 font-bold tracking-tight mt-1">Candidate ID: <span class="text-indigo-600">CND-{{ str_pad($candidate->id, 4, '0', STR_PAD_LEFT) }}</span></p>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-2 mb-1">
                            <a href="{{ route('admin.candidates.index') }}"
                               class="px-4 py-2.5 bg-slate-100 text-slate-600 rounded-xl font-black text-[10px] tracking-widest hover:bg-slate-200 transition-all uppercase">
                                Back
                            </a>
                            <a href="{{ route('admin.candidates.edit', $candidate->id) }}"
                               class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl font-black text-[10px] tracking-widest hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all uppercase flex items-center gap-2">
                                <i class="ri-edit-line"></i>
                                Edit Profile
                            </a>
                        </div>
                    </div>

                    <!-- Quick Info Bar -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8 pt-6 border-t border-slate-100">
                        <div class="flex items-center gap-3 p-3 bg-slate-50/50 rounded-xl border border-slate-100">
                            <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-indigo-600 shadow-sm border border-slate-100">
                                <i class="ri-mail-fill text-lg"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Email Address</p>
                                <p class="text-xs font-bold text-slate-700">{{ $candidate->user ? $candidate->user->email : 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-slate-50/50 rounded-xl border border-slate-100">
                            <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-blue-600 shadow-sm border border-slate-100">
                                <i class="ri-calendar-event-fill text-lg"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Joined At</p>
                                <p class="text-xs font-bold text-slate-700">{{ $candidate->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-slate-50/50 rounded-xl border border-slate-100">
                            <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-emerald-600 shadow-sm border border-slate-100">
                                <i class="ri-checkbox-circle-fill text-lg"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Status</p>
                                <p class="text-xs font-bold text-slate-700 uppercase">{{ $candidate->status ?? 'Inactive' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column: Detailed Info & Platform -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Campaign Platform -->
                    <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-200/40 overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-50 flex items-center gap-3">
                            <div class="w-8 h-8 bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-600">
                                <i class="ri-megaphone-line text-lg"></i>
                            </div>
                            <h3 class="text-base font-black text-slate-800 uppercase tracking-tight">Campaign Platform</h3>
                        </div>
                        <div class="p-8">
                            <div class="prose prose-slate max-w-none">
                                <p class="text-sm font-bold text-slate-600 leading-relaxed italic border-l-4 border-indigo-100 pl-6">
                                    @if($candidate->platform)
                                        {!! nl2br(e($candidate->platform)) !!}
                                    @else
                                        No platform statement provided for this candidate.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Election Details -->
                    <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-200/40 overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-50 flex items-center gap-3">
                            <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600">
                                <i class="ri-government-line text-lg"></i>
                            </div>
                            <h3 class="text-base font-black text-slate-800 uppercase tracking-tight">Election Assignment</h3>
                        </div>
                        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Current Election</p>
                                <div class="flex items-center gap-2 text-slate-700">
                                    <i class="ri-award-line text-slate-400 text-sm"></i>
                                    <p class="text-sm font-bold">{{ $candidate->election ? $candidate->election->title : 'Not Assigned' }}</p>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Organization</p>
                                <div class="flex items-center gap-2 text-slate-700">
                                    <i class="ri-community-line text-slate-400 text-sm"></i>
                                    <p class="text-sm font-bold">{{ $candidate->organization ? $candidate->organization->name : 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Stats & Meta -->
                <div class="space-y-6">
                    <!-- Integrity Card -->
                    <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-200/40 p-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-9 h-9 bg-emerald-50 rounded-lg flex items-center justify-center text-emerald-600">
                                <i class="ri-shield-check-line text-lg"></i>
                            </div>
                            <h3 class="text-base font-black text-slate-800 uppercase tracking-tight">Integrity Check</h3>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Profile Completion</span>
                                <span class="text-lg font-black text-indigo-600">95%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-indigo-600 h-full rounded-full" style="width: 95%"></div>
                            </div>
                            <p class="text-[10px] text-slate-400 font-bold uppercase leading-relaxed tracking-wider">Profile is verified and ready for the digital ballot.</p>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-200/40 p-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-9 h-9 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600">
                                <i class="ri-bar-chart-fill text-lg"></i>
                            </div>
                            <h3 class="text-base font-black text-slate-800 uppercase tracking-tight">Quick Stats</h3>
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <span class="text-xs font-bold text-slate-700 uppercase tracking-tight">Total Votes</span>
                                <span class="text-lg font-black text-indigo-600">0</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <span class="text-xs font-bold text-slate-700 uppercase tracking-tight">Rank</span>
                                <span class="text-lg font-black text-blue-600">N/A</span>
                            </div>
                        </div>
                    </div>

                    <!-- Pro Tip -->
                    <div class="bg-gradient-to-br from-indigo-600 to-blue-600 rounded-3xl p-6 text-white shadow-xl shadow-indigo-200">
                        <h4 class="text-base font-black uppercase tracking-tight mb-3 flex items-center gap-2">
                            <i class="ri-lightbulb-line"></i> Ballot Excellence
                        </h4>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-2.5">
                                <i class="ri-checkbox-circle-line text-emerald-400 mt-0.5"></i>
                                <p class="text-[11px] font-bold text-indigo-50 leading-relaxed uppercase tracking-wide">Studio portraits improve voter trust</p>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <i class="ri-checkbox-circle-line text-emerald-400 mt-0.5"></i>
                                <p class="text-[11px] font-bold text-indigo-50 leading-relaxed uppercase tracking-wide">Platform clarity increases engagement</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
