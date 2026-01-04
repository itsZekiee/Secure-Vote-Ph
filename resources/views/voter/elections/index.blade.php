@extends('voter.layouts.app')

@section('title', 'Cast Your Vote - ' . $election->title)

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
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-100 relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-30">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, rgba(0, 49, 83, 0.08) 1px, transparent 0); background-size: 24px 24px;"></div>
        </div>

        <!-- Gradient Orbs -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <div class="absolute -top-40 -right-40 w-[600px] h-[600px] bg-gradient-to-br from-brand-accent/20 to-transparent rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-[600px] h-[600px] bg-gradient-to-tr from-brand-primary/10 to-transparent rounded-full blur-3xl"></div>
        </div>

        <div class="relative z-10 min-h-screen flex flex-col">
            <!-- Header -->
            <header class="py-5 px-6 bg-white/80 backdrop-blur-xl border-b border-slate-200/50 shadow-sm">
                <div class="max-w-7xl mx-auto flex items-center justify-between">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                        <div class="w-11 h-11 rounded-xl gradient-brand flex items-center justify-center shadow-lg shadow-brand-primary/20 group-hover:scale-105 transition-transform">
                            <i class="fas fa-vote-yea text-white text-lg"></i>
                        </div>
                        <div>
                            <h1 class="text-lg font-bold text-brand-primary tracking-tight">SecureVote</h1>
                            <p class="text-xs text-brand-accent">Philippines</p>
                        </div>
                    </a>
                    <div class="flex items-center gap-6">
                        <div class="text-right hidden sm:block">
                            <p class="text-slate-400 text-xs uppercase tracking-wider">Now Voting</p>
                            <p class="text-brand-primary font-medium">{{ $election->title }}</p>
                        </div>
                        <div class="hidden md:flex items-center gap-2 px-3 py-1.5 bg-slate-100 rounded-lg">
                            <i class="fas fa-user text-brand-accent text-sm"></i>
                            <span class="text-slate-600 text-sm">{{ $voter['name'] }}</span>
                        </div>
                        <a href="{{ route('voter.elections.welcome', $election->code) }}" class="flex items-center gap-2 text-slate-500 hover:text-brand-primary transition-colors text-sm font-medium">
                            <i class="fas fa-arrow-left"></i>
                            <span class="hidden sm:inline">Back</span>
                        </a>
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

                    <!-- Error Messages -->
                    @if($errors->any())
                        <div class="bg-red-50 border border-red-200 text-red-600 p-4 rounded-xl mb-6 flex items-start gap-3 shadow-sm">
                            <i class="fas fa-exclamation-triangle text-lg mt-0.5"></i>
                            <div>
                                @foreach($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 p-4 rounded-xl mb-6 flex items-center gap-3 shadow-sm">
                            <i class="fas fa-check-circle text-lg"></i>
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="bg-blue-50 border border-blue-200 text-blue-700 p-4 rounded-xl mb-6 flex items-center gap-3 shadow-sm">
                            <i class="fas fa-info-circle text-lg"></i>
                            <p>{{ session('info') }}</p>
                        </div>
                    @endif

                    <form action="{{ route('voter.elections.submit', $election->code) }}" method="POST" id="voting-form">
                        @csrf

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
                                                    <!-- Check Icon -->
                                                    <div class="check-icon hidden absolute top-3 right-3 w-6 h-6 gradient-brand rounded-full items-center justify-center">
                                                        <i class="fas fa-check text-white text-xs"></i>
                                                    </div>

                                                    <!-- Candidate Photo -->
                                                    <div class="flex items-center gap-4 mb-3">
                                                        @if($candidate->photo)
                                                            <img src="{{ asset('storage/' . $candidate->photo) }}"
                                                                 alt="{{ $candidate->name }}"
                                                                 class="w-16 h-16 rounded-xl object-cover border-2 border-slate-100">
                                                        @else
                                                            <div class="w-16 h-16 rounded-xl gradient-brand flex items-center justify-center">
                                                                <span class="text-white text-xl font-bold">{{ strtoupper(substr($candidate->name, 0, 1)) }}</span>
                                                            </div>
                                                        @endif
                                                        <div class="flex-grow min-w-0">
                                                            <h4 class="font-bold text-brand-primary text-sm truncate">{{ $candidate->name }}</h4>
                                                            @if($candidate->partylist)
                                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 partylist-badge text-brand-primary text-xs rounded-full mt-1">
                                                                    <i class="fas fa-flag text-brand-accent"></i>
                                                                    {{ $candidate->partylist->name }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <!-- Candidate Info -->
                                                    @if($candidate->motto)
                                                        <p class="text-slate-500 text-xs line-clamp-2 italic">"{{ $candidate->motto }}"</p>
                                                    @endif

                                                    @if($candidate->course || $candidate->year_level)
                                                        <div class="mt-2 flex items-center gap-2 text-xs text-slate-400">
                                                            @if($candidate->course)
                                                                <span>{{ $candidate->course }}</span>
                                                            @endif
                                                            @if($candidate->year_level)
                                                                <span>• Year {{ $candidate->year_level }}</span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>

                                    @if($position->candidates->isEmpty())
                                        <div class="text-center py-8 text-slate-400">
                                            <i class="fas fa-users text-3xl mb-2"></i>
                                            <p>No candidates available for this position</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        <!-- Submit Section -->
                        <div class="bg-white rounded-2xl overflow-hidden border border-slate-200 shadow-sm">
                            <!-- Vote Summary -->
                            <div class="p-6">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-10 h-10 gradient-brand rounded-xl flex items-center justify-center">
                                        <i class="fas fa-clipboard-list text-white"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-brand-primary">Your Vote Summary</h3>
                                        <p class="text-slate-500 text-xs">Review your selections before submitting</p>
                                    </div>
                                </div>

                                <div id="vote-summary" class="text-slate-500 text-sm mb-6 min-h-[60px] p-4 bg-slate-50 rounded-xl border border-slate-200">
                                    <p class="flex items-center gap-2">
                                        <i class="fas fa-info-circle text-slate-400"></i>
                                        Select candidates above to see your choices here.
                                    </p>
                                </div>

                                <!-- Confirmation Checkbox -->
                                <div class="mb-6">
                                    <label class="flex items-start gap-3 cursor-pointer">
                                        <input type="checkbox" id="confirm-checkbox" class="mt-1 w-5 h-5 text-brand-accent border-slate-300 rounded focus:ring-brand-accent">
                                        <span class="text-slate-600 text-sm">
                                            I confirm that my vote selections are correct. I understand that once submitted, my vote cannot be changed.
                                        </span>
                                    </label>
                                </div>

                                <!-- Submit Button -->
                                <button type="submit"
                                        id="submit-btn"
                                        disabled
                                        class="w-full py-4 btn-brand text-white font-bold rounded-xl shadow-lg flex items-center justify-center gap-3 text-base disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:transform-none disabled:hover:shadow-lg">
                                    <i class="fas fa-paper-plane"></i>
                                    <span>Submit My Vote</span>
                                </button>

                                <p class="text-center text-slate-400 text-xs mt-4">
                                    <i class="fas fa-lock mr-1"></i>
                                    Your vote is encrypted and anonymous. No one can trace it back to you.
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Footer -->
            <footer class="py-5 px-6 bg-white/80 backdrop-blur border-t border-slate-200/50">
                <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-slate-500 text-sm">© {{ date('Y') }} SecureVote Philippines. All rights reserved.</p>
                    <div class="flex items-center gap-4">
                        <span class="text-slate-400 text-sm">Election Code: <span class="font-mono font-bold text-brand-primary">{{ $election->code }}</span></span>
                    </div>
                </div>
            </footer>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('voting-form');
            const summary = document.getElementById('vote-summary');
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');
            const submitBtn = document.getElementById('submit-btn');
            const confirmCheckbox = document.getElementById('confirm-checkbox');
            const totalPositions = {{ $positions->count() }};

            // Handle candidate card selection visual feedback
            document.querySelectorAll('.vote-input').forEach(input => {
                input.addEventListener('change', function() {
                    const positionId = this.dataset.position;
                    const maxVotes = parseInt(this.dataset.maxVotes);
                    const card = this.closest('.candidate-card');

                    if (maxVotes === 1) {
                        // Radio button behavior - deselect others in same position
                        document.querySelectorAll(`.vote-input[data-position="${positionId}"]`).forEach(otherInput => {
                            const otherCard = otherInput.closest('.candidate-card');
                            if (otherInput !== this) {
                                otherCard.querySelector('.card-content').classList.remove('selected');
                            }
                        });
                    } else {
                        // Checkbox behavior - check max votes limit
                        const checkedCount = document.querySelectorAll(`.vote-input[data-position="${positionId}"]:checked`).length;
                        if (checkedCount > maxVotes) {
                            this.checked = false;
                            alert(`You can only select up to ${maxVotes} candidate(s) for this position.`);
                            return;
                        }
                    }

                    // Toggle selected class
                    if (this.checked) {
                        card.querySelector('.card-content').classList.add('selected');
                    } else {
                        card.querySelector('.card-content').classList.remove('selected');
                    }

                    updateSummary();
                });
            });

            function updateSummary() {
                const selections = {};
                const inputs = document.querySelectorAll('.vote-input:checked');

                inputs.forEach(input => {
                    const positionId = input.dataset.position;
                    const candidateName = input.dataset.candidateName;
                    const positionCard = input.closest('.position-card');
                    const positionName = positionCard.querySelector('h3').textContent;

                    if (!selections[positionName]) {
                        selections[positionName] = [];
                    }
                    selections[positionName].push(candidateName);
                });

                const filledPositions = Object.keys(selections).length;
                const progressPercent = (filledPositions / totalPositions) * 100;

                progressBar.style.width = progressPercent + '%';
                progressText.textContent = filledPositions + ' / ' + totalPositions + ' positions';

                if (Object.keys(selections).length > 0) {
                    let html = '<div class="space-y-2">';
                    for (const [position, candidates] of Object.entries(selections)) {
                        html += `<div class="flex items-start gap-2">
                            <i class="fas fa-check-circle text-brand-accent mt-0.5"></i>
                            <div>
                                <span class="text-brand-primary font-medium">${position}:</span>
                                <span class="text-slate-600">${candidates.join(', ')}</span>
                            </div>
                        </div>`;
                    }
                    html += '</div>';
                    summary.innerHTML = html;
                } else {
                    summary.innerHTML = '<p class="flex items-center gap-2"><i class="fas fa-info-circle text-slate-400"></i>Select candidates above to see your choices here.</p>';
                }

                updateSubmitButton();
            }

            function updateSubmitButton() {
                const checkedInputs = document.querySelectorAll('.vote-input:checked');
                const hasSelections = checkedInputs.length > 0;
                const isConfirmed = confirmCheckbox.checked;

                submitBtn.disabled = !(hasSelections && isConfirmed);
            }

            confirmCheckbox.addEventListener('change', updateSubmitButton);

            form.addEventListener('submit', function(e) {
                const checkedInputs = document.querySelectorAll('.vote-input:checked');

                if (checkedInputs.length === 0) {
                    e.preventDefault();
                    alert('Please select at least one candidate before submitting.');
                    return;
                }

                if (!confirmCheckbox.checked) {
                    e.preventDefault();
                    alert('Please confirm your selections before submitting.');
                    return;
                }

                if (!confirm('Are you sure you want to submit your vote? This action cannot be undone.')) {
                    e.preventDefault();
                }
            });

            // Initialize
            updateSummary();
        });
    </script>
@endpush
