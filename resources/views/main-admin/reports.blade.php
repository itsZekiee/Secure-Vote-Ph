@extends('layouts.app-main-admin')

@section('content')
    <x-admin-header title="Reports & Tally">
        <x-slot name="breadcrumbs">
            <nav class="hidden sm:flex items-center text-sm text-slate-500 space-x-2 mr-4">
                <span class="text-slate-400">Admin</span>
                <i class="ri-arrow-right-s-line text-slate-300"></i>
                <span class="text-slate-700 font-medium">Reports</span>
            </nav>
        </x-slot>
    </x-admin-header>

    <div class="flex min-h-screen bg-gradient-to-br from-slate-50 via-emerald-50/20 to-sky-50/20">

        <x-admin-sidebar />

        <main class="flex-1 min-h-screen">
            <!-- Top Navigation Bar -->
            <div class="bg-white/80 backdrop-blur-xl border-b border-gray-200/50 sticky top-0 z-40">
                <div class="px-8 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-emerald-600 to-sky-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <i class="ri-bar-chart-line text-white text-lg"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900">Reports & Tally</h2>
                                    <p class="text-sm text-gray-600">Election results & statistics</p>
                                </div>
                            </div>
                            <div class="h-8 w-px bg-gray-200"></div>
                            <nav class="flex items-center space-x-2 text-sm">
                                <span class="text-gray-500">Admin</span>
                                <i class="ri-arrow-right-s-line text-gray-400"></i>
                                <span class="text-gray-900 font-semibold">Reports</span>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-6">
                <div class="max-w-7xl mx-auto">
                    <!-- Search & Filters Section -->
                    <div class="bg-white rounded-xl border border-gray-200/60 shadow-sm overflow-hidden mb-6">
                        <div class="px-6 py-4 border-b border-gray-200/60 bg-gradient-to-r from-emerald-50/50 via-sky-50/50 to-slate-50/50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-white/80 rounded-lg flex items-center justify-center shadow-sm border border-gray-200/50">
                                        <i class="ri-filter-3-line text-gray-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-bold text-gray-900">Reports Search</h3>
                                        <p class="text-[11px] text-gray-500 font-medium">Filter through completed elections</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('admin.reports.export', array_merge(request()->only(['q','organization_id','year']), ['format' => 'xlsx'])) }}"
                                       class="inline-flex items-center px-4 py-2 bg-white text-slate-700 text-xs font-bold rounded-lg border border-slate-200 hover:bg-slate-50 transition-all shadow-sm">
                                        <i class="ri-file-excel-2-line mr-1.5 text-emerald-600"></i>
                                        Export
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            <form method="GET" action="{{ route('admin.reports.index') }}">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                    <div class="lg:col-span-2">
                                        <label class="block text-xs font-bold text-gray-800 mb-2">Keyword Search</label>
                                        <div class="relative">
                                            <i class="ri-search-line absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                            <input type="search" name="q" value="{{ request('q') }}"
                                                   placeholder="Election title, organization..."
                                                   class="w-full pl-10 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 bg-white">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-800 mb-2">Organization</label>
                                        <div class="relative">
                                            <select name="organization_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 bg-white appearance-none">
                                                <option value="">All Organizations</option>
                                                @foreach($organizations ?? [] as $org)
                                                    <option value="{{ $org->id }}" {{ (string) request('organization_id') === (string) $org->id ? 'selected' : '' }}>
                                                        {{ $org->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <i class="ri-arrow-down-s-line text-gray-400"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-800 mb-2">Year</label>
                                        <div class="relative">
                                            <select name="year" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 bg-white appearance-none">
                                                <option value="">All Years</option>
                                                @php $currentYear = (int)date('Y'); @endphp
                                                @for($y = $currentYear; $y >= 2020; $y--)
                                                    <option value="{{ $y }}" {{ (string) request('year') === (string) $y ? 'selected' : '' }}>{{ $y }}</option>
                                                @endfor
                                            </select>
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <i class="ri-arrow-down-s-line text-gray-400"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-6 flex justify-end gap-2">
                                    <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 text-xs font-bold rounded-lg hover:bg-slate-200 transition-all">RESET</a>
                                    <button type="submit" class="px-6 py-2 bg-slate-900 text-white text-xs font-bold rounded-lg hover:bg-emerald-600 transition-all">SEARCH</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Forms Table -->
                    <div class="bg-white rounded-xl border border-gray-200/60 shadow-sm overflow-hidden mb-8">
                        <div class="responsive-table-container">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-slate-50/50">
                                <tr>
                                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Election Form</th>
                                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Institutional Affiliation</th>
                                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Completion Date</th>
                                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">View Tally</th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                @forelse($forms ?? [] as $form)
                                    <tr class="hover:bg-emerald-50/30 transition-all group">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 bg-white border border-slate-100 rounded-lg flex items-center justify-center text-slate-400 group-hover:scale-110 transition-transform">
                                                    <i class="ri-file-list-3-line text-lg text-emerald-600"></i>
                                                </div>
                                                <div>
                                                    <p class="font-bold text-slate-900 leading-tight text-sm">{{ $form->title }}</p>
                                                    <p class="text-[10px] font-bold text-slate-400 mt-0.5 uppercase tracking-wider">{{ $form->candidates_count ?? 0 }} Candidates</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-xs font-bold text-slate-700">{{ optional($form->organization)->name ?? 'Independent' }}</p>
                                        </td>
                                        <td class="px-6 py-4 text-xs font-bold text-slate-400 italic">
                                            {{ optional($form->end_date)->format('M d, Y') ?? 'Ongoing' }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end gap-2 transition-all">
                                                <a href="{{ route('admin.reports.view', $form->id) }}"
                                                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-900 text-white text-[10px] font-bold uppercase tracking-widest rounded-lg hover:bg-emerald-600 transition-all">
                                                    <i class="ri-eye-line text-xs"></i> VIEW RESULTS
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-6 py-16 text-center text-slate-500 font-bold">No reports matching your criteria found.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if(isset($selectedForm))
                        <!-- Tally Results Section (Visible when a form is selected) -->
                        <div class="space-y-6 animate-in fade-in slide-in-from-bottom-8 duration-700">
                            @php
                                $total = (int) ($totalVotes ?? $selectedForm->total_votes ?? 0);
                                $registered = (int) ($registeredVoters ?? $selectedForm->registered_count ?? 0);
                                $participation = ($registered > 0) ? (float) round(($total / $registered) * 100, 1) : 0;
                            @endphp

                            <!-- Stats Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                <div class="bg-white p-6 rounded-2xl border border-slate-200/60 shadow-sm">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Total Votes Cast</p>
                                    <h4 class="text-2xl font-bold text-slate-900">{{ number_format($total) }}</h4>
                                </div>
                                <div class="bg-white p-6 rounded-2xl border border-slate-200/60 shadow-sm text-center">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Participation Rate</p>
                                    <h4 class="text-2xl font-bold text-emerald-600">{{ $participation }}%</h4>
                                </div>
                                <div class="md:col-span-2 bg-slate-900 p-6 rounded-2xl flex items-center justify-between text-white overflow-hidden relative">
                                    <div class="relative z-10">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 text-emerald-400">Projected Winner</p>
                                        <h4 class="text-xl font-bold">{{ $winner->name ?? (isset($results) && count($results) ? $results[0]->name : 'N/A') }}</h4>
                                        <p class="text-[10px] font-bold text-slate-400 mt-1">{{ number_format($winner->votes ?? (isset($results) && count($results) ? $results[0]->votes : 0)) }} total votes verified</p>
                                    </div>
                                    <i class="ri-trophy-fill text-6xl text-emerald-500/10 absolute -right-3 -bottom-3"></i>
                                </div>
                            </div>

                            <!-- Detailed Tally -->
                            <div class="bg-white rounded-2xl border border-slate-200/60 shadow-lg overflow-hidden">
                                <div class="p-8 border-b border-slate-100 flex items-center justify-between">
                                    <div>
                                        <h3 class="text-xl font-bold text-slate-900 uppercase tracking-tight">Tally Breakdown</h3>
                                        <p class="text-[11px] text-gray-500 font-medium mt-0.5">Real-time metrics</p>
                                    </div>
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.reports.print', ['form_id' => $selectedForm->id]) }}" class="px-4 py-2 bg-slate-100 text-slate-600 font-bold text-[10px] uppercase tracking-widest rounded-lg hover:bg-slate-200 transition-all">PRINT REPORT</a>
                                    </div>
                                </div>
                                <div class="p-8 space-y-6">
                                    @forelse($results ?? [] as $candidate)
                                        @php
                                            $votes = (int) ($candidate->votes ?? 0);
                                            $pct = ($total > 0) ? (float) round(($votes / $total) * 100, 1) : 0;
                                        @endphp
                                        <div class="group">
                                            <div class="flex items-center justify-between mb-3">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-10 h-10 bg-slate-900 text-white rounded-xl flex items-center justify-center font-bold text-xs">
                                                        {{ strtoupper(substr($candidate->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-slate-900 text-sm">{{ $candidate->name }}</p>
                                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $candidate->party ?? 'Independent' }}</p>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-base font-bold text-slate-900">{{ number_format($votes) }}</p>
                                                    <p class="text-[10px] font-bold text-emerald-600">{{ $pct }}%</p>
                                                </div>
                                            </div>
                                            <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                                                <div class="h-full bg-gradient-to-r from-emerald-500 to-sky-500 rounded-full transition-all duration-1000 group-hover:scale-y-110" style="width: {{ $pct }}%"></div>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-center text-slate-400 font-bold py-10">No voting data available for this election yet.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
@endsection
