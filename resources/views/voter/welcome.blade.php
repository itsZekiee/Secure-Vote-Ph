@extends('voter.layouts.app')

@section('title', $election->title . ' - SecureVote PH')

@push('styles')
    <style>
        .gradient-brand { background: linear-gradient(135deg, #003153 0%, #00D4AA 100%); }
        .text-brand-primary { color: #003153; }
        .text-brand-accent { color: #00D4AA; }
        .btn-brand {
            background: linear-gradient(135deg, #003153 0%, #00D4AA 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-brand:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px -10px rgba(0, 212, 170, 0.5);
        }
        .btn-brand:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .countdown-box {
            background: linear-gradient(135deg, rgba(0, 49, 83, 0.05) 0%, rgba(0, 212, 170, 0.05) 100%);
        }
    </style>
@endpush

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-white to-slate-50 py-12 px-4">
        <div class="max-w-3xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="w-20 h-20 gradient-brand rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                    <i class="fas fa-vote-yea text-white text-3xl"></i>
                </div>
                <h1 class="text-3xl lg:text-4xl font-bold text-brand-primary mb-2">{{ $election->title }}</h1>
                <p class="text-slate-500">Election Code: <span class="font-mono font-bold">{{ $election->code }}</span></p>
            </div>

            <!-- Welcome Message -->
            <div class="bg-white rounded-3xl shadow-xl p-8 mb-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-emerald-600"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-brand-primary">Welcome, {{ $voter['name'] }}!</h2>
                        <p class="text-slate-500 text-sm">You are registered to vote in this election</p>
                    </div>
                </div>

                @if($election->description)
                    <div class="bg-slate-50 rounded-2xl p-6 mb-6">
                        <h3 class="font-semibold text-brand-primary mb-2">About This Election</h3>
                        <p class="text-slate-600">{{ $election->description }}</p>
                    </div>
                @endif

                <!-- Countdown Section -->
                <div class="countdown-box rounded-2xl p-6 mb-6">
                    <h3 class="font-semibold text-brand-primary mb-4 text-center">
                        @if(now()->lt($election->start_date))
                            <i class="fas fa-clock text-brand-accent mr-2"></i>Election Starts In
                        @elseif(now()->gt($election->end_date))
                            <i class="fas fa-flag-checkered text-brand-accent mr-2"></i>Election Has Ended
                        @else
                            <i class="fas fa-hourglass-half text-brand-accent mr-2"></i>Election Ends In
                        @endif
                    </h3>

                    <div id="countdown" class="grid grid-cols-4 gap-4 text-center">
                        <div class="bg-white rounded-xl p-4 shadow-sm">
                            <div id="days" class="text-3xl font-bold text-brand-primary">00</div>
                            <div class="text-xs text-slate-500 uppercase tracking-wide">Days</div>
                        </div>
                        <div class="bg-white rounded-xl p-4 shadow-sm">
                            <div id="hours" class="text-3xl font-bold text-brand-primary">00</div>
                            <div class="text-xs text-slate-500 uppercase tracking-wide">Hours</div>
                        </div>
                        <div class="bg-white rounded-xl p-4 shadow-sm">
                            <div id="minutes" class="text-3xl font-bold text-brand-primary">00</div>
                            <div class="text-xs text-slate-500 uppercase tracking-wide">Minutes</div>
                        </div>
                        <div class="bg-white rounded-xl p-4 shadow-sm">
                            <div id="seconds" class="text-3xl font-bold text-brand-accent">00</div>
                            <div class="text-xs text-slate-500 uppercase tracking-wide">Seconds</div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4">
                    @if($hasVoted)
                        <div class="flex-1 py-4 bg-emerald-100 text-emerald-700 font-bold rounded-2xl text-center">
                            <i class="fas fa-check-circle mr-2"></i>You Have Already Voted
                        </div>
                    @else
                        <a href="{{ route('voter.elections.vote', $election->code) }}"
                           id="voteBtn"
                           class="btn-brand flex-1 py-4 text-white font-bold rounded-2xl text-center {{ now()->lt($election->start_date) ? 'pointer-events-none opacity-60' : '' }}">
                            <i class="fas fa-vote-yea mr-2"></i>
                            <span id="voteBtnText">
                            @if(now()->lt($election->start_date))
                                    Voting Not Yet Open
                                @elseif(now()->gt($election->end_date))
                                    Voting Closed
                                @else
                                    Start Voting
                                @endif
                        </span>
                        </a>
                    @endif

                    <a href="{{ route('voter.elections.results', $election->code) }}"
                       class="flex-1 py-4 bg-slate-100 text-slate-700 font-bold rounded-2xl text-center hover:bg-slate-200 transition-colors">
                        <i class="fas fa-chart-bar mr-2"></i>View Live Results
                    </a>
                </div>
            </div>

            <!-- Election Info Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                <div class="bg-white rounded-2xl p-5 shadow-lg text-center">
                    <i class="fas fa-calendar-alt text-brand-accent text-2xl mb-2"></i>
                    <p class="text-xs text-slate-500 mb-1">Start Date</p>
                    <p class="font-bold text-brand-primary">{{ $election->start_date->format('M d, Y h:i A') }}</p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-lg text-center">
                    <i class="fas fa-calendar-check text-brand-accent text-2xl mb-2"></i>
                    <p class="text-xs text-slate-500 mb-1">End Date</p>
                    <p class="font-bold text-brand-primary">{{ $election->end_date->format('M d, Y h:i A') }}</p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-lg text-center">
                    <i class="fas fa-users text-brand-accent text-2xl mb-2"></i>
                    <p class="text-xs text-slate-500 mb-1">Total Positions</p>
                    <p class="font-bold text-brand-primary">{{ $election->positions()->count() }}</p>
                </div>
            </div>

            <div class="text-center">
                <a href="{{ route('voter.elections.access') }}" class="text-slate-500 hover:text-brand-primary">
                    <i class="fas fa-sign-out-alt mr-2"></i>Exit Election
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const startDate = new Date("{{ $election->start_date->toISOString() }}").getTime();
        const endDate = new Date("{{ $election->end_date->toISOString() }}").getTime();
        const hasVoted = {{ $hasVoted ? 'true' : 'false' }};

        function updateCountdown() {
            const now = new Date().getTime();
            let targetDate, isBeforeStart;

            if (now < startDate) {
                targetDate = startDate;
                isBeforeStart = true;
            } else if (now < endDate) {
                targetDate = endDate;
                isBeforeStart = false;
            } else {
                document.getElementById('days').textContent = '00';
                document.getElementById('hours').textContent = '00';
                document.getElementById('minutes').textContent = '00';
                document.getElementById('seconds').textContent = '00';
                return;
            }

            const distance = targetDate - now;

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById('days').textContent = String(days).padStart(2, '0');
            document.getElementById('hours').textContent = String(hours).padStart(2, '0');
            document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
            document.getElementById('seconds').textContent = String(seconds).padStart(2, '0');

            // Enable vote button when election starts
            if (!isBeforeStart && !hasVoted) {
                const voteBtn = document.getElementById('voteBtn');
                const voteBtnText = document.getElementById('voteBtnText');
                if (voteBtn) {
                    voteBtn.classList.remove('pointer-events-none', 'opacity-60');
                    voteBtnText.textContent = 'Start Voting';
                }
            }
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
    </script>
@endpush
