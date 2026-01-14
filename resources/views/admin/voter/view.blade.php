@extends('layouts.app-main-admin')

@section('content')
    <div class="min-h-screen bg-slate-50 flex flex-col">
        <x-admin-header title="Voter Profile" />

        <!-- Mobile Header -->
        <header class="lg:hidden bg-white shadow-sm border-b px-4 py-3 flex items-center justify-between">
            <button @click="collapsed = false"
                    class="p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                <i class="ri-menu-fold-line text-lg rotate-180"></i>
            </button>
            <h1 class="text-lg font-semibold text-slate-800">Voter Profile</h1>
            <div class="w-10"></div>
        </header>

        <main class="flex-1 p-4 lg:p-8">
            <div class="max-w-7xl mx-auto space-y-6">

                <!-- Profile Header Card -->
                <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-200/40 overflow-hidden">
                    <!-- Gradient Banner -->
                    <div class="h-24 bg-gradient-to-r from-emerald-600 via-teal-500 to-blue-600"></div>

                    <div class="px-6 pb-6">
                        <div class="relative flex flex-col md:flex-row md:items-end gap-5 -mt-10">
                            <!-- Avatar -->
                            <div class="relative">
                                <div class="w-24 h-24 bg-white rounded-2xl p-1.5 shadow-xl">
                                    <div class="w-full h-full bg-slate-100 rounded-xl flex items-center justify-center text-slate-400">
                                        <i class="ri-user-3-line text-4xl"></i>
                                    </div>
                                </div>
                                <div class="absolute -bottom-1 -right-1 w-7 h-7 bg-emerald-500 border-4 border-white rounded-full flex items-center justify-center text-white">
                                    <i class="ri-check-line text-[10px]"></i>
                                </div>
                            </div>

                            <!-- Name & ID -->
                            <div class="flex-1 mb-1">
                                <div class="flex items-center gap-3 flex-wrap">
                                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ $voter->name }}</h1>
                                    <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-600 rounded-lg text-[10px] font-black uppercase tracking-widest flex items-center gap-1.5 border border-emerald-100">
                                        <i class="ri-checkbox-circle-fill"></i>
                                        {{ strtoupper($voter->registration_status) }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 font-bold tracking-tight mt-1">Voter ID: <span class="text-indigo-600">{{ $voter->student_id ?? 'VTR-'.str_pad($voter->id, 4, '0', STR_PAD_LEFT) }}</span></p>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-2 mb-1">
                                <a href="{{ route('admin.voters.index') }}"
                                   class="px-4 py-2.5 bg-slate-100 text-slate-600 rounded-xl font-black text-[10px] tracking-widest hover:bg-slate-200 transition-all uppercase">
                                    Back
                                </a>
                                <a href="{{ route('admin.voters.edit', $voter->id) }}"
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
                                    <p class="text-xs font-bold text-slate-700">{{ $voter->email }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 p-3 bg-slate-50/50 rounded-xl border border-slate-100">
                                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-blue-600 shadow-sm border border-slate-100">
                                    <i class="ri-phone-fill text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Phone Number</p>
                                    <p class="text-xs font-bold text-slate-700">{{ $voter->phone ?? 'Not provided' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 p-3 bg-slate-50/50 rounded-xl border border-slate-100">
                                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-emerald-600 shadow-sm border border-slate-100">
                                    <i class="ri-id-card-fill text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Student ID</p>
                                    <p class="text-xs font-bold text-slate-700">{{ $voter->student_id ?? 'Not provided' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column: Detailed Info & History -->
                    <div class="lg:col-span-2 space-y-6">

                        <!-- Personal Information -->
                        <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-200/40 overflow-hidden">
                            <div class="px-6 py-4 border-b border-slate-50 flex items-center gap-3">
                                <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center text-slate-500">
                                    <i class="ri-user-settings-line text-lg"></i>
                                </div>
                                <h3 class="text-base font-black text-slate-800 uppercase tracking-tight">Personal Information</h3>
                            </div>
                            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-1">
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Full Name</p>
                                    <p class="text-sm font-bold text-slate-700">{{ $voter->name }}</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Registration Date</p>
                                    <div class="flex items-center gap-2 text-slate-700">
                                        <i class="ri-calendar-event-line text-slate-400 text-sm"></i>
                                        <p class="text-sm font-bold">{{ $voter->created_at->format('F d, Y') }}</p>
                                    </div>
                                </div>
                                <div class="space-y-1 md:col-span-2">
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Registered For</p>
                                    <div class="flex items-center gap-2 text-slate-700">
                                        <i class="ri-award-line text-slate-400 text-sm"></i>
                                        <p class="text-sm font-bold">{{ optional($voter->election)->title ?? 'Not assigned to an election' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Voting History -->
                        <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-200/40 overflow-hidden">
                            <div class="px-6 py-4 border-b border-slate-50 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center text-purple-600">
                                        <i class="ri-history-line text-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-black text-slate-800 uppercase tracking-tight">Voting History</h3>
                                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Past elections participated</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6 space-y-3">
                                @php
                                    $votes = \App\Models\Vote::where('voter_id', $voter->id)->with('election')->get();
                                @endphp
                                @forelse($votes as $vote)
                                    <div class="flex items-center justify-between p-3.5 bg-slate-50/50 rounded-xl border border-slate-100 hover:border-indigo-100 transition-colors group">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 bg-white rounded-lg flex items-center justify-center text-purple-600 shadow-sm border border-purple-100 group-hover:bg-purple-600 group-hover:text-white transition-all">
                                                <i class="ri-ballot-fill"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold text-slate-700">{{ optional($vote->election)->title ?? 'Election #'.$vote->election_id }}</p>
                                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Voted on {{ $vote->created_at->format('M d, Y') }}</p>
                                            </div>
                                        </div>
                                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-600 rounded-lg text-[9px] font-black uppercase tracking-widest flex items-center gap-1.5 border border-emerald-100">
                                            <i class="ri-checkbox-circle-fill"></i>
                                            Voted
                                        </span>
                                    </div>
                                @empty
                                    <div class="text-center py-8 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                                        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300 shadow-sm">
                                            <i class="ri-history-line text-2xl"></i>
                                        </div>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">No voting history found</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Stats & Actions -->
                    <div class="space-y-6">
                        <!-- Participation Stats -->
                        <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-200/40 p-6">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-9 h-9 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600">
                                    <i class="ri-line-chart-line text-lg"></i>
                                </div>
                                <h3 class="text-base font-black text-slate-800 uppercase tracking-tight">Participation Stats</h3>
                            </div>

                            <div class="relative flex flex-col items-center justify-center py-4 mb-6 bg-indigo-50/30 rounded-2xl border border-indigo-50">
                                <span class="text-3xl font-black text-indigo-600">100%</span>
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-1">Participation Rate</span>
                            </div>

                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-3 bg-emerald-50/50 rounded-xl border border-emerald-100">
                                    <span class="text-xs font-bold text-slate-700">Elections Voted</span>
                                    <span class="text-lg font-black text-emerald-600">{{ $votes->count() }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-blue-50/50 rounded-xl border border-blue-100">
                                    <span class="text-xs font-bold text-slate-700">Eligible Elections</span>
                                    <span class="text-lg font-black text-blue-600">1</span>
                                </div>
                            </div>
                        </div>

                        <!-- Verification Status -->
                        <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-200/40 p-6">
                            <div class="flex items-center gap-3 mb-5">
                                <div class="w-9 h-9 bg-emerald-50 rounded-lg flex items-center justify-center text-emerald-600">
                                    <i class="ri-shield-check-line text-lg"></i>
                                </div>
                                <h3 class="text-base font-black text-slate-800 uppercase tracking-tight">Verification</h3>
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center gap-2.5 p-2.5 bg-slate-50 rounded-xl">
                                    <i class="ri-checkbox-circle-fill text-emerald-500 text-base"></i>
                                    <span class="text-[11px] font-bold text-slate-600 uppercase tracking-tight">Identity Verified</span>
                                </div>
                                <div class="flex items-center gap-2.5 p-2.5 bg-slate-50 rounded-xl">
                                    <i class="ri-checkbox-circle-fill text-emerald-500 text-base"></i>
                                    <span class="text-[11px] font-bold text-slate-600 uppercase tracking-tight">Email Verified</span>
                                </div>
                                <div class="flex items-center gap-2.5 p-2.5 bg-slate-50 rounded-xl">
                                    <i class="ri-checkbox-circle-fill text-emerald-500 text-base"></i>
                                    <span class="text-[11px] font-bold text-slate-600 uppercase tracking-tight">Eligible to Vote</span>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-200/40 p-6">
                            <h3 class="text-base font-black text-slate-800 uppercase tracking-tight mb-5">Quick Actions</h3>
                            <div class="space-y-2.5">
                                @if($voter->registration_status !== 'approved')
                                    <form method="POST" action="{{ route('admin.voters.approve', $voter->id) }}">
                                        @csrf
                                        <button type="submit" class="w-full py-3 bg-emerald-600 text-white rounded-xl font-black text-[10px] tracking-widest hover:bg-emerald-700 transition-all uppercase flex items-center justify-center gap-2 shadow-lg shadow-emerald-100">
                                            <i class="ri-check-line"></i> Approve Voter
                                        </button>
                                    </form>
                                @endif

                                <button class="w-full py-3 bg-slate-50 text-slate-700 rounded-xl font-black text-[10px] tracking-widest hover:bg-slate-100 transition-all uppercase border border-slate-100">
                                    Send Notification
                                </button>

                                @if($voter->registration_status !== 'declined')
                                    <form method="POST" action="{{ route('admin.voters.decline', $voter->id) }}">
                                        @csrf
                                        <button type="submit" class="w-full py-3 bg-white text-rose-500 rounded-xl font-black text-[10px] tracking-widest hover:bg-rose-50 transition-all uppercase border border-rose-100 mt-1">
                                            Revoke Access
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection
