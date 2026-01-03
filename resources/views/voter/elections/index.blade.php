@extends('voter.layouts.app')

@section('title', 'Cast Your Vote - ' . $election->name)

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-100 relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-30">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, rgba(15, 118, 110, 0.08) 1px, transparent 0); background-size: 24px 24px;"></div>
        </div>

        <!-- Gradient Orbs -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <div class="absolute -top-40 -right-40 w-[600px] h-[600px] bg-gradient-to-br from-teal-100/40 to-transparent rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-[600px] h-[600px] bg-gradient-to-tr from-cyan-100/40 to-transparent rounded-full blur-3xl"></div>
        </div>

        <div class="relative z-10 min-h-screen flex flex-col">
            <!-- Header -->
            <header class="py-5 px-6 bg-white/80 backdrop-blur-xl border-b border-slate-200/50 shadow-sm">
                <div class="max-w-7xl mx-auto flex items-center justify-between">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                        <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-teal-500 to-teal-600 flex items-center justify-center shadow-lg shadow-teal-500/20 group-hover:scale-105 transition-transform">
                            <i class="fas fa-shield-alt text-white text-lg"></i>
                        </div>
                        <div>
                            <h1 class="text-lg font-bold text-slate-800 tracking-tight">SecureVote</h1>
                            <p class="text-xs text-teal-600">Philippines</p>
                        </div>
                    </a>
                    <div class="flex items-center gap-6">
                        <div class="text-right hidden sm:block">
                            <p class="text-slate-400 text-xs uppercase tracking-wider">Now Voting</p>
                            <p class="text-slate-800 font-medium">{{ $election->name }}</p>
                        </div>
                        <a href="{{ route('home') }}" class="flex items-center gap-2 text-slate-500 hover:text-teal-600 transition-colors text-sm font-medium">
                            <i class="fas fa-arrow-left"></i>
                            <span class="hidden sm:inline">Back to Home</span>
                        </a>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <div class="flex-grow px-4 sm:px-6 py-8">
                <div class="max-w-5xl mx-auto">
                    <!-- Election Header Card -->
                    <div class="bg-gradient-to-r from-teal-600 to-teal-500 rounded-2xl p-6 sm:p-8 mb-8 shadow-2xl shadow-teal-500/20">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-6">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center">
                                <i class="fas fa-vote-yea text-white text-2xl sm:text-3xl"></i>
                            </div>
                            <div class="flex-grow">
                                <h2 class="text-2xl sm:text-3xl font-bold text-white mb-2">{{ $election->name }}</h2>
                                <p class="text-white/80 text-sm sm:text-base">{{ $election->description ?? 'Select your preferred candidates for each position below' }}</p>
                            </div>
                            <div class="flex gap-3 w-full sm:w-auto">
                                <div class="flex-1 sm:flex-none bg-white/20 backdrop-blur rounded-xl px-4 py-3 text-center">
                                    <p class="text-2xl font-bold text-white">{{ $positions->count() }}</p>
                                    <p class="text-white/70 text-xs uppercase tracking-wider">Positions</p>
                                </div>
                                <div class="flex-1 sm:flex-none bg-white/20 backdrop-blur rounded-xl px-4 py-3 text-center">
                                    <p class="text-2xl font-bold text-white">{{ $positions->sum(fn($p) => $p->candidates->count()) }}</p>
                                    <p class="text-white/70 text-xs uppercase tracking-wider">Candidates</p>
                                </div>
                            </div>
                        </div>
                    </div>

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
                        <div class="bg-teal-50 border border-teal-200 text-teal-700 p-4 rounded-xl mb-6 flex items-center gap-3 shadow-sm">
                            <i class="fas fa-check-circle text-lg"></i>
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    <form action="{{ route('voter.elections.vote', $election->id) }}" method="POST" id="voting-form">
                        @csrf

                        <!-- Progress Indicator -->
                        <div class="bg-white rounded-xl p-5 mb-6 border border-slate-200 shadow-sm">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-slate-600 text-sm font-medium">Voting Progress</span>
                                <span class="text-teal-600 font-semibold text-sm" id="progress-text">0 / {{ $positions->count() }} positions</span>
                            </div>
                            <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-teal-500 to-teal-400 rounded-full transition-all duration-500" id="progress-bar" style="width: 0%"></div>
                            </div>
                        </div>

                        <!-- Positions and Candidates -->
                        @foreach($positions as $index => $position)
                            <div class="bg-white rounded-2xl mb-6 overflow-hidden border border-slate-200 shadow-sm hover:shadow-md transition-shadow position-card" data-position-id="{{ $position->id }}">
                                <!-- Position Header -->
                                <div class="bg-gradient-to-r from-slate-50 to-white px-6 py-5 border-b border-slate-100">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg shadow-teal-500/20">
                                            <span class="text-white font-bold text-lg">{{ $index + 1 }}</span>
                                        </div>
                                        <div class="flex-grow">
                                            <h3 class="text-xl font-bold text-slate-800">{{ $position->name }}</h3>
                                            <p class="text-slate-500 text-sm flex items-center gap-2">
                                                <i class="fas fa-hand-pointer text-teal-500"></i>
                                                Select {{ $position->max_votes ?? 1 }} candidate{{ ($position->max_votes ?? 1) > 1 ? 's' : '' }}
                                            </p>
                                        </div>
                                        <div class="hidden sm:flex items-center gap-2 px-4 py-2 bg-slate-100 rounded-lg">
                                            <i class="fas fa-users text-teal-600"></i>
                                            <span class="text-slate-700 font-medium">{{ $position->candidates->count() }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Candidates Grid -->
                                <div class="p-6 bg-slate-50/50">
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                        @foreach($position->candidates as $candidate)
                                            <label class="candidate-card cursor-pointer block group">
                                                <input type="{{ ($position->max_votes ?? 1) > 1 ? 'checkbox' : 'radio' }}"
                                                       name="votes[{{ $position->id }}]{{ ($position->max_votes ?? 1) > 1 ? '[]' : '' }}"
                                                       value="{{ $candidate->id }}"
                                                       class="hidden peer vote-input"
                                                       data-position="{{ $position->id }}"
                                                       data-candidate-name="{{ $candidate->name }}">
                                                <div class="relative bg-white border-2 border-slate-200 rounded-xl p-4 transition-all duration-300 peer-checked:border-teal-500 peer-checked:bg-teal-50/50 hover:border-slate-300 hover:shadow-md group-hover:translate-y-[-2px]">
                                                    <!-- Selection Check -->
                                                    <div class="absolute top-4 right-4 w-6 h-6 rounded-full border-2 border-slate-300 flex items-center justify-center transition-all">
                                                        <i class="fas fa-check text-white text-xs"></i>
                                                    </div>

                                                    <div class="flex items-center gap-4">
                                                        <!-- Candidate Photo -->
                                                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200 flex-shrink-0 border border-slate-200">
                                                            @if($candidate->photo)
                                                                <img src="{{ asset('storage/' . $candidate->photo) }}" alt="{{ $candidate->name }}" class="w-full h-full object-cover">
                                                            @else
                                                                <div class="w-full h-full flex items-center justify-center text-slate-400">
                                                                    <i class="fas fa-user text-2xl"></i>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <!-- Candidate Info -->
                                                        <div class="flex-grow min-w-0 pr-8">
                                                            <h4 class="text-base sm:text-lg font-semibold text-slate-800 mb-1 truncate">{{ $candidate->name }}</h4>
                                                            @if($candidate->partylist)
                                                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-teal-100 text-teal-700 rounded-md text-xs font-medium mb-2">
                                                                    <i class="fas fa-flag text-[10px]"></i>
                                                                    {{ $candidate->partylist->name }}
                                                                </div>
                                                            @endif
                                                            @if($candidate->motto)
                                                                <p class="text-slate-500 text-sm italic line-clamp-2">"{{ Str::limit($candidate->motto, 50) }}"</p>
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
                        <div class="bg-white rounded-2xl overflow-hidden border border-slate-200 shadow-sm">
                            <!-- Vote Summary -->
                            <div class="p-6">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-clipboard-list text-teal-600"></i>
                                    </div>
                                    <h4 class="font-semibold text-slate-800 text-lg">Your Selections</h4>
                                </div>
                                <div id="vote-summary" class="text-slate-500 text-sm mb-6 min-h-[60px] p-4 bg-slate-50 rounded-xl border border-slate-200">
                                    <p class="flex items-center gap-2">
                                        <i class="fas fa-info-circle text-slate-400"></i>
                                        Select candidates above to see your choices here.
                                    </p>
                                </div>

                                <!-- Submit Button -->
                                <button type="submit" id="submit-btn" class="w-full py-4 bg-gradient-to-r from-teal-600 to-teal-500 text-white font-bold rounded-xl shadow-lg shadow-teal-500/30 flex items-center justify-center gap-3 text-base transition-all hover:shadow-xl hover:shadow-teal-500/40 hover:scale-[1.01] active:scale-[0.99] disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100">
                                    <i class="fas fa-paper-plane"></i>
                                    <span>Submit My Vote</span>
                                    <i class="fas fa-arrow-right text-sm"></i>
                                </button>

                                <p class="text-center text-slate-400 text-xs mt-4">
                                    <i class="fas fa-lock mr-1"></i>
                                    Once submitted, your vote cannot be changed
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
                        <a href="#" class="text-slate-500 hover:text-teal-600 text-sm transition-colors">Privacy Policy</a>
                        <a href="#" class="text-slate-500 hover:text-teal-600 text-sm transition-colors">Terms of Service</a>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    @push('styles')
        <style>
            .candidate-card input:checked + div {
                border-color: #0d9488 !important;
                background: rgba(13, 148, 136, 0.05) !important;
            }
            .candidate-card input:checked + div .fa-check {
                transform: scale(1) !important;
            }
            .candidate-card input:checked + div > .absolute {
                border-color: #0d9488 !important;
                background: #0d9488 !important;
            }
            .line-clamp-2 {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('voting-form');
                const summary = document.getElementById('vote-summary');
                const progressBar = document.getElementById('progress-bar');
                const progressText = document.getElementById('progress-text');
                const totalPositions = {{ $positions->count() }};

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
                                <i class="fas fa-check-circle text-teal-600 mt-0.5"></i>
                                <div>
                                    <span class="text-slate-700 font-medium">${position}:</span>
                                    <span class="text-slate-600">${candidates.join(', ')}</span>
                                </div>
                            </div>`;
                        }
                        html += '</div>';
                        summary.innerHTML = html;
                    } else {
                        summary.innerHTML = '<p class="flex items-center gap-2"><i class="fas fa-info-circle text-slate-400"></i>Select candidates above to see your choices here.</p>';
                    }
                }

                form.addEventListener('change', updateSummary);
            });
        </script>
    @endpush
@endsection
