@extends('voter.layouts.app')

@section('title', $election->title . ' - SecureVote PH')

@push('styles')
    <style>
        :root {
            --brand-primary: #003153;
            --brand-accent: #00D4AA;
            --brand-accent-dark: #00B38F;
            --brand-text-dark: #003153;
            --brand-text-muted: #64748b;
            --brand-bg: #f8fafc;
            --card-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #fff;
        }

        .btn-start-voting {
            background: linear-gradient(135deg, #003153 0%, #00D4AA 100%);
            color: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-start-voting:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px -10px rgba(0, 212, 170, 0.5);
            color: white;
        }

        .btn-view-results {
            border: 2px solid #003153;
            color: #003153;
            transition: all 0.3s ease;
        }

        .btn-view-results:hover {
            background-color: #003153;
            color: white;
        }

        .countdown-card {
            background: white;
            border-radius: 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 49, 83, 0.15);
            border: 1px solid rgba(0, 49, 83, 0.05);
        }

        .countdown-box {
            background: linear-gradient(135deg, rgba(0, 212, 170, 0.1) 0%, rgba(0, 49, 83, 0.05) 100%);
            border-radius: 1.5rem;
            padding: 1.5rem 1rem;
            min-width: 80px;
            border: 1px solid rgba(0, 212, 170, 0.1);
        }

        .stat-card-small {
            background: white;
            border: 1px solid #f1f5f9;
            border-radius: 2rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .stat-card-small:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
            border-color: rgba(0, 212, 170, 0.2);
        }

        .feature-card {
            background: white;
            border: 1px solid #f1f5f9;
            border-radius: 2.5rem;
            padding: 2.5rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 30px 60px -12px rgba(0, 49, 83, 0.12);
            border-color: rgba(0, 212, 170, 0.3);
        }

        .icon-box {
            width: 56px;
            height: 56px;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .icon-teal {
            background: linear-gradient(135deg, #003153 0%, #00D4AA 100%);
            color: white;
        }

        .icon-dark-teal {
            background-color: #003153;
            color: white;
        }

        .gradient-text {
            background: linear-gradient(135deg, #003153 0%, #00D4AA 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
@endpush

@section('content')
    <div class="min-h-screen bg-white">
        <!-- Header -->
        <header class="border-b border-slate-100 py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-[#003153] to-[#00D4AA] rounded-xl flex items-center justify-center text-white shadow-lg shadow-brand-accent/20">
                        <i class="fas fa-vote-yea text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-black text-[#003153] tracking-tight">SecureVote</h1>
                        <p class="text-[10px] font-black text-[#00D4AA] uppercase tracking-[0.2em]">Digital Ballot System</p>
                    </div>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="flex flex-col lg:flex-row gap-16 items-start">
                <!-- Left Content -->
                <div class="flex-1">
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-[#00D4AA]/10 text-[#003153] rounded-full text-sm font-bold mb-8">
                        <i class="far fa-calendar-alt text-[#00D4AA]"></i>
                        {{ $election->start_date->format('Y') }} General Election
                    </div>

                    <h2 class="text-5xl lg:text-6xl font-extrabold text-[#003153] mb-8 leading-[1.1] tracking-tight">
                        {{ $election->title }}
                    </h2>

                    <p class="text-slate-500 text-lg mb-12 max-w-2xl leading-relaxed font-medium">
                        {{ $election->description ?? 'Exercise your democratic right and make your voice heard. This election will determine the leadership for the next four years. Your vote matters in shaping our collective future.' }}
                    </p>

                    <!-- Stats Cards -->
                    <div class="flex flex-wrap gap-6 mb-12">
                        <div class="stat-card-small flex items-center gap-6 p-6 min-w-[280px]">
                            <div class="icon-box icon-teal shadow-brand-accent/20">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <div class="text-3xl font-black text-[#003153] tracking-tight">{{ $election->candidates()->count() }}</div>
                                <div class="text-xs text-slate-400 font-black uppercase tracking-widest">Candidates</div>
                            </div>
                        </div>

                        <div class="stat-card-small flex items-center gap-6 p-6 min-w-[280px]">
                            <div class="icon-box icon-teal shadow-brand-accent/20">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div>
                                <div class="text-3xl font-black text-[#003153] tracking-tight">{{ number_format($election->votes()->count()) }}</div>
                                <div class="text-xs text-slate-400 font-black uppercase tracking-widest">Votes Cast</div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-6">
                        @if($hasVoted)
                            <div class="px-10 py-5 bg-slate-100 text-slate-500 rounded-2xl font-bold flex items-center gap-3">
                                <i class="fas fa-check-circle text-emerald-500"></i>
                                VOTE ALREADY SUBMITTED
                            </div>
                        @else
                            <a href="{{ route('voter.elections.vote', $election->code) }}"
                               id="voteBtn"
                               class="btn-start-voting px-12 py-6 rounded-[2rem] font-black flex items-center gap-4 shadow-2xl shadow-brand-accent/30 text-lg">
                                <i class="fas fa-edit"></i>
                                <span id="voteBtnText">START VOTING</span>
                            </a>
                        @endif

                        <a href="{{ route('voter.elections.results', $election->code) }}"
                           class="btn-view-results px-12 py-6 rounded-[2rem] font-black flex items-center gap-4 text-lg border-2">
                            <i class="fas fa-poll-h"></i>
                            VIEW RESULTS
                        </a>
                    </div>
                </div>

                <!-- Right Content: Countdown Card -->
                <div class="w-full lg:w-[450px]">
                    <div class="countdown-card p-10">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 bg-[#003153] rounded-xl flex items-center justify-center text-white shadow-lg shadow-brand-accent/20">
                                <i class="fas fa-clock text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-[#003153] uppercase tracking-tight">Time Remaining</h3>
                                <p class="text-[10px] text-[#00D4AA] font-black uppercase tracking-widest">Election Clock</p>
                            </div>
                        </div>

                        <div id="countdown" class="grid grid-cols-4 gap-4 mb-10">
                            <div class="text-center">
                                <div id="days" class="countdown-box text-3xl font-black text-[#003153] mb-2">00</div>
                                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Days</div>
                            </div>
                            <div class="text-center">
                                <div id="hours" class="countdown-box text-3xl font-black text-[#003153] mb-2">00</div>
                                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Hours</div>
                            </div>
                            <div class="text-center">
                                <div id="minutes" class="countdown-box text-3xl font-black text-[#003153] mb-2">00</div>
                                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Min</div>
                            </div>
                            <div class="text-center">
                                <div id="seconds" class="countdown-box text-3xl font-black text-[#003153] mb-2">00</div>
                                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Sec</div>
                            </div>
                        </div>

                        <div class="bg-slate-50 rounded-2xl p-6 text-center border border-slate-100">
                            <span class="text-[#003153] font-black uppercase text-[10px] tracking-widest">Deadline:</span>
                            <span class="text-slate-500 font-bold ml-1 text-xs">{{ $election->end_date->format('F d, Y \a\t h:i A') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-32">
                <div class="feature-card">
                    <div class="icon-box icon-teal mb-8">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4 class="text-xl font-black text-[#003153] mb-4 uppercase tracking-tight">Secure & Anonymous</h4>
                    <p class="text-slate-500 font-medium leading-relaxed">Your vote is encrypted and completely anonymous. We use industry-standard security protocols.</p>
                </div>

                <div class="feature-card">
                    <div class="icon-box icon-teal mb-8">
                        <i class="fas fa-magic text-xl"></i>
                    </div>
                    <h4 class="text-xl font-black text-[#003153] mb-4 uppercase tracking-tight">Easy to Use</h4>
                    <p class="text-slate-500 font-medium leading-relaxed">Simple, intuitive interface designed for voters of all technical backgrounds.</p>
                </div>

                <div class="feature-card">
                    <div class="icon-box icon-teal mb-8">
                        <i class="fas fa-chart-line text-xl"></i>
                    </div>
                    <h4 class="text-xl font-black text-[#003153] mb-4 uppercase tracking-tight">Real-time Results</h4>
                    <p class="text-slate-500 font-medium leading-relaxed">View live election results as votes are counted with full transparency.</p>
                </div>
            </div>

            <div class="text-center mt-20">
                <a href="{{ route('voter.elections.access') }}"
                   class="text-slate-400 font-bold hover:text-[#003153] transition-all uppercase text-xs tracking-[0.2em] flex items-center justify-center gap-2">
                    <i class="fas fa-sign-out-alt"></i>
                    Exit Election Portal
                </a>
            </div>
        </main>
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
            let targetDate, label, isBeforeStart, isEnded, subLabel;

            if (now < startDate) {
                targetDate = startDate;
                label = "VOTING START: IN PROGRESS";
                subLabel = "Time remaining until voting starts";
                isBeforeStart = true;
                isEnded = false;
            } else if (now < endDate) {
                targetDate = endDate;
                label = "VOTING END: ACTIVE";
                subLabel = "Time remaining until voting closes";
                isBeforeStart = false;
                isEnded = false;
            } else {
                targetDate = now;
                label = "VOTING ENDED: CLOSED";
                subLabel = "Election has concluded";
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

            const subLabelEl = document.getElementById('countdown-label-sub');
            if (subLabelEl) subLabelEl.textContent = subLabel;

            // Update button status
            if (!hasVoted) {
                const voteBtn = document.getElementById('voteBtn');
                const voteBtnText = document.getElementById('voteBtnText');

                if (voteBtn) {
                    voteBtn.classList.remove(
                        'pointer-events-none',
                        'opacity-50',
                        'cursor-not-allowed'
                    );

                    if (isBeforeStart) {
                        voteBtn.classList.add('pointer-events-none', 'opacity-50', 'cursor-not-allowed');
                        if (voteBtnText) voteBtnText.textContent = 'WAITING TO OPEN';
                    } else if (isEnded) {
                        voteBtn.classList.add('pointer-events-none', 'opacity-50', 'cursor-not-allowed');
                        if (voteBtnText) voteBtnText.textContent = 'ELECTION CLOSED';
                    } else {
                        if (voteBtnText) voteBtnText.textContent = 'Start Voting';
                    }
                }
            }
        }

        updateCountdown();
        const timer = setInterval(updateCountdown, 1000);
    </script>
@endpush
