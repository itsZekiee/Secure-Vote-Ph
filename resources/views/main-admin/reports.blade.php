@extends('layouts.app-main-admin')

@section('content')

    <div x-data="{ collapsed: false, isMobile: window.innerWidth < 1024 }"
         x-init="window.addEventListener('resize', () => { isMobile = window.innerWidth < 1024 })"
         class="flex min-h-screen bg-slate-50">

        <x-admin-sidebar />

        <main class="flex-1 min-h-screen">

            <!-- Mobile Header -->
            <header x-show="isMobile"
                    class="lg:hidden bg-white shadow-sm border-b px-4 py-3 flex items-center justify-between">
                <button @click="collapsed = false"
                        class="p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                    <i class="ri-menu-line text-lg"></i>
                </button>
                <h1 class="text-lg font-semibold text-slate-800">Reports &amp; Tally</h1>
            </header>

            <div class="p-6">
                <div class="max-w-7xl">

                    <!-- Hero -->
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center justify-center w-14 h-14 rounded-xl bg-gradient-to-br from-emerald-600 to-sky-600 text-white shadow-md">
                                <i class="ri-bar-chart-line text-2xl"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-semibold text-slate-900 leading-tight">Reports &amp; Tally</h2>
                                <p class="text-sm text-slate-500 mt-1">View completed forms, final tallies and election statistics</p>
                            </div>
                        </div>

                        <nav class="hidden sm:flex items-center text-sm text-slate-500 space-x-2">
                            <span class="text-slate-400">Admin</span>
                            <i class="ri-arrow-right-s-line text-slate-300"></i>
                            <span class="text-slate-700 font-medium">Reports</span>
                        </nav>
                    </div>

                    <!-- Form selector & actions + Forms table -->
                    <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
                        <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-col md:flex-row items-start md:items-center gap-3">
                            <div class="flex-1 min-w-0 flex items-center gap-3">
                                <!-- Search -->
                                <label for="q" class="sr-only">Search</label>
                                <div class="relative flex-1">
                                    <input id="q" name="q" type="search" value="{{ request('q') }}"
                                           placeholder="Search by title, candidate, or keyword"
                                           class="w-full px-4 py-3 pl-10 border border-gray-200 rounded-lg bg-white text-sm focus:outline-none focus:ring-2 focus:ring-sky-100" />
                                    <span class="absolute left-3 top-3 text-slate-400">
                                    <i class="ri-search-line"></i>
                                </span>
                                </div>

                                <!-- Organization filter (always dropdown) -->
                                <label for="organization_id" class="sr-only">Organization</label>
                                <select id="organization_id" name="organization_id"
                                        class="px-3 py-3 border border-gray-200 rounded-lg text-sm bg-white">
                                    <option value="">{{ __('All organizations') }}</option>
                                    @foreach($organizations ?? [] as $org)
                                        <option value="{{ $org->id }}" {{ (string) request('organization_id') === (string) $org->id ? 'selected' : '' }}>
                                            {{ $org->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <!-- Year filter -->
                                <label for="year" class="sr-only">Year</label>
                                <select id="year" name="year" class="px-3 py-3 border border-gray-200 rounded-lg text-sm bg-white">
                                    <option value="">{{ __('All years') }}</option>
                                    @if(isset($years) && count($years))
                                        @foreach($years as $y)
                                            <option value="{{ $y }}" {{ (string) request('year') === (string) $y ? 'selected' : '' }}>{{ $y }}</option>
                                        @endforeach
                                    @else
                                        @php
                                            $start = 2020;
                                            $current = (int) date('Y');
                                        @endphp
                                        @for($y = $current; $y >= $start; $y--)
                                            <option value="{{ $y }}" {{ (string) request('year') === (string) $y ? 'selected' : '' }}>{{ $y }}</option>
                                        @endfor
                                    @endif
                                </select>
                            </div>

                            <div class="flex items-center gap-2">
                                <button type="submit"
                                        class="inline-flex items-center px-4 py-3 bg-emerald-600 text-white rounded-lg text-sm shadow hover:bg-emerald-700">
                                    <i class="ri-search-line mr-2"></i>
                                    Search
                                </button>

                                <a href="{{ route('admin.reports.index') }}"
                                   class="inline-flex items-center px-4 py-3 bg-white border border-gray-200 rounded-lg text-sm hover:bg-gray-50">
                                    Reset
                                </a>

                                <a href="{{ route('admin.reports.export', array_merge(request()->only(['q','organization_id','year']), ['format' => 'xlsx'])) }}"
                                   class="inline-flex items-center px-4 py-3 text-sm bg-white border border-gray-200 rounded-lg hover:bg-gray-50">
                                    <i class="ri-file-excel-2-line mr-2"></i>
                                    Export XLSX
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Forms table -->
                    <div class="bg-white rounded-lg shadow-sm border overflow-hidden mb-6">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-lg font-semibold text-slate-900">Forms</h3>
                            <p class="text-sm text-slate-500 mt-1">List of completed forms matching your filters</p>
                        </div>

                        <div class="p-6 overflow-x-auto">
                            @if(isset($forms) && count($forms))
                                <table class="min-w-full text-sm text-left">
                                    <thead>
                                    <tr class="text-slate-500 bg-gray-50">
                                        <th class="px-4 py-3">Form Title</th>
                                        <th class="px-4 py-3">Organization Name</th>
                                        <th class="px-4 py-3">Date Ended</th>
                                        <th class="px-4 py-3">Contacted Person</th>
                                        <th class="px-4 py-3">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody class="divide-y">
                                    @foreach($forms as $form)
                                        <tr class="bg-white">
                                            <td class="px-4 py-3">
                                                <a href="{{ isset($form->id) ? route('admin.forms.show', $form->id) : '#' }}" class="text-sky-600 hover:underline">
                                                    {{ $form->title ?? '—' }}
                                                </a>
                                            </td>
                                            <td class="px-4 py-3">
                                                {{ optional($form->organization)->name ?? ($form->organization_name ?? '—') }}
                                            </td>
                                            <td class="px-4 py-3">
                                                {{ optional($form->ended_at)->format('Y-m-d') ?? '—' }}
                                            </td>
                                            <td class="px-4 py-3">
                                                {{ $form->contact_person ?? $form->contacted_person ?? '—' }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <a href="{{ route('admin.reports.export', ['form_id' => $form->id, 'format' => 'xlsx']) }}"
                                                   class="inline-flex items-center px-3 py-1 text-sm bg-white border border-gray-200 rounded hover:bg-gray-50">
                                                    <i class="ri-download-line mr-2"></i>
                                                    Download
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="text-center text-slate-500 py-8">
                                    No forms found.
                                </div>
                            @endif
                        </div>

                        @if(isset($forms) && method_exists($forms, 'links'))
                            <div class="px-6 py-3 bg-gray-50">
                                {{ $forms->appends(request()->only(['q','organization_id','year']))->links() }}
                            </div>
                        @endif
                    </div>

                    @if(isset($selectedForm))
                        @php
                            $total = (int) ($totalVotes ?? $selectedForm->total_votes ?? 0);
                            $registered = (int) ($registeredVoters ?? $selectedForm->registered_count ?? 0);
                            $participation = ($registered > 0 && $total > 0) ? (float) round(($total / $registered) * 100, 1) : null;
                        @endphp

                            <!-- Summary cards -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                            <div class="bg-white rounded-lg shadow-sm border p-4">
                                <div class="text-sm text-slate-500">Total Votes Cast</div>
                                <div class="mt-2 text-2xl font-semibold text-slate-900">{{ $total }}</div>
                                <div class="text-xs text-slate-400 mt-1">{{ __('Final tally for this form') }}</div>
                            </div>

                            <div class="bg-white rounded-lg shadow-sm border p-4">
                                <div class="text-sm text-slate-500">Registered Voters</div>
                                <div class="mt-2 text-2xl font-semibold text-slate-900">{{ $registered }}</div>
                                <div class="text-xs text-slate-400 mt-1">Total registered at start</div>
                            </div>

                            <div class="bg-white rounded-lg shadow-sm border p-4">
                                <div class="text-sm text-slate-500">Started</div>
                                <div class="mt-2 text-lg font-medium text-slate-900">{{ optional($selectedForm->started_at)->format('Y-m-d H:i') ?? '—' }}</div>
                                <div class="text-xs text-slate-400 mt-1">When voting started</div>
                            </div>

                            <div class="bg-white rounded-lg shadow-sm border p-4">
                                <div class="text-sm text-slate-500">Ended</div>
                                <div class="mt-2 text-lg font-medium text-slate-900">{{ optional($selectedForm->ended_at)->format('Y-m-d H:i') ?? '—' }}</div>
                                <div class="text-xs text-slate-400 mt-1">When voting ended</div>
                            </div>
                        </div>

                        <!-- Winner highlight -->
                        <div class="bg-white rounded-lg shadow-sm border p-4 mb-6 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-yellow-100 text-yellow-700 flex items-center justify-center font-semibold">
                                    <i class="ri-trophy-line text-xl"></i>
                                </div>
                                <div>
                                    <div class="text-sm text-slate-500">Winner</div>
                                    <div class="text-lg font-semibold text-slate-900">
                                        {{ $winner->name ?? (isset($results) && count($results) ? $results[0]->name : '—') }}
                                    </div>
                                    <div class="text-xs text-slate-400 mt-1">
                                        {{ $winner->votes ?? (isset($results) && count($results) ? $results[0]->votes : 0) }} votes
                                    </div>
                                </div>
                            </div>

                            <div class="text-sm text-slate-500">
                                <span class="mr-3">Participation</span>
                                <span class="font-medium text-slate-700">{{ $participation !== null ? $participation . '%' : '—' }}</span>
                            </div>
                        </div>

                        <!-- Results table -->
                        <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">Tally Results</h3>
                                    <p class="text-sm text-slate-500 mt-1">Candidate vote counts and percentages</p>
                                </div>

                                <div class="text-sm text-slate-500">
                                    <span class="mr-3">Total votes</span>
                                    <span class="font-medium text-slate-700">{{ $total }}</span>
                                </div>
                            </div>

                            <div class="p-6">
                                @if(isset($results) && count($results))
                                    <div class="space-y-4">
                                        @foreach($results as $candidate)
                                            @php
                                                $votes = (int) ($candidate->votes ?? 0);
                                                $pct = ($total > 0) ? (float) round(($votes / $total) * 100, 1) : 0;
                                                $pctWidth = $pct . '%';
                                            @endphp

                                            <div class="flex items-center justify-between gap-4">
                                                <div class="flex-1">
                                                    <div class="flex items-center justify-between">
                                                        <div class="flex items-center gap-3">
                                                            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-700 font-medium">
                                                                {{ strtoupper(substr($candidate->name, 0, 1)) }}
                                                            </div>
                                                            <div>
                                                                <div class="font-medium text-slate-800">{{ $candidate->name }}</div>
                                                                <div class="text-xs text-slate-400">Party: {{ $candidate->party ?? '—' }}</div>
                                                            </div>
                                                        </div>

                                                        <div class="text-right">
                                                            <div class="text-sm font-semibold text-slate-800">{{ $votes }}</div>
                                                            <div class="text-xs text-slate-400">{{ $pct }}%</div>
                                                        </div>
                                                    </div>

                                                    <div class="mt-2 h-2 bg-gray-100 rounded-full overflow-hidden">
                                                        <div class="h-2 bg-emerald-500 rounded-full" style="width: {{ $pctWidth }}"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center text-slate-500 py-12">
                                        No results available for this form.
                                    </div>
                                @endif
                            </div>

                            <div class="px-6 py-4 bg-gray-50 flex items-center justify-between text-sm text-slate-500">
                                <div>
                                    Showing results for: <span class="font-medium text-slate-700">{{ $selectedForm->title }}</span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.reports.print', ['form_id' => $selectedForm->id]) ?? '#' }}"
                                       class="px-3 py-1 rounded border border-gray-200 bg-white text-sm">Print</a>
                                    <a href="{{ route('admin.reports.snapshot', ['form_id' => $selectedForm->id]) ?? '#' }}"
                                       class="px-3 py-1 rounded border border-gray-200 bg-white text-sm">Snapshot</a>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </main>
    </div>

@endsection
