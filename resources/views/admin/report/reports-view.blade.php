@extends('layouts.app-main-admin')

@section('content')
<div class="flex min-h-screen bg-slate-50">
    <x-admin-sidebar />

    <main class="flex-1 min-h-screen">
        <x-admin-header title="Election Analytics & Results" />

        <div class="p-4 lg:p-8">
            <div class="max-w-7xl mx-auto">
                <!-- Navigation & Breadcrumbs -->
                <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <nav class="flex items-center gap-2 text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">
                            <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-600 transition-colors">Dashboard</a>
                            <i class="ri-arrow-right-s-line text-xs"></i>
                            <a href="{{ route('admin.reports.index') }}" class="hover:text-indigo-600 transition-colors">Reports</a>
                            <i class="ri-arrow-right-s-line text-xs"></i>
                            <span class="text-slate-900">View Analytics</span>
                        </nav>
                        <h1 class="text-3xl font-black text-slate-900 tracking-tight uppercase">{{ $election->title }}</h1>
                    </div>
                    <div class="flex items-center gap-3">
                        <button onclick="window.print()"
                                class="px-5 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-50 transition-all flex items-center gap-2 shadow-sm">
                            <i class="ri-printer-line text-lg"></i>
                            Print
                        </button>
                        <a href="{{ route('admin.reports.export', ['form_id' => $election->id, 'format' => 'xlsx']) }}"
                           class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all flex items-center gap-2">
                            <i class="ri-file-excel-2-line text-lg"></i>
                            Export Data
                        </a>
                    </div>
                </div>

                <!-- Election Profile Card -->
                <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-200/40 overflow-hidden mb-8">
                    <div class="grid grid-cols-1 lg:grid-cols-3">
                        <div class="p-8 lg:p-10 lg:col-span-2 border-b lg:border-b-0 lg:border-r border-slate-100">
                            <div class="flex items-center gap-6 mb-8">
                                <div class="w-16 h-16 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 shadow-inner">
                                    <i class="ri-building-line text-3xl"></i>
                                </div>
                                <div>
                                    <h2 class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Organization</h2>
                                    <p class="text-xl font-black text-slate-900 uppercase tracking-tight">{{ optional($election->organization)->name ?? 'Independent Election' }}</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                                <div>
                                    <h3 class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Duration</h3>
                                    <div class="flex items-center gap-3 text-slate-700">
                                        <i class="ri-calendar-event-line text-indigo-500"></i>
                                        <span class="text-xs font-bold uppercase tracking-wide">{{ optional($election->start_date)->format('M d, Y') }} - {{ optional($election->end_date)->format('M d, Y') }}</span>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Election Code</h3>
                                    <div class="flex items-center gap-3">
                                        <span class="px-4 py-1.5 bg-slate-50 rounded-lg font-mono font-black text-xs text-slate-700 border border-slate-200 uppercase tracking-widest">{{ $election->code }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-8 lg:p-10 bg-slate-50/30 flex flex-col justify-center">
                            <h3 class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-6 text-right">Engagement Overview</h3>
                            <div class="space-y-4">
                                <div class="text-right">
                                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Voter Turnout</p>
                                    <p class="text-5xl font-black text-indigo-600 leading-none">
                                        {{ $totalRegistered > 0 ? round(($turnoutCount / $totalRegistered) * 100, 1) : 0 }}%
                                    </p>
                                </div>
                                <div class="flex flex-col items-end">
                                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-2 text-right">
                                        {{ number_format($turnoutCount) }} participated out of <br>
                                        {{ number_format($totalRegistered) }} total registered voters
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-200/40 transition-all hover:shadow-2xl hover:shadow-slate-200/50 group">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center transition-transform group-hover:scale-110 shadow-sm">
                                <i class="ri-checkbox-circle-line text-xl"></i>
                            </div>
                            <h3 class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Total Votes Cast</h3>
                        </div>
                        <div class="text-3xl font-black text-slate-900 tracking-tight">{{ number_format($totalVotes) }}</div>
                        <p class="text-[9px] text-slate-400 mt-2 font-bold uppercase tracking-widest">Accumulated across all positions</p>
                    </div>

                    <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-200/40 transition-all hover:shadow-2xl hover:shadow-slate-200/50 group">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center transition-transform group-hover:scale-110 shadow-sm">
                                <i class="ri-scales-3-line text-xl"></i>
                            </div>
                            <h3 class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Election Scale</h3>
                        </div>
                        <div class="text-3xl font-black text-slate-900 tracking-tight">{{ $election->positions->count() }}</div>
                        <p class="text-[9px] text-slate-400 mt-2 font-bold uppercase tracking-widest">{{ $election->positions->count() }} position categories and positions</p>
                    </div>

                    <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-200/40 transition-all hover:shadow-2xl hover:shadow-slate-200/50 group">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center transition-transform group-hover:scale-110 shadow-sm">
                                <i class="ri-user-star-line text-xl"></i>
                            </div>
                            <h3 class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Candidates</h3>
                        </div>
                        <div class="text-3xl font-black text-slate-900 tracking-tight">{{ $election->positions->pluck('candidates')->collapse()->count() }}</div>
                        <p class="text-[9px] text-slate-400 mt-2 font-bold uppercase tracking-widest">{{ $election->positions->pluck('candidates')->collapse()->count() }} active participants</p>
                    </div>
                </div>

                <!-- Results Section -->
                <div class="mb-12">
                    <h2 class="text-xl font-black text-slate-900 uppercase tracking-tight mb-8 flex items-center gap-3 ml-2">
                        <i class="ri-bar-chart-2-fill text-indigo-600"></i>
                        Detailed Results per Position
                    </h2>

                    <div class="grid grid-cols-1 gap-10">
                        @foreach($election->positions as $position)
                            <div class="bg-white rounded-3xl border border-slate-200/60 overflow-hidden shadow-xl shadow-slate-200/40 transition-all hover:shadow-2xl">
                                <div class="px-8 py-6 bg-slate-50 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-100">
                                            <i class="ri-briefcase-line text-lg"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight">{{ $position->title }}</h3>
                                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Position Category</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="px-4 py-1.5 bg-white border border-slate-200 rounded-xl text-[9px] font-black text-slate-600 shadow-sm uppercase tracking-widest">
                                            {{ number_format($position->candidates->sum('votes_count')) }} Total Votes Cast
                                        </span>
                                    </div>
                                </div>

                                <div class="p-8">
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                                        <!-- Left Side: Vote Distribution (Mock Chart Area) -->
                                        <div>
                                            <h4 class="text-[10px] font-black text-slate-900 uppercase tracking-widest mb-6">Vote Distribution</h4>
                                            <div class="h-48 flex items-end justify-around gap-2 bg-slate-50/50 rounded-2xl p-6 border border-slate-100 relative">
                                                <!-- Mock Grid Lines -->
                                                <div class="absolute inset-0 p-6 flex flex-col justify-between opacity-20 pointer-events-none">
                                                    <div class="border-t border-slate-300 w-full"></div>
                                                    <div class="border-t border-slate-300 w-full"></div>
                                                    <div class="border-t border-slate-300 w-full"></div>
                                                </div>

                                                @foreach($position->candidates->sortByDesc('votes_count')->take(4) as $candidate)
                                                    @php
                                                        $posTotalVotes = $position->candidates->sum('votes_count');
                                                        $percentage = $posTotalVotes > 0 ? ($candidate->votes_count / $posTotalVotes) * 100 : 0;
                                                        $colors = ['bg-indigo-500', 'bg-blue-500', 'bg-emerald-500', 'bg-amber-500'];
                                                        $color = $colors[$loop->index % count($colors)];
                                                    @endphp
                                                    <div class="relative group flex flex-col items-center flex-1 max-w-[60px]">
                                                        <div class="{{ $color }} w-full rounded-t-lg transition-all duration-1000 group-hover:brightness-110 shadow-lg shadow-slate-200"
                                                             style="height: {{ max($percentage, 5) }}%"></div>
                                                        <div class="mt-3 text-[8px] font-black text-slate-400 uppercase tracking-tighter truncate w-full text-center">
                                                            {{ explode(' ', $candidate->name)[0] }}
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <!-- Right Side: Candidate Rankings -->
                                        <div>
                                            <h4 class="text-[10px] font-black text-slate-900 uppercase tracking-widest mb-6">Candidate Rankings</h4>
                                            <div class="space-y-6">
                                                @foreach($position->candidates->sortByDesc('votes_count') as $candidate)
                                                    @php
                                                        $posTotalVotes = $position->candidates->sum('votes_count');
                                                        $percentage = $posTotalVotes > 0 ? round(($candidate->votes_count / $posTotalVotes) * 100, 1) : 0;
                                                        $isWinner = $loop->first && $candidate->votes_count > 0;
                                                    @endphp
                                                    <div class="relative group">
                                                        <div class="flex items-center justify-between mb-2">
                                                            <div class="flex items-center gap-4">
                                                                <div class="w-7 h-7 {{ $isWinner ? 'bg-amber-100 text-amber-600' : 'bg-slate-100 text-slate-500' }} rounded-lg flex items-center justify-center text-[11px] font-black shadow-sm">
                                                                    {{ $loop->iteration }}
                                                                </div>
                                                                <div>
                                                                    <p class="text-xs font-black text-slate-800 uppercase tracking-tight">{{ $candidate->name }}</p>
                                                                    <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">{{ optional($candidate->partylist)->name ?? 'Independent' }}</p>
                                                                </div>
                                                            </div>
                                                            <div class="text-right">
                                                                <p class="text-sm font-black text-slate-900 leading-none">{{ number_format($candidate->votes_count) }}</p>
                                                                <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">{{ $percentage }}%</p>
                                                            </div>
                                                        </div>
                                                        <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                                            <div class="h-full rounded-full transition-all duration-1000 {{ $isWinner ? 'bg-indigo-600 shadow-sm shadow-indigo-100' : 'bg-slate-300' }}"
                                                                 style="width: {{ $percentage }}%"></div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Election Summary Card (Bottom Panel) -->
                <div class="bg-indigo-50/50 rounded-[2.5rem] border border-indigo-100 p-8 mb-12">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-indigo-200">
                            <i class="ri-line-chart-line text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-black text-slate-900 uppercase tracking-tight">Election Summary</h2>
                            <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Key highlights and metrics</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="bg-white p-6 rounded-3xl border border-white shadow-xl shadow-indigo-100/50">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Voters</p>
                            <p class="text-2xl font-black text-slate-900">{{ number_format($totalRegistered) }}</p>
                        </div>
                        <div class="bg-white p-6 rounded-3xl border border-white shadow-xl shadow-indigo-100/50">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Votes Cast</p>
                            <p class="text-2xl font-black text-slate-900">{{ number_format($totalVotes) }}</p>
                        </div>
                        <div class="bg-white p-6 rounded-3xl border border-white shadow-xl shadow-indigo-100/50">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Turnout Rate</p>
                            <p class="text-2xl font-black text-indigo-600">{{ $totalRegistered > 0 ? round(($turnoutCount / $totalRegistered) * 100, 1) : 0 }}%</p>
                        </div>
                        <div class="bg-white p-6 rounded-3xl border border-white shadow-xl shadow-indigo-100/50">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Positions</p>
                            <p class="text-2xl font-black text-slate-900">{{ $election->positions->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
@endpush
