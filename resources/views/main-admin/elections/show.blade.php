@extends('voter.layouts.app')

@section('title', 'Election Preview - ' . $election->title)

@push('styles')
    <style>
        .gradient-brand {
            background: linear-gradient(135deg, #003153 0%, #00D4AA 100%);
        }
        .text-brand-primary { color: #003153; }
        .text-brand-accent { color: #00D4AA; }
        .bg-brand-primary { background-color: #003153; }
        .bg-brand-accent { background-color: #00D4AA; }
        .border-brand-accent { border-color: #00D4AA; }

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

        .candidate-card {
            transition: all 0.3s ease;
        }
        .candidate-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 49, 83, 0.1);
        }
        .candidate-card.selected {
            border-color: #00D4AA !important;
            background: linear-gradient(135deg, rgba(0, 212, 170, 0.05) 0%, rgba(0, 49, 83, 0.05) 100%) !important;
            box-shadow: 0 0 0 3px rgba(0, 212, 170, 0.2);
        }
        .candidate-card.selected .check-icon {
            display: flex !important;
        }
        .candidate-card input:checked + .card-content {
            border-color: #00D4AA !important;
            background: linear-gradient(135deg, rgba(0, 212, 170, 0.05) 0%, rgba(0, 49, 83, 0.05) 100%) !important;
        }
        .candidate-card input:checked + .card-content .check-icon {
            display: flex !important;
        }

        .position-badge {
            background: linear-gradient(135deg, rgba(0, 49, 83, 0.1) 0%, rgba(0, 212, 170, 0.1) 100%);
        }

        .progress-bar-fill {
            background: linear-gradient(135deg, #003153 0%, #00D4AA 100%);
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .partylist-badge {
            background: linear-gradient(135deg, rgba(0, 212, 170, 0.1) 0%, rgba(0, 49, 83, 0.1) 100%);
        }
    </style>
@endpush

@section('content')
    <div class="bg-amber-100 border-b border-amber-200 py-2 text-center text-amber-800 font-semibold sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 flex items-center justify-center gap-4">
            <span class="inline-flex items-center">
                <i class="fas fa-eye mr-2"></i> PREVIEW MODE: Viewing as a voter
            </span>
            <div class="h-4 w-px bg-amber-300 mx-2"></div>
            <a href="{{ route('admin.elections.index') }}" class="text-amber-900 hover:text-amber-700 underline text-sm">
                <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-100 relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-30">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, rgba(0, 49, 83, 0.08) 1px, transparent 0); background-size: 24px 24px;"></div>
        </div>

        <div class="relative z-10 min-h-screen flex flex-col">
            <!-- Header -->
            <header class="py-5 px-6 bg-white/80 backdrop-blur-xl border-b border-slate-200/50 shadow-sm">
                <div class="max-w-7xl mx-auto flex items-center justify-between">
                    <div class="flex items-center gap-3 group">
                        <div class="w-11 h-11 rounded-xl gradient-brand flex items-center justify-center shadow-lg shadow-brand-primary/20 group-hover:scale-105 transition-transform">
                            <i class="fas fa-vote-yea text-white text-lg"></i>
                        </div>
                        <div>
                            <h1 class="text-lg font-bold text-brand-primary tracking-tight">SecureVote</h1>
                            <p class="text-xs text-brand-accent">Philippines</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-6">
                        <div class="text-right hidden sm:block">
                            <p class="text-slate-400 text-xs uppercase tracking-wider">Now Voting</p>
                            <p class="text-brand-primary font-medium">{{ $election->title }}</p>
                        </div>
                        <div class="hidden md:flex items-center gap-2 px-3 py-1.5 bg-slate-100 rounded-lg">
                            <i class="fas fa-user text-brand-accent text-sm"></i>
                            <span class="text-slate-600 text-sm">{{ $voter['name'] }}</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <div class="flex-grow px-4 sm:px-6 py-8">
                <div class="max-w-5xl mx-auto">
                    <!-- Election Header Card -->
                    <div class="gradient-brand rounded-2xl p-6 sm:p-8 mb-8 shadow-2xl shadow-brand-primary/20">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-6">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center">
                                <i class="fas fa-vote-yea text-white text-2xl sm:text-3xl"></i>
                            </div>
                            <div class="flex-grow">
                                <h2 class="text-2xl sm:text-3xl font-bold text-white mb-2">{{ $election->title }}</h2>
                                <p class="text-white/80 text-sm sm:text-base">{{ $election->description ?? 'Select your preferred candidates for each position below' }}</p>
                            </div>
                            <div class="flex gap-3 w-full sm:w-auto">
                                <div class="flex-1 sm:flex-none bg-white/20 backdrop-blur rounded-xl px-4 py-3 text-center">
                                    <p class="text-white/70 text-xs uppercase tracking-wider">Positions</p>
                                    <p class="text-white text-xl font-bold">{{ $positions->count() }}</p>
                                </div>
                                <div class="flex-1 sm:flex-none bg-white/20 backdrop-blur rounded-xl px-4 py-3 text-center">
                                    <p class="text-white/70 text-xs uppercase tracking-wider">Candidates</p>
                                    <p class="text-white text-xl font-bold">{{ $positions->sum(fn($p) => $p->candidates->count()) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form onsubmit="return false;" id="voting-form">
                        <!-- Progress Indicator -->
                        <div class="bg-white rounded-xl p-5 mb-6 border border-slate-200 shadow-sm">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-slate-600 text-sm font-medium">Voting Progress</span>
                                <span class="text-brand-accent font-semibold text-sm" id="progress-text">0 / {{ $positions->count() }} positions</span>
                            </div>
                            <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full progress-bar-fill rounded-full transition-all duration-500" id="progress-bar" style="width: 0%"></div>
                            </div>
                        </div>

                        <!-- Positions and Candidates -->
                        @foreach($positions as $index => $position)
                            <div class="bg-white rounded-2xl mb-6 overflow-hidden border border-slate-200 shadow-sm hover:shadow-md transition-shadow position-card" data-position-id="{{ $position->id }}">
                                <!-- Position Header -->
                                <div class="bg-gradient-to-r from-slate-50 to-white px-6 py-5 border-b border-slate-100">
                                    <div class="flex items-center justify-between flex-wrap gap-3">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 position-badge rounded-xl flex items-center justify-center">
                                                <span class="text-brand-primary font-bold text-lg">{{ $index + 1 }}</span>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-brand-primary">{{ $position->name }}</h3>
                                                <p class="text-slate-500 text-sm">
                                                    Select {{ $position->max_votes ?? 1 }} candidate{{ ($position->max_votes ?? 1) > 1 ? 's' : '' }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="px-3 py-1.5 bg-brand-accent/10 text-brand-accent text-xs font-semibold rounded-full">
                                                {{ $position->candidates->count() }} Candidate{{ $position->candidates->count() > 1 ? 's' : '' }}
                                            </span>
                                        </div>
                                    </div>
                                    @if($position->description)
                                        <p class="mt-3 text-slate-500 text-sm pl-16">{{ $position->description }}</p>
                                    @endif
                                </div>

                                <!-- Candidates Grid -->
                                <div class="p-6 bg-slate-50/50">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                        @foreach($position->candidates as $candidate)
                                            <label class="candidate-card cursor-pointer block">
                                                <input type="{{ ($position->max_votes ?? 1) > 1 ? 'checkbox' : 'radio' }}"
                                                       name="votes[{{ $position->id }}]{{ ($position->max_votes ?? 1) > 1 ? '[]' : '' }}"
                                                       value="{{ $candidate->id }}"
                                                       class="vote-input hidden"
                                                       data-position="{{ $position->id }}"
                                                       data-candidate-name="{{ $candidate->name }}"
                                                       data-max-votes="{{ $position->max_votes ?? 1 }}">
                                                <div class="card-content relative bg-white rounded-xl border-2 border-slate-200 p-4 hover:border-brand-accent/50 transition-all">
                                                    <div class="flex items-center gap-4">
                                                        <div class="relative">
                                                            <div class="w-14 h-14 rounded-xl bg-slate-100 overflow-hidden border border-slate-200">
                                                                @if($candidate->photo)
                                                                    <img src="{{ asset('storage/' . $candidate->photo) }}" class="w-full h-full object-cover">
                                                                @else
                                                                    <div class="w-full h-full flex items-center justify-center text-slate-300">
                                                                        <i class="fas fa-user text-xl"></i>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="check-icon absolute -top-2 -right-2 w-6 h-6 bg-brand-accent text-white rounded-full hidden items-center justify-center shadow-lg border-2 border-white">
                                                                <i class="fas fa-check text-[10px]"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow">
                                                            <h4 class="font-bold text-brand-primary leading-tight">{{ $candidate->name }}</h4>
                                                            @if($candidate->partylist)
                                                                <p class="text-[10px] font-bold text-brand-accent uppercase tracking-wider mt-1">{{ $candidate->partylist->name }}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Submit Section -->
                        <div class="mt-12 bg-white rounded-2xl p-8 border border-slate-200 shadow-xl shadow-slate-200/50 flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-brand-accent/10 rounded-full flex items-center justify-center mb-6">
                                <i class="fas fa-shield-alt text-brand-accent text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-brand-primary mb-2">Review Your Selection</h3>
                            <p class="text-slate-500 mb-8 max-w-md">In preview mode, you can select candidates but submission is disabled.</p>

                            <button type="button" disabled class="btn-brand text-white px-10 py-4 rounded-xl font-bold text-lg opacity-50 cursor-not-allowed">
                                <i class="fas fa-paper-plane mr-2"></i> Submit Vote
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const voteInputs = document.querySelectorAll('.vote-input');
        const progressBar = document.getElementById('progress-bar');
        const progressText = document.getElementById('progress-text');
        const totalPositions = {{ $positions->count() }};

        function updateProgress() {
            const selectedPositions = new Set();
            voteInputs.forEach(input => {
                if (input.checked) {
                    selectedPositions.add(input.dataset.position);
                }
            });

            const count = selectedPositions.size;
            const percentage = (count / totalPositions) * 100;
            progressBar.style.width = percentage + '%';
            progressText.textContent = count + ' / ' + totalPositions + ' positions';
        }

        voteInputs.forEach(input => {
            input.addEventListener('change', function() {
                const card = this.closest('.candidate-card');
                const positionId = this.dataset.position;
                const maxVotes = parseInt(this.dataset.maxVotes);

                if (this.type === 'radio') {
                    document.querySelectorAll(`.vote-input[data-position="${positionId}"]`).forEach(i => {
                        i.closest('.candidate-card').classList.remove('selected');
                    });
                    if (this.checked) card.classList.add('selected');
                } else {
                    const checkedCount = document.querySelectorAll(`.vote-input[data-position="${positionId}"]:checked`).length;
                    if (checkedCount > maxVotes) {
                        this.checked = false;
                        alert(`You can only select up to ${maxVotes} candidates for this position.`);
                        return;
                    }
                    if (this.checked) card.classList.add('selected');
                    else card.classList.remove('selected');
                }
                updateProgress();
            });
        });
    });
</script>
@endpush
