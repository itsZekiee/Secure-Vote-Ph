@extends('voter.layouts.app')

@section('title', $election->title . ' - SecureVote PH')

@push('styles')
    <style>
        :root {
            --brand-primary: #003153;
            --brand-accent: #00D4AA;
            --brand-secondary: #008080;
        }

        .gradient-brand {
            background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-accent) 100%);
        }

        .text-brand-primary {
            color: var(--brand-primary);
        }

        .text-brand-accent {
            color: var(--brand-accent);
        }

        .btn-brand {
            background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-accent) 100%);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .btn-brand:hover:not(:disabled) {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 25px 50px -12px rgba(0, 212, 170, 0.5);
        }

        .btn-brand:disabled {
            background: #cbd5e1 !important;
            color: #64748b !important;
            box-shadow: none !important;
            transform: none !important;
            cursor: not-allowed !important;
        }

        .btn-brand.btn-active {
            background: linear-gradient(135deg, var(--brand-accent) 0%, var(--brand-secondary) 100%);
            box-shadow: 0 15px 30px -10px rgba(0, 212, 170, 0.4);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .countdown-item {
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.25);
        }

        .countdown-item div:first-child {
            color: #ffffff;
        }

        .countdown-item:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }

        @keyframes pulse-ring {
            0% {
                transform: scale(.95);
                box-shadow: 0 0 0 0 rgba(0, 212, 170, 0.7);
            }

            70% {
                transform: scale(1);
                box-shadow: 0 0 0 15px rgba(0, 212, 170, 0);
            }

            100% {
                transform: scale(.95);
                box-shadow: 0 0 0 0 rgba(0, 212, 170, 0);
            }
        }

        .btn-active-pulse {
            animation: pulse-ring 2s infinite;
        }

        .stat-card {
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-8px);
            background: white !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
        }

        #countdown .countdown-item {
            background: rgba(0, 0, 0, 0.35) !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
        }

        #countdown .countdown-item div:first-child {
            color: #ffffff !important;
        }

        #countdown .countdown-item div:last-child {
            color: rgba(255, 255, 255, 0.8) !important;
        }

        /* Seconds box */
        #countdown .countdown-item.active,
        #countdown .bg-brand-accent {
            background: linear-gradient(135deg,
                    #00d4aa,
                    #00bfa5) !important;
        }
    </style>
@endpush

