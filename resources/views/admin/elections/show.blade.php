@extends('voter.layouts.app')

@section('title', 'Election Preview - ' . $election->title)

@push('styles')
    <style>
        .gradient-brand {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        }
        .text-brand-primary { color: #0f172a; }
        .text-brand-accent { color: #3b82f6; }
        .bg-brand-primary { background-color: #0f172a; }
        .bg-brand-accent { background-color: #3b82f6; }
        .border-brand-accent { border-color: #3b82f6; }

        .btn-brand {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-brand:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(59, 130, 246, 0.4);
        }

        .candidate-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .candidate-card:hover {
            transform: translateY(-4px);
            border-color: #3b82f6 !important;
        }
        .candidate-card.selected {
            border-color: #3b82f6 !important;
            background-color: #f8fafc !important;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }
        .candidate-card.selected .check-icon {
            display: flex !important;
        }

        .progress-bar-fill {
            background: linear-gradient(90deg, #3b82f6 0%, #60a5fa 100%);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
@endpush

@section('content')
    <div class="bg-amber-100 border-b border-amber-200 py-2 text-center text-amber-800 font-semibold sticky top-0 z-50 shadow-sm px-4">
        <div class="max-w-7xl mx-auto flex items-center justify-center gap-2 sm:gap-4 flex-wrap">
            <span class="inline-flex items-center text-xs sm:text-sm">
                <i class="fas fa-eye mr-2"></i> PREVIEW MODE
            </span>
            <div class="h-4 w-px bg-amber-300 mx-1 sm:mx-2 hidden xs:block"></div>
            <a href="{{ route('admin.elections.index') }}" class="text-amber-900 hover:text-amber-700 underline text-xs sm:text-sm">
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
            <header class="py-4 sm:py-6 px-4 sm:px-8 bg-white/90 backdrop-blur-xl border-b border-slate-200 sticky top-[41px] z-40 shadow-sm">
                <div class="max-w-7xl mx-auto flex items-center justify-between">
                    <div class="flex items-center gap-3 sm:gap-4 group">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-slate-900 flex items-center justify-center shadow-xl shadow-slate-200 transition-transform group-hover:scale-105">
                            <i class="fas fa-vote-yea text-white text-lg sm:text-xl"></i>
                        </div>
                        <div class="min-w-0">
                            <h1 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight uppercase truncate">SecureVote</h1>
                            <p class="text-[9px] sm:text-[10px] font-bold text-blue-600 uppercase tracking-[0.2em]">Philippines</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 sm:gap-8">
                        <div class="text-right hidden xs:block">
                            <p class="text-slate-400 text-[9px] sm:text-[10px] uppercase font-black tracking-[0.2em] mb-0.5">Active Session</p>
                            <p class="text-slate-900 font-bold text-xs sm:text-sm truncate max-w-[120px] sm:max-w-none">{{ $election->title }}</p>
                        </div>
                        <div class="h-8 w-px bg-slate-200 hidden md:block"></div>
                        <div class="hidden md:flex items-center gap-3 px-4 py-2 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                                <i class="fas fa-user text-xs"></i>
                            </div>
                            <span class="text-slate-900 font-bold text-sm">{{ $voter['name'] }}</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <div class="flex-grow px-4 sm:px-6 py-6 sm:py-10">
                <div class="max-w-6xl mx-auto">
                    <!-- Election Header Card -->
                    <div class="bg-slate-900 rounded-[1.5rem] sm:rounded-[2.5rem] p-6 sm:p-12 mb-6 sm:mb-10 shadow-2xl relative overflow-hidden text-center lg:text-left">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-blue-600/20 blur-[100px] rounded-full -mr-32 -mt-32"></div>
                        <div class="absolute bottom-0 left-0 w-64 h-64 bg-indigo-600/20 blur-[100px] rounded-full -ml-32 -mb-32"></div>

                        <div class="relative z-10 flex flex-col lg:flex-row items-center lg:items-center gap-6 sm:gap-8">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white/10 backdrop-blur-md rounded-2xl sm:rounded-3xl flex items-center justify-center border border-white/20 shadow-inner flex-shrink-0 mx-auto lg:mx-0">
                                <i class="fas fa-landmark text-white text-2xl sm:text-3xl"></i>
                            </div>
                            <div class="flex-grow min-w-0">
                                <div class="flex flex-wrap items-center justify-center lg:justify-start gap-3 mb-3">
                                    <span class="px-3 py-1 bg-blue-500 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-full">Official Election</span>
                                    <span class="text-slate-400 text-sm font-medium">• {{ $election->organization ? $election->organization->name : 'General' }}</span>
                                </div>
                                <h2 class="text-2xl sm:text-4xl font-black text-white mb-4 tracking-tight leading-tight uppercase">{{ $election->title }}</h2>
                                <p class="text-slate-400 text-base sm:text-lg max-w-2xl leading-relaxed mx-auto lg:mx-0">{{ $election->description ?? 'Securely cast your vote for the leadership positions listed below.' }}</p>
                            </div>
                            <div class="flex flex-row lg:flex-col gap-4 w-full lg:w-auto overflow-x-auto no-scrollbar pb-2 lg:pb-0">
                                <div class="flex-1 min-w-[120px] lg:w-40 bg-white/5 backdrop-blur-md rounded-2xl p-4 border border-white/10 text-center lg:text-left">
                                    <p class="text-slate-500 text-[10px] font-black uppercase tracking-widest mb-1">Positions</p>
                                    <p class="text-white text-2xl font-black">{{ $positions->count() }}</p>
                                </div>
                                <div class="flex-1 min-w-[120px] lg:w-40 bg-white/5 backdrop-blur-md rounded-2xl p-4 border border-white/10 text-center lg:text-left">
                                    <p class="text-slate-500 text-[10px] font-black uppercase tracking-widest mb-1">Candidates</p>
                                    <p class="text-white text-2xl font-black">{{ $positions->sum(fn($p) => $p->candidates->count()) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form onsubmit="return false;" id="voting-form">
                        <!-- Progress Indicator -->
                        <div class="bg-white rounded-2xl sm:rounded-3xl p-6 sm:p-8 mb-6 sm:mb-10 border border-slate-200 shadow-sm">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                                <div>
                                    <span class="text-slate-900 text-sm font-black uppercase tracking-widest">Voting Progress</span>
                                    <p class="text-slate-400 text-xs mt-1 font-medium">Please complete all positions to finalize your ballot</p>
                                </div>
                                <span class="text-blue-600 font-black text-sm" id="progress-text">0 / {{ $positions->count() }} POSITIONS</span>
                            </div>
                            <div class="h-3 sm:h-4 bg-slate-100 rounded-full overflow-hidden p-1">
                                <div class="h-full progress-bar-fill rounded-full transition-all duration-700 shadow-sm" id="progress-bar" style="width: 0%"></div>
                            </div>
                        </div>

                        <!-- Positions and Candidates -->
                        <div class="space-y-12">
                            @foreach($positions as $index => $position)
                                <div class="position-card" data-position-id="{{ $position->id }}">
                                    <!-- Position Header -->
                                    <div class="flex items-center gap-6 mb-8">
                                        <div class="w-14 h-14 bg-slate-900 text-white rounded-2xl flex items-center justify-center font-black text-xl shadow-lg shadow-slate-200">
                                            {{ sprintf('%02d', $index + 1) }}
                                        </div>
                                        <div>
                                            <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight">{{ $position->name }}</h3>
                                            <div class="flex items-center gap-3 mt-1">
                                                <span class="text-slate-500 text-sm font-bold">Requirement:</span>
                                                <span class="px-3 py-1 bg-blue-50 text-blue-700 text-[10px] font-black rounded-full uppercase tracking-wider">
                                                    Select {{ $position->max_votes ?? 1 }} Candidate{{ ($position->max_votes ?? 1) > 1 ? 's' : '' }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="h-px flex-grow bg-slate-100 ml-4 hidden md:block"></div>
                                    </div>

                                    <!-- Candidates Grid -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                                        @foreach($position->candidates as $candidate)
                                            <label class="candidate-card group cursor-pointer block relative">
                                                <input type="{{ ($position->max_votes ?? 1) > 1 ? 'checkbox' : 'radio' }}"
                                                       name="votes[{{ $position->id }}]{{ ($position->max_votes ?? 1) > 1 ? '[]' : '' }}"
                                                       value="{{ $candidate->id }}"
                                                       class="vote-input hidden"
                                                       data-position="{{ $position->id }}"
                                                       data-candidate-name="{{ $candidate->name }}"
                                                       data-max-votes="{{ $position->max_votes ?? 1 }}">

                                                <div class="card-content bg-white rounded-[1.5rem] sm:rounded-[2rem] border-2 border-slate-100 p-4 sm:p-6 transition-all hover:shadow-xl hover:shadow-slate-100 relative overflow-hidden">
                                                    <div class="flex flex-col items-center text-center">
                                                        <div class="relative mb-4 sm:mb-6">
                                                            <div class="w-16 h-16 sm:w-24 sm:h-24 rounded-2xl sm:rounded-3xl bg-slate-50 overflow-hidden border-4 border-white shadow-lg transition-transform group-hover:scale-105">
                                                                @if($candidate->photo)
                                                                    <img src="{{ asset('storage/' . $candidate->photo) }}" class="w-full h-full object-cover">
                                                                @else
                                                                    <div class="w-full h-full flex items-center justify-center text-slate-200">
                                                                        <i class="fas fa-user text-2xl sm:text-3xl"></i>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="check-icon absolute -top-1 sm:-top-2 -right-1 sm:-right-2 w-6 h-6 sm:w-8 sm:h-8 bg-blue-600 text-white rounded-lg sm:rounded-xl hidden items-center justify-center shadow-lg border-2 border-white transform rotate-12">
                                                                <i class="fas fa-check text-[10px] sm:text-xs"></i>
                                                            </div>
                                                        </div>

                                                        <h4 class="font-black text-base sm:text-lg text-slate-900 leading-tight mb-2 truncate w-full">{{ $candidate->name }}</h4>

                                                        @if($candidate->partylist)
                                                            <span class="px-3 py-1 bg-slate-100 text-slate-500 text-[8px] sm:text-[9px] font-black uppercase tracking-[0.2em] rounded-full truncate max-w-full">
                                                                {{ $candidate->partylist->name }}
                                                            </span>
                                                        @else
                                                            <span class="px-3 py-1 bg-slate-50 text-slate-400 text-[8px] sm:text-[9px] font-black uppercase tracking-[0.2em] rounded-full">
                                                                Independent
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Submit Section -->
                        <div class="mt-12 sm:mt-20 bg-slate-50 rounded-[1.5rem] sm:rounded-[3rem] p-6 sm:p-12 border-2 border-slate-100 flex flex-col items-center text-center">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white rounded-2xl sm:rounded-3xl flex items-center justify-center mb-6 sm:mb-8 shadow-xl shadow-slate-200">
                                <i class="fas fa-shield-alt text-blue-600 text-2xl sm:text-3xl"></i>
                            </div>
                            <h3 class="text-xl sm:text-3xl font-black text-slate-900 mb-3 sm:mb-4 tracking-tight uppercase">Review Your Ballot</h3>
                            <p class="text-slate-500 mb-6 sm:mb-10 max-w-md text-base sm:text-lg leading-relaxed">Please ensure all your selections are correct. You cannot change your vote after submission.</p>

                            <button type="button" disabled class="w-full sm:w-auto bg-slate-200 text-slate-400 px-8 sm:px-12 py-4 sm:py-5 rounded-xl sm:rounded-2xl font-black text-base sm:text-lg cursor-not-allowed uppercase tracking-widest flex items-center justify-center gap-3">
                                <i class="fas fa-lock text-sm"></i>
                                Submit Final Vote
                            </button>
                            <p class="mt-4 sm:mt-6 text-slate-400 text-[10px] sm:text-xs font-bold uppercase tracking-widest italic">Submission is disabled in preview mode</p>
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
