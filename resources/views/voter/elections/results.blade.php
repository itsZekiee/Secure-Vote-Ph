@extends('voter.layouts.app')

@section('title', 'Live Results - ' . $election->title)

@push('styles')
    <style>
        :root {
            --brand-primary: #003153;
            --brand-accent: #00D4AA;
            --brand-bg: #F8FAFC;
        }

        .bg-brand-primary { background-color: var(--brand-primary); }
        .bg-brand-accent { background-color: var(--brand-accent); }
        .text-brand-primary { color: var(--brand-primary); }
        .text-brand-accent { color: var(--brand-accent); }

        .glass-header {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .stat-card-gradient {
            background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-accent) 100%);
            border-radius: 1rem;
            padding: 1.25rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }

        .stat-card {
            background: white;
            border-radius: 1rem;
            padding: 1.25rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.02);
        }

        .leader-card {
            background: white;
            border-radius: 1rem;
            padding: 1rem;
            border: 1px solid #F1F5F9;
            transition: all 0.3s ease;
        }

        .leader-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
        }

        .result-section {
            background: white;
            border-radius: 1.5rem;
            overflow: hidden;
            box-shadow: 0 15px 35px -12px rgba(0, 0, 0, 0.05);
            border: 1px solid #F1F5F9;
        }

        .result-header {
            background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-accent) 100%);
            padding: 1.25rem;
            color: white;
        }

        .progress-bar-container {
            height: 8px;
            background: #F1F5F9;
            border-radius: 999px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            background: var(--brand-accent);
            border-radius: 999px;
            transition: width 1s ease-out;
        }

        .chart-bar {
            background: #F1F5F9;
            border-radius: 0.5rem 0.5rem 0 0;
            transition: all 0.5s ease;
            position: relative;
        }

        .chart-bar:hover {
            background: var(--brand-accent);
            opacity: 0.8;
        }

        .chart-bar.leading {
            background: var(--brand-accent);
        }
    </style>
@endpush

