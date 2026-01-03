@extends('voter.layouts.app')

@section('title', 'Welcome - SecureVote PH')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-gray-100 to-slate-100 relative overflow-hidden">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-purple-300/30 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-blue-300/30 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
            <div class="absolute top-1/3 right-1/4 w-64 h-64 bg-emerald-300/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-gradient-to-r from-purple-200/20 via-blue-200/20 to-emerald-200/20 rounded-full blur-3xl"></div>
        </div>

        <!-- Grid Pattern Overlay -->
        <div class="absolute inset-0 opacity-[0.03]" style="background-image: linear-gradient(rgba(0,0,0,0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(0,0,0,0.1) 1px, transparent 1px); background-size: 60px 60px;"></div>

        <!-- Floating Particles -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-20 left-[10%] w-2 h-2 bg-purple-400/40 rounded-full animate-float"></div>
            <div class="absolute top-40 right-[15%] w-3 h-3 bg-blue-400/40 rounded-full animate-float" style="animation-delay: 0.5s;"></div>
            <div class="absolute top-60 left-[20%] w-2 h-2 bg-emerald-400/40 rounded-full animate-float" style="animation-delay: 1s;"></div>
            <div class="absolute bottom-40 right-[25%] w-2 h-2 bg-purple-400/40 rounded-full animate-float" style="animation-delay: 1.5s;"></div>
            <div class="absolute bottom-60 left-[30%] w-3 h-3 bg-blue-400/40 rounded-full animate-float" style="animation-delay: 2s;"></div>
        </div>

        <div class="relative z-10 min-h-screen flex flex-col">
            <!-- Header -->
            <header class="py-6 px-4 backdrop-blur-md bg-white/60 border-b border-gray-200/50 shadow-sm">
                <div class="max-w-7xl mx-auto flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="relative group">
                            <div class="absolute inset-0 bg-gradient-to-r from-purple-500 via-blue-500 to-emerald-500 rounded-full blur-lg opacity-40 group-hover:opacity-70 transition-opacity"></div>
                            <div class="relative w-14 h-14 rounded-full bg-white flex items-center justify-center shadow-xl ring-2 ring-white/50 overflow-hidden">
                                <img src="{{ asset('images/logo.png') }}" alt="SecureVote Logo" class="w-full h-full object-cover">
                            </div>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold bg-gradient-to-r from-purple-700 via-blue-700 to-emerald-700 bg-clip-text text-transparent tracking-tight">SecureVote</h1>
                            <p class="text-sm text-gray-500 font-medium">Philippines</p>
                        </div>
                    </div>
                    <nav class="flex items-center gap-4">
                        <a href="{{ route('home') }}" class="group flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white/80 hover:bg-white border border-gray-200 hover:border-purple-300 shadow-sm hover:shadow-md transition-all duration-300">
                            <i class="fas fa-home text-purple-500 group-hover:text-purple-600 transition-colors"></i>
                            <span class="text-gray-600 group-hover:text-gray-800 transition-colors font-medium hidden sm:inline">Home</span>
                        </a>
                    </nav>
                </div>
            </header>

            <!-- Main Content -->
            <div class="flex-grow flex items-center justify-center px-4 py-16">
                <div class="max-w-5xl mx-auto text-center">
                    <!-- Quote Badge -->
                    <div class="inline-flex items-center gap-3 px-5 py-2.5 rounded-full bg-white/80 border border-gray-200 shadow-lg mb-10 backdrop-blur-md">
                        <div class="relative w-8 h-8 rounded-full overflow-hidden ring-2 ring-purple-200">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-full object-cover">
                        </div>
                        <span class="text-sm text-gray-600 font-medium italic">"Your vote is your voice—use it wisely to shape a better future."</span>
                    </div>

                    <!-- Hero Icon with Multi-Color Glow Effect -->
                    <div class="relative inline-block mb-12">
                        <div class="absolute inset-0 bg-gradient-to-r from-purple-500 via-blue-500 to-emerald-500 rounded-full blur-2xl opacity-40 animate-pulse scale-110"></div>
                        <div class="absolute inset-0 bg-gradient-to-r from-purple-400 via-blue-400 to-emerald-400 rounded-full blur-xl opacity-30 animate-pulse scale-105" style="animation-delay: 0.5s;"></div>
                        <div class="relative w-36 h-36 bg-white rounded-full flex items-center justify-center shadow-2xl transform hover:scale-105 hover:rotate-3 transition-all duration-500 ring-4 ring-white/50 overflow-hidden">
                            <img src="{{ asset('images/logo.png') }}" alt="SecureVote Logo" class="w-28 h-28 object-cover rounded-full">
                        </div>
                        <!-- Orbiting Elements -->
                        <div class="absolute -top-2 -right-2 w-8 h-8 bg-gradient-to-br from-purple-400 to-purple-600 rounded-lg flex items-center justify-center shadow-lg animate-bounce-slow">
                            <i class="fas fa-shield-alt text-white text-xs"></i>
                        </div>
                        <div class="absolute -bottom-2 -left-2 w-8 h-8 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-lg flex items-center justify-center shadow-lg animate-bounce-slow" style="animation-delay: 0.3s;">
                            <i class="fas fa-check text-white text-xs"></i>
                        </div>
                    </div>

                    <!-- Election Code Badge -->
                    @if(session('election_code'))
                        <div class="inline-block px-4 py-2 bg-purple-100 text-purple-700 rounded-full text-sm font-semibold mb-4">
                            <i class="fas fa-hashtag mr-1"></i>{{ session('election_code') }}
                        </div>
                    @endif

                    <!-- Headline -->
                    <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold text-gray-800 mb-6 leading-tight tracking-tight">
                        <span class="block text-transparent bg-clip-text bg-gradient-to-r from-purple-600 via-blue-600 to-emerald-600 animate-gradient">
                            {{ $election->title ?? session('election_title', 'Election Title') }}
                        </span>
                    </h1>

                    <p class="text-xl md:text-2xl text-gray-500 mb-10 max-w-2xl mx-auto leading-relaxed font-light">
                        {{ $election->description ?? session('election_description', 'Election description will appear here.') }}
                    </p>

                    <!-- Countdown Timer -->
                    <div class="mb-10">
                        <p class="text-gray-600 font-medium mb-4">Voting starts in:</p>
                        <div id="countdown" class="flex justify-center gap-4">
                            <div class="flex flex-col items-center p-4 rounded-2xl bg-white/80 border border-gray-100 shadow-lg backdrop-blur-md min-w-[80px]">
                                <span id="days" class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-purple-400 bg-clip-text text-transparent">00</span>
                                <span class="text-sm text-gray-500 mt-1">Days</span>
                            </div>
                            <div class="flex flex-col items-center p-4 rounded-2xl bg-white/80 border border-gray-100 shadow-lg backdrop-blur-md min-w-[80px]">
                                <span id="hours" class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-blue-400 bg-clip-text text-transparent">00</span>
                                <span class="text-sm text-gray-500 mt-1">Hours</span>
                            </div>
                            <div class="flex flex-col items-center p-4 rounded-2xl bg-white/80 border border-gray-100 shadow-lg backdrop-blur-md min-w-[80px]">
                                <span id="minutes" class="text-3xl font-bold bg-gradient-to-r from-emerald-600 to-emerald-400 bg-clip-text text-transparent">00</span>
                                <span class="text-sm text-gray-500 mt-1">Minutes</span>
                            </div>
                            <div class="flex flex-col items-center p-4 rounded-2xl bg-white/80 border border-gray-100 shadow-lg backdrop-blur-md min-w-[80px]">
                                <span id="seconds" class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-emerald-400 bg-clip-text text-transparent">00</span>
                                <span class="text-sm text-gray-500 mt-1">Seconds</span>
                            </div>
                        </div>
                        <p id="countdown-status" class="text-emerald-600 font-semibold mt-4 hidden">Election is now open!</p>
                    </div>

                    <!-- CTA Button -->
                    <div class="flex justify-center mb-24">
                        @if(isset($election) && $election->id)
                            <a href="{{ route('voter.elections.show', $election->id) }}" id="vote-btn" class="group relative inline-flex items-center justify-center gap-3 px-10 py-5 overflow-hidden rounded-2xl font-bold text-lg transition-all duration-300 hover:scale-105 active:scale-95 shadow-xl hover:shadow-2xl">
                                @elseif(session('election_id'))
                                    <a href="{{ route('voter.elections.show', session('election_id')) }}" id="vote-btn" class="group relative inline-flex items-center justify-center gap-3 px-10 py-5 overflow-hidden rounded-2xl font-bold text-lg transition-all duration-300 hover:scale-105 active:scale-95 shadow-xl hover:shadow-2xl">
                                        @else
                                            <a href="{{ route('voter.elections.access') }}" id="vote-btn" class="group relative inline-flex items-center justify-center gap-3 px-10 py-5 overflow-hidden rounded-2xl font-bold text-lg transition-all duration-300 hover:scale-105 active:scale-95 shadow-xl hover:shadow-2xl">
                                                @endif
                                                <div class="absolute inset-0 bg-gradient-to-r from-purple-600 via-blue-600 to-emerald-600 bg-[length:200%_100%] animate-gradient-x"></div>
                                                <div class="absolute inset-0 bg-gradient-to-r from-purple-500 via-blue-500 to-emerald-500 opacity-0 group-hover:opacity-100 transition-opacity blur-xl"></div>
                                                <span class="relative flex items-center gap-3 text-white">
            <i class="fas fa-vote-yea text-xl"></i>
            <span>Start to Vote</span>
            <i class="fas fa-arrow-right text-sm group-hover:translate-x-2 transition-transform duration-300"></i>
        </span>
                                            </a>
                    </div>


                    <!-- Live Results Section -->
                    <div class="mb-24">
                        <div class="flex items-center justify-center gap-3 mb-8">
                            <div class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
                            <h2 class="text-2xl font-bold text-gray-800">Live Results</h2>
                        </div>

                        <div class="max-w-3xl mx-auto bg-white/80 backdrop-blur-xl rounded-3xl border border-gray-100 shadow-xl p-8">
                            @if(isset($election) && $election->candidates && $election->candidates->count() > 0)
                                <div class="space-y-6">
                                    @php
                                        $totalVotes = $election->candidates->sum('votes_count') ?: 1;
                                    @endphp

                                    @foreach($election->candidates->sortByDesc('votes_count') as $index => $candidate)
                                        @php
                                            $percentage = round(($candidate->votes_count / $totalVotes) * 100, 1);
                                            $colors = ['from-purple-500 to-purple-600', 'from-blue-500 to-blue-600', 'from-emerald-500 to-emerald-600', 'from-amber-500 to-amber-600', 'from-rose-500 to-rose-600'];
                                            $color = $colors[$index % count($colors)];
                                        @endphp

                                        <div class="group">
                                            <div class="flex items-center justify-between mb-2">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br {{ $color }} flex items-center justify-center text-white font-bold shadow-md">
                                                        {{ $index + 1 }}
                                                    </div>
                                                    <div class="text-left">
                                                        <h4 class="font-semibold text-gray-800">{{ $candidate->name }}</h4>
                                                        <p class="text-sm text-gray-500">{{ $candidate->position->name ?? 'Candidate' }}</p>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <span class="text-lg font-bold text-gray-800">{{ number_format($candidate->votes_count) }}</span>
                                                    <span class="text-sm text-gray-500 ml-1">votes</span>
                                                </div>
                                            </div>
                                            <div class="relative h-4 bg-gray-100 rounded-full overflow-hidden">
                                                <div class="absolute inset-y-0 left-0 bg-gradient-to-r {{ $color }} rounded-full transition-all duration-1000 ease-out" style="width: {{ $percentage }}%"></div>
                                            </div>
                                            <div class="text-right mt-1">
                                                <span class="text-sm font-medium text-gray-600">{{ $percentage }}%</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-between">
                                    <div class="flex items-center gap-2 text-gray-500">
                                        <i class="fas fa-users"></i>
                                        <span class="text-sm">Total Votes: <strong class="text-gray-800">{{ number_format($totalVotes) }}</strong></span>
                                    </div>
                                    <div class="flex items-center gap-2 text-gray-500">
                                        <i class="fas fa-sync-alt animate-spin-slow"></i>
                                        <span class="text-sm">Updates every 30 seconds</span>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-chart-bar text-gray-400 text-3xl"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-600 mb-2">No Results Yet</h3>
                                    <p class="text-gray-500">Results will appear here once voting begins.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Features Grid -->
                    <div id="features" class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- Feature 1 - Purple -->
                        <div class="group relative p-8 rounded-3xl bg-white/80 border border-gray-100 hover:border-purple-200 transition-all duration-500 hover:-translate-y-3 shadow-lg hover:shadow-2xl backdrop-blur-xl overflow-hidden">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-purple-100 to-transparent rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform duration-500"></div>
                            <div class="relative">
                                <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-700 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300 shadow-lg">
                                    <i class="fas fa-shield-alt text-white text-2xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800 mb-3">Bank-Level Security</h3>
                                <p class="text-gray-500 leading-relaxed">Military-grade 256-bit AES encryption protects every vote cast on our platform</p>
                            </div>
                        </div>

                        <!-- Feature 2 - Blue -->
                        <div class="group relative p-8 rounded-3xl bg-white/80 border border-gray-100 hover:border-blue-200 transition-all duration-500 hover:-translate-y-3 shadow-lg hover:shadow-2xl backdrop-blur-xl overflow-hidden">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-100 to-transparent rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform duration-500"></div>
                            <div class="relative">
                                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300 shadow-lg">
                                    <i class="fas fa-chart-line text-white text-2xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800 mb-3">Full Transparency</h3>
                                <p class="text-gray-500 leading-relaxed">Real-time auditable results with complete transparency and verifiable outcomes</p>
                            </div>
                        </div>

                        <!-- Feature 3 - Emerald -->
                        <div class="group relative p-8 rounded-3xl bg-white/80 border border-gray-100 hover:border-emerald-200 transition-all duration-500 hover:-translate-y-3 shadow-lg hover:shadow-2xl backdrop-blur-xl overflow-hidden">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-emerald-100 to-transparent rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform duration-500"></div>
                            <div class="relative">
                                <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300 shadow-lg">
                                    <i class="fas fa-globe text-white text-2xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800 mb-3">Vote Anywhere</h3>
                                <p class="text-gray-500 leading-relaxed">Seamlessly access and vote from any device, anywhere in the world, anytime</p>
                            </div>
                        </div>
                    </div>

                    <!-- How It Works Section -->
                    <div class="mt-24 mb-16">
                        <h2 class="text-3xl font-bold text-gray-800 mb-12">How It Works</h2>
                        <div class="flex flex-col md:flex-row items-center justify-center gap-8">
                            <div class="flex flex-col items-center">
                                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white font-bold text-xl shadow-lg mb-4">1</div>
                                <h4 class="font-semibold text-gray-700 mb-2">Enter Code</h4>
                                <p class="text-gray-500 text-sm max-w-[150px]">Use your unique election access code</p>
                            </div>
                            <div class="hidden md:block w-20 h-0.5 bg-gradient-to-r from-purple-300 to-blue-300"></div>
                            <div class="flex flex-col items-center">
                                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-xl shadow-lg mb-4">2</div>
                                <h4 class="font-semibold text-gray-700 mb-2">Verify Identity</h4>
                                <p class="text-gray-500 text-sm max-w-[150px]">Confirm your voter registration</p>
                            </div>
                            <div class="hidden md:block w-20 h-0.5 bg-gradient-to-r from-blue-300 to-emerald-300"></div>
                            <div class="flex flex-col items-center">
                                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white font-bold text-xl shadow-lg mb-4">3</div>
                                <h4 class="font-semibold text-gray-700 mb-2">Cast Your Vote</h4>
                                <p class="text-gray-500 text-sm max-w-[150px]">Select candidates securely</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="py-8 px-4 bg-white/60 border-t border-gray-200/50 backdrop-blur-md">
                <div class="max-w-7xl mx-auto">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center overflow-hidden shadow-md">
                                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-full object-cover">
                            </div>
                            <span class="font-semibold bg-gradient-to-r from-purple-600 to-emerald-600 bg-clip-text text-transparent">SecureVote PH</span>
                        </div>
                        <p class="text-gray-400 text-sm">&copy; {{ date('Y') }} SecureVote Philippines. All rights reserved.</p>
                        <div class="flex items-center gap-4">
                            <a href="#" class="text-gray-400 hover:text-purple-600 transition-colors"><i class="fab fa-facebook text-lg"></i></a>
                            <a href="#" class="text-gray-400 hover:text-blue-500 transition-colors"><i class="fab fa-twitter text-lg"></i></a>
                            <a href="#" class="text-gray-400 hover:text-emerald-600 transition-colors"><i class="fab fa-instagram text-lg"></i></a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <style>
        @keyframes gradient-x {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        .animate-gradient-x {
            animation: gradient-x 3s ease infinite;
        }
        @keyframes gradient {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        .animate-gradient {
            background-size: 200% 200%;
            animation: gradient 4s ease infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes bounce-slow {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }
        .animate-bounce-slow {
            animation: bounce-slow 2s ease-in-out infinite;
        }
        .animate-spin-slow {
            animation: spin 3s linear infinite;
        }
    </style>

    <script>
        // Countdown Timer - using voting_start from election or session
        const votingStartDate = new Date("{{ $election->voting_start ?? session('election_voting_start', now()) }}").getTime();

        const countdown = setInterval(function() {
            const now = new Date().getTime();
            const distance = votingStartDate - now;

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById("days").textContent = days.toString().padStart(2, '0');
            document.getElementById("hours").textContent = hours.toString().padStart(2, '0');
            document.getElementById("minutes").textContent = minutes.toString().padStart(2, '0');
            document.getElementById("seconds").textContent = seconds.toString().padStart(2, '0');

            if (distance < 0) {
                clearInterval(countdown);
                document.getElementById("countdown").classList.add("hidden");
                document.getElementById("countdown-status").classList.remove("hidden");
            }
        }, 1000);
    </script>
@endsection