@section('content')
    <div class="min-h-screen bg-[#F5F5F5] py-12 px-4 relative overflow-hidden">
        <!-- Decoration -->
        <div class="absolute top-0 left-0 w-full h-64 bg-gradient-to-b from-brand-primary/5 to-transparent"></div>

        <div class="max-w-4xl mx-auto relative z-10">
            <!-- Header -->
            <div class="text-center mb-12">
                <div
                    class="inline-flex items-center px-4 py-2 bg-white rounded-full shadow-sm mb-6 border border-slate-100">
                    <span class="w-2 h-2 bg-brand-accent rounded-full mr-2 animate-pulse"></span>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Election Portal</span>
                </div>
                <h1 class="text-4xl lg:text-5xl font-black text-brand-primary mb-4 tracking-tight">{{ $election->title }}
                </h1>
                <p class="text-slate-500 text-lg">Official Digital Ballot for Election Code: <span
                        class="font-mono font-bold text-brand-accent">{{ $election->code }}</span></p>
            </div>

            <!-- Main Content Card -->
            <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200/50 overflow-hidden border border-white">
                <div class="p-8 lg:p-12">
                    <div class="flex flex-col md:flex-row items-center gap-8 mb-12">
                        <div
                            class="w-24 h-24 bg-brand-primary/5 rounded-3xl flex items-center justify-center border border-brand-primary/10 shadow-inner group">
                            <i class="fas fa-user-check text-brand-primary text-4xl group-hover:scale-110 transition-transform"></i>
                        </div>
                        <div class="text-center md:text-left">
                            <h2 class="text-2xl lg:text-3xl font-bold text-brand-primary mb-1">Welcome back,
                                {{ $voter['name'] }}!
                            </h2>
                            <p class="text-slate-400">Your identity has been verified. You are authorized to participate in
                                this election.</p>
                        </div>
                        @if(!$hasVoted)
                            <div class="ml-auto">
                                <span
                                    class="px-4 py-2 bg-emerald-50 text-emerald-600 rounded-xl text-sm font-bold border border-emerald-100">
                                    <i class="fas fa-shield-check mr-1"></i> Ready to Vote
                                </span>
                            </div>
                        @endif
                    </div>

                    @if($election->description)
                        <div class="bg-slate-50 rounded-[2rem] p-8 mb-10 border border-slate-100">
                            <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Election Overview</h3>
                            <p class="text-slate-600 text-lg leading-relaxed">{{ $election->description }}</p>
                        </div>
                    @endif

                    <!-- Progress Bar -->
                    <div class="mb-10">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Election
                                Progress</span>
                            <span id="progress-text" class="text-xs font-bold text-brand-primary">0%</span>
                        </div>
                        <div class="w-full bg-slate-100 h-3 rounded-full overflow-hidden border border-slate-200 p-0.5">
                            <div id="progress-bar"
                                class="h-full bg-gradient-to-r from-brand-primary to-brand-accent rounded-full transition-all duration-1000"
                                style="width: 0%"></div>
                        </div>
                    </div>

                    <!-- Prominent Countdown Section -->
                    <div
                        class="bg-brand-primary rounded-[2.5rem] p-10 lg:p-14 text-white shadow-2xl shadow-brand-primary/30 mb-12 relative overflow-hidden">
                        <!-- Abstract Background -->
                        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32 blur-3xl"></div>
                        <div
                            class="absolute bottom-0 left-0 w-64 h-64 bg-brand-accent/10 rounded-full -ml-32 -mb-32 blur-3xl">
                        </div>

                        <div class="relative z-10">
                            <h3 class="text-center text-white/70 font-bold uppercase tracking-[0.3em] text-xs mb-8">
                                <span id="countdown-label">
                                    @if(now()->lt($election->start_date))
                                        Election Commences In
                                    @elseif(now()->gt($election->end_date))
                                        Election Concluded
                                    @else
                                        Voting Period Ends In
                                    @endif
                                </span>
                            </h3>

                            <div id="countdown" class="grid grid-cols-2 md:grid-cols-4 gap-6 lg:gap-8">
                                <div class="relative group">
                                    <div class="countdown-item rounded-3xl p-6 lg:p-8 transition-transform hover:scale-105">
                                        <div id="days" class="text-4xl lg:text-6xl font-black mb-1">00</div>
                                        <div
                                            class="text-[10px] lg:text-xs font-bold uppercase tracking-widest text-white/50">
                                            Days</div>
                                    </div>
                                </div>
                                <div class="relative group">
                                    <div class="countdown-item rounded-3xl p-6 lg:p-8 transition-transform hover:scale-105">
                                        <div id="hours" class="text-4xl lg:text-6xl font-black mb-1">00</div>
                                        <div
                                            class="text-[10px] lg:text-xs font-bold uppercase tracking-widest text-white/50">
                                            Hours</div>
                                    </div>
                                </div>
                                <div class="relative group">
                                    <div class="countdown-item rounded-3xl p-6 lg:p-8 transition-transform hover:scale-105">
                                        <div id="minutes" class="text-4xl lg:text-6xl font-black mb-1">00</div>
                                        <div
                                            class="text-[10px] lg:text-xs font-bold uppercase tracking-widest text-white/50">
                                            Mins</div>
                                    </div>
                                </div>
                                <div class="relative group">
                                    <div
                                        class="countdown-item countdown-active rounded-3xl p-6 lg:p-8 transition-transform hover:scale-105">
                                        <div id="seconds" class="text-4xl lg:text-6xl font-black mb-1">00</div>
                                        <div
                                            class="text-[10px] lg:text-xs font-bold uppercase tracking-widest text-black/70">
                                            Secs</div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-6">
                        @if($hasVoted)
                            <div
                                class="flex-1 py-6 bg-emerald-50 text-emerald-600 font-black rounded-3xl text-center border-2 border-emerald-100 flex items-center justify-center gap-3">
                                <i class="fas fa-check-circle text-2xl"></i>
                                <span>VOTE SUBMITTED</span>
                            </div>
                        @else
                            <a href="{{ route('voter.elections.vote', $election->code) }}"
                                id="voteBtn"
                                class="flex-1 py-6 btn-brand text-white font-black rounded-3xl text-center shadow-xl shadow-brand-primary/20 transition-all flex items-center justify-center gap-3">
                                <i class="fas fa-vote-yea text-xl"></i>
                                <span id="voteBtnText">
                                    @if(now()->lt($election->start_date))
                                        WAITING TO OPEN
                                    @elseif(now()->gt($election->end_date))
                                        ELECTION CLOSED
                                    @else
                                        START VOTING NOW
                                    @endif
                                </span>
                            </a>
                        @endif

                        <a href="{{ route('voter.elections.results', $election->code) }}"
                            class="flex-1 py-6 bg-white text-brand-primary font-black rounded-3xl text-center border-2 border-slate-100 hover:border-brand-primary hover:bg-slate-50 transition-all flex items-center justify-center gap-3">
                            <i class="fas fa-chart-pie text-xl text-brand-accent"></i>
                            LIVE RESULTS
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mt-12 mb-12">
                <div
                    class="stat-card bg-white/60 backdrop-blur rounded-[2rem] p-8 border border-white shadow-sm text-center transition-all">
                    <div
                        class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-indigo-500">
                        <i class="fas fa-calendar-alt text-xl"></i>
                    </div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Start Time</p>
                    <p class="font-bold text-brand-primary">{{ $election->start_date->format('M d, Y') }}</p>
                    <p class="text-xs text-slate-500">{{ $election->start_date->format('h:i A') }}</p>
                </div>
                <div
                    class="stat-card bg-white/60 backdrop-blur rounded-[2rem] p-8 border border-white shadow-sm text-center transition-all">
                    <div
                        class="w-12 h-12 bg-orange-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-orange-500">
                        <i class="fas fa-calendar-check text-xl"></i>
                    </div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">End Time</p>
                    <p class="font-bold text-brand-primary">{{ $election->end_date->format('M d, Y') }}</p>
                    <p class="text-xs text-slate-500">{{ $election->end_date->format('h:i A') }}</p>
                </div>
                <div
                    class="stat-card bg-white/60 backdrop-blur rounded-[2rem] p-8 border border-white shadow-sm text-center transition-all">
                    <div
                        class="w-12 h-12 bg-brand-accent/10 rounded-2xl flex items-center justify-center mx-auto mb-4 text-brand-accent">
                        <i class="fas fa-list-ul text-xl"></i>
                    </div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Ballot Content</p>
                    <p class="font-bold text-brand-primary">{{ $election->positions()->count() }} Positions</p>
                    <p class="text-xs text-slate-500">{{ $election->candidates()->count() }} Candidates</p>
                </div>
            </div>

            <div class="text-center">
                <a href="{{ route('voter.elections.access') }}"
                    class="inline-flex items-center gap-2 text-slate-400 font-bold hover:text-brand-primary transition-colors py-2 px-4 rounded-xl hover:bg-white shadow-none hover:shadow-sm">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Exit Portal</span>
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Synchronize server time
        const serverTime = {{ now()->timestamp * 1000 }};
        const localTimeAtServerTime = new Date().getTime();
        const timeOffset = serverTime - localTimeAtServerTime;

        const startDate = {{ $election->start_date->timestamp * 1000 }};
        const endDate = {{ $election->end_date->timestamp * 1000 }};
        const hasVoted = {{ $hasVoted ? 'true' : 'false' }};

        function updateCountdown() {
            const now = new Date().getTime() + timeOffset;
            let targetDate, label, isBeforeStart, isEnded;

            if (now < startDate) {
                targetDate = startDate;
                label = "Election Commences In";
                isBeforeStart = true;
                isEnded = false;
            } else if (now < endDate) {
                targetDate = endDate;
                label = "Voting Period Ends In";
                isBeforeStart = false;
                isEnded = false;
            } else {
                targetDate = now;
                label = "Election Concluded";
                isBeforeStart = false;
                isEnded = true;
            }

            const distance = Math.max(0, targetDate - now);

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById('days').textContent = String(days).padStart(2, '0');
            document.getElementById('hours').textContent = String(hours).padStart(2, '0');
            document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
            document.getElementById('seconds').textContent = String(seconds).padStart(2, '0');
            document.getElementById('countdown-label').textContent = label;

            // Update Progress Bar
            const totalDuration = endDate - startDate;
            const elapsed = now - startDate;
            let progress = 0;
            if (now >= startDate && now < endDate) {
                progress = (elapsed / totalDuration) * 100;
            } else if (now >= endDate) {
                progress = 100;
            }
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');
            if (progressBar) progressBar.style.width = progress + '%';
            if (progressText) progressText.textContent = Math.round(progress) + '%';

// Update button status
if (!hasVoted) {
    const voteBtn = document.getElementById('voteBtn');
    const voteBtnText = document.getElementById('voteBtnText');

    // RESET STATE
    voteBtn.classList.remove(
        'pointer-events-none',
        'opacity-50',
        'cursor-not-allowed',
        'btn-active-pulse'
    );
    voteBtn.disabled = false;

    if (isBeforeStart) {
        voteBtn.classList.add('pointer-events-none', 'cursor-not-allowed');
        voteBtn.classList.remove('btn-active');
        voteBtn.disabled = true;
        voteBtnText.textContent = 'WAITING TO OPEN';

    } else if (isEnded) {
        voteBtn.classList.add('pointer-events-none', 'cursor-not-allowed');
        voteBtn.classList.remove('btn-active');
        voteBtn.disabled = true;
        voteBtnText.textContent = 'ELECTION CLOSED';

    } else {
        voteBtn.classList.add('btn-active-pulse', 'btn-active');
        voteBtnText.textContent = 'START VOTING NOW';
    }
}

        }

        updateCountdown();
        const timer = setInterval(updateCountdown, 1000);
    </script>
@endpush
