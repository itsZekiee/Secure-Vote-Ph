@extends('layouts.app-main-admin')

@section('title', 'Election Results - ' . $election->title)

@push('styles')
    <style>
        /* Custom animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse-glow {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4);
            }
            50% {
                box-shadow: 0 0 0 8px rgba(16, 185, 129, 0);
            }
        }

        @keyframes gradient-shift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes count-up {
            from { opacity: 0; transform: scale(0.5); }
            to { opacity: 1; transform: scale(1); }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .animate-pulse-glow {
            animation: pulse-glow 2s infinite;
        }

        .animate-gradient {
            background-size: 200% 200%;
            animation: gradient-shift 3s ease infinite;
        }

        .stagger-1 { animation-delay: 0.1s; }
        .stagger-2 { animation-delay: 0.2s; }
        .stagger-3 { animation-delay: 0.3s; }
        .stagger-4 { animation-delay: 0.4s; }

        /* Glassmorphism */
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .glass-dark {
            background: rgba(17, 24, 39, 0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        /* Progress bar animation */
        .progress-bar {
            transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Card hover effects */
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.1);
        }

        /* Candidate card 3D effect */
        .candidate-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            transform-style: preserve-3d;
        }

        .candidate-card:hover {
            transform: translateY(-2px) scale(1.02);
        }

        /* Crown animation */
        .crown-badge {
            animation: count-up 0.5s ease-out forwards;
        }

        /* Floating particles */
        .particle {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
    </style>
@endpush

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-emerald-50/30 relative overflow-hidden">
        <!-- Decorative Background Elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-emerald-100/40 to-teal-100/40 rounded-full blur-3xl transform translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-gradient-to-tr from-blue-100/30 to-indigo-100/30 rounded-full blur-3xl transform -translate-x-1/2 translate-y-1/2"></div>
            <div class="absolute top-1/2 left-1/2 w-64 h-64 bg-gradient-to-r from-purple-100/20 to-pink-100/20 rounded-full blur-3xl transform -translate-x-1/2 -translate-y-1/2"></div>
        </div>

        <!-- Header Section -->
        <div class="relative glass border-b border-white/20 shadow-lg shadow-gray-100/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
                <div class="text-center animate-fade-in-up">
                    <!-- Live Badge -->
                    <div class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full mb-6 shadow-lg shadow-emerald-500/25 animate-pulse-glow">
                    <span class="relative flex h-3 w-3 mr-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-white"></span>
                    </span>
                        <span class="text-sm font-semibold text-white tracking-wide">LIVE RESULTS</span>
                    </div>

                    <!-- Title -->
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 mb-4 tracking-tight">
                        {{ $election->title }}
                    </h1>

                    <!-- Description -->
                    <p class="text-lg md:text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed font-light">
                        {{ $election->description }}
                    </p>

                    <!-- Stats Cards -->
                    <div class="flex flex-wrap justify-center gap-4 mt-10">
                        <div class="glass px-6 py-4 rounded-2xl border border-white/30 shadow-lg shadow-gray-100/50 card-hover stagger-1 animate-fade-in-up opacity-0">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="text-left">
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $election->start_date?->format('M d') }} - {{ $election->end_date?->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="glass px-6 py-4 rounded-2xl border border-white/30 shadow-lg shadow-gray-100/50 card-hover stagger-2 animate-fade-in-up opacity-0">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-violet-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg shadow-violet-500/30">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <div class="text-left">
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Registered</p>
                                    <p class="text-2xl font-bold text-gray-900" id="total-voters">{{ number_format($totalVoters ?? 0) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="glass px-6 py-4 rounded-2xl border border-white/30 shadow-lg shadow-gray-100/50 card-hover stagger-3 animate-fade-in-up opacity-0">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="text-left">
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Votes Cast</p>
                                    <p class="text-2xl font-bold text-gray-900" id="votes-cast">{{ number_format($totalVotesCast ?? 0) }}</p>
                                </div>
                            </div>
                        </div>

                        @php
                            $turnoutPercentage = ($totalVoters ?? 0) > 0 ? (($totalVotesCast ?? 0) / ($totalVoters ?? 1)) * 100 : 0;
                        @endphp
                        <div class="glass px-6 py-4 rounded-2xl border border-white/30 shadow-lg shadow-gray-100/50 card-hover stagger-4 animate-fade-in-up opacity-0">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/30">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                    </svg>
                                </div>
                                <div class="text-left">
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Turnout</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ number_format($turnoutPercentage, 1) }}%</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

            <!-- Real-time Results Section -->
            <section class="mb-20">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-10 gap-4">
                    <div class="animate-fade-in-up">
                        <h2 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <span class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </span>
                            Real-Time Results
                        </h2>
                        <p class="text-gray-500 mt-2 ml-13">Auto-refreshing every 30 seconds</p>
                    </div>
                    <div class="glass px-4 py-2 rounded-full border border-white/30 flex items-center text-sm text-gray-600 animate-fade-in-up">
                        <div class="relative mr-3">
                            <div class="w-2 h-2 bg-emerald-500 rounded-full animate-ping absolute"></div>
                            <div class="w-2 h-2 bg-emerald-500 rounded-full relative"></div>
                        </div>
                        Updated: <span id="last-updated" class="ml-1 font-semibold text-gray-900">Just now</span>
                    </div>
                </div>

                <div class="grid gap-8">
                    @forelse($positions ?? [] as $positionIndex => $position)
                        <div class="glass rounded-3xl border border-white/30 shadow-xl shadow-gray-100/50 overflow-hidden card-hover animate-fade-in-up opacity-0" style="animation-delay: {{ $positionIndex * 0.1 }}s">
                            <!-- Position Header -->
                            <div class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 px-8 py-6 relative overflow-hidden">
                                <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"30\" height=\"30\" viewBox=\"0 0 30 30\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cpath d=\"M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z\" fill=\"rgba(255,255,255,0.05)\"%3E%3C/path%3E%3C/svg%3E')] opacity-50"></div>
                            <div class="relative flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center border border-white/20">
                                        <span class="text-xl font-bold text-white">{{ $positionIndex + 1 }}</span>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-white">{{ $position->name }}</h3>
                                        <p class="text-white/60 text-sm mt-0.5">{{ $position->candidates->count() }} candidates competing</p>
                                    </div>
                                </div>
                                <span class="px-4 py-2 bg-white/10 text-white text-sm font-medium rounded-full border border-white/20">
                                {{ $position->max_votes ?? 1 }} {{ ($position->max_votes ?? 1) > 1 ? 'votes' : 'vote' }} max
                            </span>
                            </div>
                        </div>

                        <div class="p-8">
                            <div class="space-y-6">
                                @php
                                    $candidates = $position->candidates->sortByDesc('votes_count');
                                    $maxVotes = $candidates->max('votes_count') ?: 1;
                                    $totalPositionVotes = $candidates->sum('votes_count') ?: 1;
                                @endphp

                                @foreach($candidates as $index => $candidate)
                                    @php
                                        $percentage = ($candidate->votes_count / $totalPositionVotes) * 100;
                                        $isLeading = $index === 0 && $candidate->votes_count > 0;
                                        $rank = $index + 1;
                                    @endphp
                                    <div class="candidate-card relative bg-gradient-to-r {{ $isLeading ? 'from-emerald-50 to-teal-50 border-emerald-200' : 'from-gray-50 to-slate-50 border-gray-200' }} rounded-2xl p-6 border-2 transition-all duration-300">
                                        <!-- Rank Badge -->
                                        <div class="absolute -top-3 -left-3 w-8 h-8 {{ $isLeading ? 'bg-gradient-to-br from-amber-400 to-orange-500' : 'bg-gradient-to-br from-gray-400 to-gray-500' }} rounded-full flex items-center justify-center shadow-lg {{ $isLeading ? 'shadow-amber-500/30 crown-badge' : '' }}">
                                            @if($isLeading)
                                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @else
                                                <span class="text-xs font-bold text-white">{{ $rank }}</span>
                                            @endif
                                        </div>

                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-5">
                                                <!-- Candidate Photo -->
                                                <div class="relative group">
                                                    @if($candidate->photo)
                                                        <div class="w-20 h-20 rounded-2xl overflow-hidden border-3 {{ $isLeading ? 'border-emerald-400 shadow-lg shadow-emerald-500/30' : 'border-gray-300' }} transition-all duration-300 group-hover:scale-105">
                                                            <img src="{{ asset('storage/' . $candidate->photo) }}"
                                                                 alt="{{ $candidate->name }}"
                                                                 class="w-full h-full object-cover">
                                                        </div>
                                                    @else
                                                        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br {{ $isLeading ? 'from-emerald-400 to-teal-500' : 'from-gray-300 to-gray-400' }} flex items-center justify-center shadow-lg group-hover:scale-105 transition-all duration-300">
                                                            <span class="text-2xl font-bold text-white">{{ substr($candidate->name, 0, 1) }}</span>
                                                        </div>
                                                    @endif
                                                    @if($isLeading)
                                                        <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 px-3 py-1 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full shadow-lg">
                                                            <span class="text-xs font-bold text-white">LEADING</span>
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Candidate Info -->
                                                <div>
                                                    <h4 class="text-xl font-bold text-gray-900">{{ $candidate->name }}</h4>
                                                    @if($candidate->partylist)
                                                        <div class="flex items-center mt-1.5">
                                                            <div class="w-3 h-3 rounded-full bg-gradient-to-r from-violet-500 to-purple-500 mr-2"></div>
                                                            <span class="text-sm font-medium text-gray-600">{{ $candidate->partylist->name }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Vote Count -->
                                            <div class="text-right">
                                                <p class="text-4xl font-extrabold {{ $isLeading ? 'text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-600' : 'text-gray-700' }}">
                                                    {{ number_format($candidate->votes_count ?? 0) }}
                                                </p>
                                                <p class="text-sm font-medium {{ $isLeading ? 'text-emerald-600' : 'text-gray-500' }} mt-1">
                                                    {{ number_format($percentage, 1) }}% of votes
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Progress Bar -->
                                        <div class="mt-6">
                                            <div class="h-3 bg-gray-200 rounded-full overflow-hidden">
                                                <div class="progress-bar h-full rounded-full {{ $isLeading ? 'bg-gradient-to-r from-emerald-400 via-teal-500 to-emerald-600 animate-gradient' : 'bg-gradient-to-r from-gray-400 to-gray-500' }}"
                                                     style="width: 0%"
                                                     data-width="{{ $percentage }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                </div>
                @empty
                    <div class="glass rounded-3xl border border-white/30 shadow-xl p-16 text-center animate-fade-in-up">
                        <div class="w-24 h-24 mx-auto bg-gradient-to-br from-gray-200 to-gray-300 rounded-3xl flex items-center justify-center mb-6">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">No Results Available</h3>
                        <p class="text-gray-500 text-lg">Results will appear here once voting begins.</p>
                    </div>
            @endforelse
        </div>
        </section>

        <!-- Partylist & Candidates Section -->
        <section class="mb-16">
            <div class="text-center mb-12 animate-fade-in-up">
                <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-violet-100 to-purple-100 rounded-full mb-4">
                    <svg class="w-5 h-5 text-violet-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="text-sm font-semibold text-violet-700">Meet the Candidates</span>
                </div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Partylists & Candidates</h2>
                <p class="text-gray-500 text-lg max-w-2xl mx-auto">Discover the partylists, their vision, and the candidates running for office.</p>
            </div>

            <div class="grid lg:grid-cols-2 gap-10">
                @forelse($partylists ?? [] as $partylistIndex => $partylist)
                    <div class="glass rounded-3xl border border-white/30 shadow-xl overflow-hidden card-hover animate-fade-in-up opacity-0" style="animation-delay: {{ $partylistIndex * 0.15 }}s">
                        <!-- Partylist Header -->
                        <div class="relative bg-gradient-to-br from-gray-900 via-slate-800 to-gray-900 px-8 py-10 text-white overflow-hidden">
                            <!-- Decorative Elements -->
                            <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-white/10 to-transparent rounded-full blur-2xl transform translate-x-1/2 -translate-y-1/2"></div>
                            <div class="absolute bottom-0 left-0 w-32 h-32 bg-gradient-to-tr from-emerald-500/20 to-transparent rounded-full blur-2xl transform -translate-x-1/2 translate-y-1/2"></div>

                            <div class="relative flex items-center space-x-5">
                                @if($partylist->logo)
                                    <div class="w-24 h-24 rounded-2xl overflow-hidden border-2 border-white/20 shadow-2xl transform hover:scale-105 transition-transform duration-300">
                                        <img src="{{ asset('storage/' . $partylist->logo) }}"
                                             alt="{{ $partylist->name }}"
                                             class="w-full h-full object-cover">
                                    </div>
                                @else
                                    <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-white/20 to-white/5 flex items-center justify-center border-2 border-white/20 shadow-2xl">
                                        <span class="text-3xl font-bold">{{ substr($partylist->name, 0, 2) }}</span>
                                    </div>
                                @endif
                                <div>
                                    <h3 class="text-2xl font-bold">{{ $partylist->name }}</h3>
                                    @if($partylist->acronym)
                                        <p class="text-white/60 text-sm font-medium mt-1">{{ $partylist->acronym }}</p>
                                    @endif
                                    <div class="flex items-center mt-3 space-x-4">
                                    <span class="flex items-center text-sm text-white/70">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        {{ $partylist->candidates->count() ?? 0 }} Candidates
                                    </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Partylist Platform -->
                        @if($partylist->platform)
                            <div class="px-8 py-6 bg-gradient-to-r from-emerald-50 via-teal-50 to-cyan-50 border-b border-emerald-100/50">
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-2">Party Platform</h4>
                                        <p class="text-gray-700 leading-relaxed">{{ Str::limit($partylist->platform, 200) }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Candidates List -->
                        <div class="p-8">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6 flex items-center">
                                <span class="w-8 h-px bg-gray-300 mr-3"></span>
                                Candidates
                                <span class="w-8 h-px bg-gray-300 ml-3"></span>
                            </h4>
                            <div class="space-y-5">
                                @forelse($partylist->candidates ?? [] as $candidate)
                                    <div class="group relative bg-gradient-to-r from-gray-50 to-slate-50 rounded-2xl p-5 border border-gray-100 hover:border-emerald-200 hover:from-emerald-50 hover:to-teal-50 transition-all duration-300 cursor-pointer">
                                        <div class="flex items-start space-x-4">
                                            <!-- Candidate Photo -->
                                            <div class="flex-shrink-0">
                                                @if($candidate->photo)
                                                    <div class="w-20 h-20 rounded-xl overflow-hidden border-2 border-gray-200 group-hover:border-emerald-300 shadow-lg group-hover:shadow-emerald-500/20 transition-all duration-300 group-hover:scale-105">
                                                        <img src="{{ asset('storage/' . $candidate->photo) }}"
                                                             alt="{{ $candidate->name }}"
                                                             class="w-full h-full object-cover">
                                                    </div>
                                                @else
                                                    <div class="w-20 h-20 rounded-xl bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center shadow-lg group-hover:from-emerald-400 group-hover:to-teal-500 transition-all duration-300 group-hover:scale-105">
                                                        <span class="text-2xl font-bold text-white">{{ substr($candidate->name, 0, 1) }}</span>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Candidate Info -->
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-start justify-between">
                                                    <div>
                                                        <h5 class="text-lg font-bold text-gray-900 group-hover:text-emerald-700 transition-colors">{{ $candidate->name }}</h5>
                                                        <div class="mt-2">
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-500/30">
                                                        {{ $candidate->position->name ?? 'Position' }}
                                                    </span>
                                                        </div>
                                                    </div>
                                                    @if(isset($candidate->votes_count))
                                                        <div class="text-right bg-white px-4 py-2 rounded-xl shadow-sm border border-gray-100">
                                                            <span class="text-2xl font-extrabold text-gray-900">{{ number_format($candidate->votes_count) }}</span>
                                                            <p class="text-xs font-medium text-gray-500">votes</p>
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Candidate Platform -->
                                                @if($candidate->platform)
                                                    <div class="mt-4 p-4 bg-white rounded-xl border border-gray-100 shadow-sm">
                                                        <p class="text-xs font-bold text-emerald-600 uppercase tracking-wider mb-2 flex items-center">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                                            </svg>
                                                            Platform
                                                        </p>
                                                        <p class="text-sm text-gray-700 leading-relaxed">{{ Str::limit($candidate->platform, 150) }}</p>
                                                    </div>
                                                @endif

                                                <!-- Additional Info -->
                                                @if($candidate->course || $candidate->year_level || $candidate->motto)
                                                    <div class="mt-4 flex flex-wrap gap-2">
                                                        @if($candidate->course)
                                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                                <svg class="w-3 h-3 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                </svg>
                                                {{ $candidate->course }}
                                            </span>
                                                        @endif
                                                        @if($candidate->year_level)
                                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                                Year {{ $candidate->year_level }}
                                            </span>
                                                        @endif
                                                    </div>
                                                    @if($candidate->motto)
                                                        <p class="mt-3 text-sm italic text-gray-500 border-l-2 border-emerald-300 pl-3">"{{ $candidate->motto }}"</p>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-10 text-gray-500">
                                        <div class="w-16 h-16 mx-auto bg-gray-100 rounded-2xl flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        </div>
                                        <p class="font-medium">No candidates registered</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="lg:col-span-2 glass rounded-3xl border border-white/30 shadow-xl p-16 text-center animate-fade-in-up">
                        <div class="w-24 h-24 mx-auto bg-gradient-to-br from-gray-200 to-gray-300 rounded-3xl flex items-center justify-center mb-6">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">No Partylists Available</h3>
                        <p class="text-gray-500 text-lg">Partylist information will appear here once registered.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Back Button -->
        <div class="text-center animate-fade-in-up">
            <a href="{{ route('voter.welcome', $election->code) }}"
               class="group inline-flex items-center px-8 py-4 bg-gradient-to-r from-gray-900 to-gray-800 text-white font-semibold rounded-2xl hover:from-gray-800 hover:to-gray-700 transition-all duration-300 shadow-xl shadow-gray-900/20 hover:shadow-2xl hover:shadow-gray-900/30 hover:-translate-y-1">
                <svg class="w-5 h-5 mr-3 transform group-hover:-translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Election
            </a>
        </div>
    </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Animate progress bars on load
                setTimeout(() => {
                    document.querySelectorAll('.progress-bar').forEach(bar => {
                        bar.style.width = bar.dataset.width;
                    });
                }, 500);

                // Trigger fade-in animations
                document.querySelectorAll('.animate-fade-in-up').forEach((el, index) => {
                    el.style.opacity = '1';
                });
            });

            // Auto-refresh results every 30 seconds
            setInterval(function() {
                location.reload();
            }, 30000);

            // Update timestamp every second
            function updateTimestamp() {
                const now = new Date();
                const timeString = now.toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
                document.getElementById('last-updated').textContent = timeString;
            }

            setInterval(updateTimestamp, 1000);
            updateTimestamp();
        </script>
    @endpush
@endsection
