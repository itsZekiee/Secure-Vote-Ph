@extends('layouts.app-main-admin')

@section('content')
<div class="flex min-h-screen bg-slate-50">
    <x-admin-sidebar />

    <main class="flex-1 min-h-screen">
        <x-admin-header title="Election Analytics & Results" />

        <div class="p-6">
            <div class="max-w-7xl mx-auto">
                <!-- Navigation & Breadcrumbs -->
                <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <nav class="flex items-center gap-2 text-sm text-slate-500 mb-2">
                            <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-600 transition-colors">Dashboard</a>
                            <i class="ri-arrow-right-s-line text-lg"></i>
                            <a href="{{ route('admin.reports.index') }}" class="hover:text-indigo-600 transition-colors">Reports</a>
                            <i class="ri-arrow-right-s-line text-lg"></i>
                            <span class="text-slate-900 font-medium">View Report</span>
                        </nav>
                        <h1 class="text-4xl font-black text-slate-900 tracking-tight">{{ $election->title }}</h1>
                    </div>
                    <div class="flex items-center gap-3">
                        <button onclick="window.print()"
                                class="px-6 py-3 bg-white border border-slate-200 text-slate-700 rounded-xl font-bold hover:bg-slate-50 transition-all flex items-center gap-2 shadow-sm">
                            <i class="ri-printer-line text-lg"></i>
                            Print Report
                        </button>
                        <a href="{{ route('admin.reports.export', ['form_id' => $election->id, 'format' => 'xlsx']) }}"
                           class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all flex items-center gap-2">
                            <i class="ri-file-excel-2-line text-lg"></i>
                            Export Data
                        </a>
                    </div>
                </div>

                <!-- Election Profile Card -->
                <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden mb-8">
                    <div class="grid grid-cols-1 lg:grid-cols-3">
                        <div class="p-10 lg:p-12 lg:col-span-2 border-b lg:border-b-0 lg:border-r border-slate-100">
                            <div class="flex items-center gap-6 mb-8">
                                <div class="w-20 h-20 rounded-3xl bg-indigo-50 flex items-center justify-center text-indigo-600 shadow-inner">
                                    <i class="ri-building-line text-4xl"></i>
                                </div>
                                <div>
                                    <h2 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Organization</h2>
                                    <p class="text-2xl font-bold text-slate-900">{{ optional($election->organization)->name ?? 'Independent Election' }}</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                                <div>
                                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Duration</h3>
                                    <div class="flex items-center gap-3 text-slate-700">
                                        <i class="ri-calendar-event-line text-indigo-500 text-lg"></i>
                                        <span class="font-semibold">{{ optional($election->start_date)->format('M d, Y') }} - {{ optional($election->end_date)->format('M d, Y') }}</span>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Election Code</h3>
                                    <div class="flex items-center gap-3">
                                        <span class="px-4 py-1.5 bg-slate-100 rounded-lg font-mono font-bold text-slate-700 border border-slate-200 uppercase tracking-widest">{{ $election->code }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-10 lg:p-12 bg-slate-50/50 flex flex-col justify-center">
                            <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-6">Engagement Overview</h3>
                            <div class="space-y-6">
                                <div>
                                    <div class="flex justify-between items-end mb-2">
                                        <span class="text-sm font-bold text-slate-600">Voter Turnout</span>
                                        <span class="text-2xl font-black text-indigo-600">
                                            {{ $totalRegistered > 0 ? round(($turnoutCount / $totalRegistered) * 100, 1) : 0 }}%
                                        </span>
                                    </div>
                                    <div class="w-full bg-slate-200 h-2.5 rounded-full overflow-hidden">
                                        <div class="h-full bg-indigo-600 rounded-full transition-all duration-1000"
                                             style="width: {{ $totalRegistered > 0 ? ($turnoutCount / $totalRegistered) * 100 : 0 }}%"></div>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-2 font-medium">{{ $turnoutCount }} participated out of {{ $totalRegistered }} total registered voters</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                    <div class="bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm transition-all hover:shadow-md group">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center transition-transform group-hover:scale-110">
                                <i class="ri-checkbox-circle-line text-2xl"></i>
                            </div>
                            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest">Total Votes Cast</h3>
                        </div>
                        <div class="text-4xl font-black text-slate-900 tracking-tight">{{ number_format($totalVotes) }}</div>
                        <p class="text-xs text-slate-500 mt-2 font-medium">Accumulated across all positions</p>
                    </div>

                    <div class="bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm transition-all hover:shadow-md group">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center transition-transform group-hover:scale-110">
                                <i class="ri-group-line text-2xl"></i>
                            </div>
                            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest">Election Scale</h3>
                        </div>
                        <div class="text-4xl font-black text-slate-900 tracking-tight">{{ $election->positions->count() }}</div>
                        <p class="text-xs text-slate-500 mt-2 font-medium">Distinct categories and positions</p>
                    </div>

                    <div class="bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm transition-all hover:shadow-md group">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center transition-transform group-hover:scale-110">
                                <i class="ri-user-star-line text-2xl"></i>
                            </div>
                            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest">Candidates</h3>
                        </div>
                        <div class="text-4xl font-black text-slate-900 tracking-tight">{{ $election->positions->pluck('candidates')->collapse()->count() }}</div>
                        <p class="text-xs text-slate-500 mt-2 font-medium">Total active participants</p>
                    </div>
                </div>

                <!-- Results Section -->
                <div class="mb-8">
                    <h2 class="text-2xl font-black text-slate-900 uppercase tracking-tight mb-8 flex items-center gap-3">
                        <i class="ri-bar-chart-2-fill text-indigo-600"></i>
                        Detailed Results per Position
                    </h2>

                    <div class="grid grid-cols-1 gap-8">
                        @foreach($election->positions as $position)
                            <div class="bg-white rounded-[2.5rem] border border-slate-200 overflow-hidden shadow-sm transition-all hover:shadow-lg">
                                <div class="px-10 py-8 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-200">
                                            <i class="ri-briefcase-line text-xl"></i>
                                        </div>
                                        <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight">{{ $position->title }}</h3>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="px-5 py-2 bg-white border border-slate-200 rounded-2xl text-xs font-black text-slate-600 shadow-sm">
                                            {{ number_format($position->candidates->sum('votes_count')) }} TOTAL VOTES
                                        </span>
                                    </div>
                                </div>

                                <div class="p-10">
                                    <div class="space-y-10">
                                        @foreach($position->candidates->sortByDesc('votes_count') as $candidate)
                                            @php
                                                $posTotalVotes = $position->candidates->sum('votes_count');
                                                $percentage = $posTotalVotes > 0 ? round(($candidate->votes_count / $posTotalVotes) * 100, 1) : 0;
                                                $isWinner = $loop->first && $candidate->votes_count > 0;
                                            @endphp
                                            <div class="relative group">
                                                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 gap-4">
                                                    <div class="flex items-center gap-5">
                                                        <div class="relative">
                                                            @if($candidate->photo)
                                                                <img src="{{ asset('storage/' . $candidate->photo) }}" class="w-16 h-16 rounded-2xl object-cover shadow-md border-2 {{ $isWinner ? 'border-indigo-600' : 'border-slate-200' }}">
                                                            @else
                                                                <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 border-2 {{ $isWinner ? 'border-indigo-600' : 'border-slate-200' }}">
                                                                    <i class="ri-user-line text-2xl"></i>
                                                                </div>
                                                            @endif
                                                            @if($isWinner)
                                                                <div class="absolute -top-3 -right-3 w-8 h-8 bg-amber-400 text-white rounded-full flex items-center justify-center shadow-lg border-2 border-white">
                                                                    <i class="ri-trophy-fill text-sm"></i>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <div class="flex items-center gap-3 mb-1">
                                                                <h4 class="text-xl font-black text-slate-900">{{ $candidate->name }}</h4>
                                                                @if($isWinner)
                                                                    <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-widest rounded-full border border-indigo-100">
                                                                        Current Leader
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            @if($candidate->partylist)
                                                                <div class="flex items-center gap-2 text-slate-400 text-sm font-bold">
                                                                    <i class="ri-team-line"></i>
                                                                    {{ $candidate->partylist->name }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="text-left sm:text-right">
                                                        <div class="text-3xl font-black text-slate-900 leading-none mb-1">
                                                            {{ number_format($candidate->votes_count) }}
                                                        </div>
                                                        <div class="text-sm font-bold text-slate-400 uppercase tracking-widest">
                                                            Votes ({{ $percentage }}%)
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="w-full bg-slate-100 h-4 rounded-full overflow-hidden p-1 border border-slate-200 shadow-inner">
                                                    <div class="h-full rounded-full transition-all duration-1000 shadow-sm {{ $isWinner ? 'bg-gradient-to-r from-indigo-600 to-indigo-400' : 'bg-slate-300' }}"
                                                         style="width: {{ $percentage }}%"></div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
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