@section('content')
    <div class="min-h-screen bg-slate-50">
        <div class="flex-1 flex flex-col min-h-screen">
            <!-- Top Navigation -->
            <header class="glass-header sticky top-0 z-50 px-4 py-3">
                <div class="max-w-7xl mx-auto flex items-center justify-between">
                    @php
                        $backRoute = route('voter.welcome', $election->code);
                    @endphp
                    <a href="{{ $backRoute }}" class="flex items-center gap-2 text-brand-primary font-bold hover:opacity-70 transition-opacity text-sm">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back</span>
                    </a>

                    <div class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 bg-brand-accent rounded-full animate-pulse"></span>
                        <span class="text-brand-primary font-bold text-xs uppercase tracking-wider">Live Results</span>
                    </div>
                </div>
            </header>

            <main class="max-w-7xl mx-auto px-4 py-8">
            <!-- Header Section -->
            <div class="text-center mb-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-brand-accent/10 text-brand-accent rounded-full text-[10px] font-black uppercase tracking-widest mb-4 border border-brand-accent/20">
                    <i class="fas fa-chart-line"></i>
                    <span>Real-time Election Results</span>
                </div>
                <h1 class="text-3xl lg:text-4xl font-black text-brand-primary mb-2 tracking-tight">Election Results {{ date('Y') }}</h1>
                <p class="text-slate-500 text-sm font-medium">Live vote counting • Updated every 30 seconds</p>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                <div class="stat-card-gradient flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center text-xl">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-widest opacity-70 mb-0.5">Total Votes</div>
                        <div class="text-2xl font-black">{{ number_format($election->votes()->count()) }}</div>
                        <div class="text-[9px] opacity-60">Across all positions</div>
                    </div>
                </div>

                <div class="stat-card flex items-center gap-4">
                    <div class="w-12 h-12 bg-brand-accent/10 text-brand-accent rounded-xl flex items-center justify-center text-xl">
                        <i class="fas fa-poll"></i>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Voter Turnout</div>
                        @php
                            $totalVoters = \App\Models\Voter::where('election_id', $election->id)->count();
                            $votedCount = $election->votes()->distinct('voter_id')->count('voter_id');
                            $turnout = $totalVoters > 0 ? ($votedCount / $totalVoters) * 100 : 0;
                        @endphp
                        <div class="text-2xl font-black text-brand-primary">{{ number_format($turnout, 1) }}%</div>
                        <div class="text-[9px] text-slate-400">Of registered voters</div>
                    </div>
                </div>

                <div class="stat-card flex items-center gap-4">
                    <div class="w-12 h-12 bg-brand-accent/10 text-brand-accent rounded-xl flex items-center justify-center text-xl">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Positions</div>
                        <div class="text-2xl font-black text-brand-primary">{{ $positions->count() }}</div>
                        <div class="text-[9px] text-slate-400">Being contested</div>
                    </div>
                </div>
            </div>

            <!-- Current Leaders -->
            <div class="mb-12">
                <div class="flex items-center gap-2 mb-6">
                    <i class="fas fa-crown text-brand-accent text-lg"></i>
                    <h2 class="text-xl font-black text-brand-primary uppercase tracking-tight">Current Leaders</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($positions as $position)
                        @php
                            $leader = $position->candidates->sortByDesc('votes_count')->first();
                            $totalPosVotes = $position->candidates->sum('votes_count');
                            $percentage = $totalPosVotes > 0 ? ($leader->votes_count / $totalPosVotes) * 100 : 0;
                            $leaderName = $resultsAnonymized ? 'Candidate #1' : ($leader->name ?? 'No Candidates');
                        @endphp
                        <div class="leader-card">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-8 h-8 bg-brand-accent/10 text-brand-accent rounded-lg flex items-center justify-center text-xs">
                                    <i class="fas fa-ribbon"></i>
                                </div>
                                <span class="text-[9px] font-black text-brand-accent uppercase tracking-widest">{{ $position->title }}</span>
                            </div>
                            <h4 class="text-base font-black text-brand-primary leading-tight mb-1 truncate">{{ $leaderName }}</h4>
                            <div class="flex items-end gap-2">
                                <span class="text-xl font-black text-brand-primary">{{ number_format($percentage, 2) }}%</span>
                                <span class="text-[9px] font-bold text-slate-400 mb-1">{{ number_format($leader->votes_count ?? 0) }} votes</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Detailed Results per Position -->
            <div class="space-y-10">
                @foreach($positions as $position)
                    <div class="result-section">
                        <div class="result-header">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center text-2xl backdrop-blur-sm">
                                        <i class="fas fa-user-circle"></i>
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-black tracking-tight uppercase">{{ $position->title }}</h2>
                                        <p class="text-white/70 font-bold text-[10px] uppercase tracking-widest">
                                            {{ number_format($position->candidates->sum('votes_count')) }} Total Votes Cast
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 lg:p-8">
                            @php
                                $sortedCandidates = $position->candidates->sortByDesc('votes_count');
                                $leader = $sortedCandidates->first();
                                $totalPosVotes = $position->candidates->sum('votes_count');
                                $leaderName = $resultsAnonymized ? 'Candidate #1' : $leader->name;
                            @endphp

                                <!-- Leading Candidate Highlight -->
                            <div class="mb-8 p-6 bg-brand-accent/5 rounded-[1.5rem] border border-brand-accent/10 relative overflow-hidden">
                                <div class="absolute top-0 right-0 p-6 opacity-10">
                                    <i class="fas fa-trophy text-6xl text-brand-accent"></i>
                                </div>
                                <div class="relative z-10">
                                    <div class="inline-flex items-center gap-2 px-2 py-0.5 bg-brand-accent text-white rounded-full text-[9px] font-black uppercase tracking-widest mb-4">
                                        <i class="fas fa-crown"></i>
                                        <span>Currently Leading</span>
                                    </div>
                                    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                                        <div>
                                            <h3 class="text-2xl font-black text-brand-primary mb-1">{{ $leaderName }}</h3>
                                            <p class="text-slate-500 text-xs font-bold">
                                                {{ number_format($leader->votes_count) }} votes • {{ $totalPosVotes > 0 ? number_format(($leader->votes_count / $totalPosVotes) * 100, 2) : 0 }}% of total
                                            </p>
                                        </div>
                                        <div class="text-4xl font-black text-brand-accent tracking-tighter">
                                            {{ $totalPosVotes > 0 ? number_format(($leader->votes_count / $totalPosVotes) * 100, 2) : 0 }}%
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Vote Distribution Chart (CSS Based) -->
                            <div class="mb-10">
                                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">Vote Distribution</h4>
                                <div class="h-48 flex items-end gap-3 px-3 border-b border-slate-100">
                                    @foreach($sortedCandidates as $candidate)
                                        @php
                                            $height = $totalPosVotes > 0 ? ($candidate->votes_count / $totalPosVotes) * 100 : 0;
                                        @endphp
                                        <div class="flex-1 flex flex-col items-center gap-2">
                                            <div class="chart-bar w-full {{ $loop->first ? 'leading' : '' }}" style="height: {{ max($height, 5) }}%">
                                                <div class="absolute -top-6 left-0 right-0 text-center text-[9px] font-black text-slate-400">
                                                    {{ number_format($candidate->votes_count) }}
                                                </div>
                                            </div>
                                            <div class="text-[9px] font-black text-slate-400 uppercase tracking-tight text-center truncate w-full">
                                                @php
                                                    $nameParts = explode(' ', $candidate->name);
                                                    $lastName = last($nameParts);
                                                    $displayName = (in_array($lastName, ['IV', 'III', 'II', 'Jr.', 'Sr.']) && count($nameParts) > 1)
                                                        ? $nameParts[count($nameParts) - 2]
                                                        : $lastName;

                                                    if ($resultsAnonymized) {
                                                        $displayName = 'Candidate #' . $loop->iteration;
                                                    }
                                                @endphp
                                                {{ $displayName }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- All Candidates List -->
                            <div>
                                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">All Candidates</h4>
                                <div class="space-y-3">
                                    @foreach($sortedCandidates as $candidate)
                                        @php
                                            $percentage = $totalPosVotes > 0 ? ($candidate->votes_count / $totalPosVotes) * 100 : 0;
                                        @endphp
                                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 flex items-center gap-4">
                                            <div class="w-8 text-base font-black text-slate-300">#{{ $loop->iteration }}</div>
                                            <div class="flex-grow">
                                                <div class="flex justify-between items-center mb-2">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-base font-black text-brand-primary">
                                                            {{ $resultsAnonymized ? 'Candidate #' . $loop->iteration : $candidate->name }}
                                                        </span>
                                                        @if($loop->first)
                                                            <span class="px-1.5 py-0.5 bg-brand-accent text-white rounded text-[7px] font-black uppercase tracking-widest">Leading</span>
                                                        @endif
                                                    </div>
                                                    <span class="text-base font-black text-brand-primary">{{ number_format($percentage, 2) }}%</span>
                                                </div>
                                                <div class="progress-bar-container h-1.5">
                                                    <div class="progress-bar-fill" style="width: {{ $percentage }}%; {{ !$loop->first ? 'background: #CBD5E1;' : '' }}"></div>
                                                </div>
                                                <div class="mt-1.5 text-[9px] font-bold text-slate-400 uppercase tracking-widest">
                                                    {{ number_format($candidate->votes_count) }} votes
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            </main>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        async function updateVotes() {
            try {
                const response = await fetch('{{ route('voter.elections.results.votes', $election->code) }}');
                const data = await response.json();

                if (data.success) {
                    // Refresh page for now to update all complex UI components correctly
                    // In a production app, we would update individual elements via DOM
                    location.reload();
                }
            } catch (error) {
                console.error('Failed to update votes:', error);
            }
        }

        // Update every 30 seconds as per design
        setInterval(updateVotes, 30000);
    </script>
@endpush
