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
    <div class="min-h-screen bg-[#F5F5F5] relative overflow-hidden">
        <!-- Background Decoration -->
        <div class="absolute top-0 left-0 w-full h-96 bg-gradient-to-b from-brand-primary/5 to-transparent"></div>

        <div class="relative z-10 min-h-screen flex flex-col">
            <!-- Header -->
            <header class="py-4 px-6 bg-white/90 backdrop-blur-xl border-b border-slate-100 shadow-sm sticky top-0 z-30">
                <div class="max-w-7xl mx-auto flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl gradient-brand flex items-center justify-center shadow-lg shadow-brand-primary/20">
                            <i class="fas fa-vote-yea text-white text-base"></i>
                        </div>
                        <div class="hidden sm:block">
                            <h1 class="text-base font-black text-brand-primary tracking-tight">SecureVote</h1>
                            <p class="text-[10px] font-bold text-brand-accent uppercase tracking-widest">Digital Ballot</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-8">
                        <div class="hidden lg:flex items-center gap-4">
                            <div class="text-right">
                                <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest">Active Voter</p>
                                <p class="text-sm font-bold text-brand-primary">{{ $voter['name'] }}</p>
                            </div>
                            <div class="w-px h-8 bg-slate-100"></div>
                            <div class="text-right">
                                <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest">Election</p>
                                <p class="text-sm font-bold text-brand-primary truncate max-w-[200px]">{{ $election->title }}</p>
                            </div>
                        </div>

                        <a href="{{ route('voter.elections.welcome', $election->code) }}" class="flex items-center gap-2 px-4 py-2 bg-slate-50 text-slate-500 hover:text-brand-primary hover:bg-white rounded-xl transition-all border border-slate-100 text-sm font-bold">
                            <i class="fas fa-arrow-left text-xs"></i>
                            <span>Cancel</span>
                        </a>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <div class="flex-grow px-4 sm:px-6 py-10">
                <div class="max-w-5xl mx-auto">
                    <!-- Progress Section -->
                    <div class="bg-white rounded-[2rem] p-8 mb-10 shadow-xl shadow-slate-200/50 border border-white">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-brand-accent/10 rounded-lg flex items-center justify-center text-brand-accent">
                                    <i class="fas fa-tasks"></i>
                                </div>
                                <span class="text-sm font-black text-slate-400 uppercase tracking-widest">Ballot Completion</span>
                            </div>
                            <span class="text-brand-primary font-black text-lg" id="progress-text">0 / {{ $positions->count() }}</span>
                        </div>
                        <div class="h-4 bg-slate-50 rounded-full p-1 border border-slate-100">
                            <div class="h-full progress-bar-fill rounded-full transition-all duration-700 ease-out" id="progress-bar" style="width: 0%"></div>
                        </div>
                    </div>

                    <!-- Positions & Candidates -->
                    <form action="{{ route('voter.elections.submit', $election->code) }}" method="POST" id="voting-form">
                        @csrf
                        <div class="space-y-12">
                            @foreach($positions as $index => $position)
                                <div class="position-card" data-position-id="{{ $position->id }}">
                                    <div class="flex items-center gap-4 mb-6">
                                        <div class="w-14 h-14 bg-brand-primary text-white rounded-2xl flex items-center justify-center font-black text-xl shadow-xl shadow-brand-primary/20">
                                            {{ $index + 1 }}
                                        </div>
                                        <div>
                                            <h2 class="text-2xl font-black text-brand-primary tracking-tight uppercase">{{ $position->name }}</h2>
                                            <p class="text-slate-400 font-bold text-sm">
                                                Please select <span class="text-brand-accent">{{ $position->max_votes ?? 1 }}</span> candidate{{ ($position->max_votes ?? 1) > 1 ? 's' : '' }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                        @foreach($position->candidates as $candidate)
                                            <label class="candidate-card group cursor-pointer block">
                                                <input type="{{ ($position->max_votes ?? 1) > 1 ? 'checkbox' : 'radio' }}"
                                                       name="votes[{{ $position->id }}]{{ ($position->max_votes ?? 1) > 1 ? '[]' : '' }}"
                                                       value="{{ $candidate->id }}"
                                                       class="vote-input hidden"
                                                       data-position-id="{{ $position->id }}"
                                                       data-position-name="{{ $position->name }}"
                                                       data-candidate-name="{{ $candidate->name }}"
                                                       data-max-votes="{{ $position->max_votes ?? 1 }}">

                                                <div class="card-content relative bg-white rounded-[2rem] border-2 border-transparent p-6 transition-all duration-300 shadow-sm hover:shadow-xl hover:border-brand-accent/20">
                                                    <!-- Selection Indicator -->
                                                    <div class="check-icon hidden absolute -top-3 -right-3 w-8 h-8 bg-brand-accent text-white rounded-full items-center justify-center shadow-lg border-2 border-white z-10">
                                                        <i class="fas fa-check text-xs"></i>
                                                    </div>

                                                    <div class="flex flex-col items-center text-center">
                                                        <div class="w-24 h-24 rounded-[2rem] mb-4 overflow-hidden border-4 border-slate-50 shadow-inner group-hover:scale-105 transition-transform duration-500">
                                                            @if($candidate->photo)
                                                                <img src="{{ asset('storage/' . $candidate->photo) }}" class="w-full h-full object-cover">
                                                            @else
                                                                <div class="w-full h-full gradient-brand flex items-center justify-center">
                                                                    <span class="text-white text-3xl font-black">{{ substr($candidate->name, 0, 1) }}</span>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <h4 class="font-black text-brand-primary text-lg leading-tight mb-1">{{ $candidate->name }}</h4>

                                                        @if($candidate->partylist)
                                                            <div class="px-3 py-1 bg-brand-primary/5 rounded-full border border-brand-primary/10">
                                                                <span class="text-[10px] font-black text-brand-primary uppercase tracking-widest">
                                                                    {{ $candidate->partylist->name }}
                                                                </span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Final Review Button -->
                        <div class="mt-20 flex justify-center">
                            <button type="button" id="review-btn"
                                    class="group px-12 py-6 bg-brand-primary text-white rounded-[2rem] font-black text-xl shadow-2xl shadow-brand-primary/30 hover:shadow-brand-primary/50 hover:-translate-y-1 transition-all flex items-center gap-4">
                                <span>REVIEW BALLOT</span>
                                <i class="fas fa-arrow-right text-brand-accent group-hover:translate-x-1 transition-transform"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Summary Modal -->
        <div id="summary-modal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-brand-primary/60 backdrop-blur-md" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-[3rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white p-8 lg:p-12">
                        <div class="flex items-center justify-between mb-10">
                            <div>
                                <h3 class="text-3xl font-black text-brand-primary uppercase tracking-tight" id="modal-title">Review Your Ballot</h3>
                                <p class="text-slate-400 font-bold">Please verify your selections before final submission.</p>
                            </div>
                            <button type="button" class="close-modal w-12 h-12 flex items-center justify-center bg-slate-50 text-slate-400 rounded-2xl hover:bg-slate-100 transition-colors">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>

                        <div id="modal-summary-content" class="space-y-6 max-h-[50vh] overflow-y-auto pr-4 custom-scrollbar">
                            <!-- Dynamic Content -->
                        </div>

                        <div class="mt-12 space-y-4">
                            <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100">
                                <label class="flex items-start gap-4 cursor-pointer">
                                    <div class="relative flex items-center mt-1">
                                        <input type="checkbox" id="modal-confirm" class="w-6 h-6 text-brand-accent border-slate-300 rounded-lg focus:ring-brand-accent transition-all">
                                    </div>
                                    <span class="text-slate-600 font-bold text-sm leading-relaxed">
                                        I confirm that my selections are final. I understand that I cannot change my vote after this submission.
                                    </span>
                                </label>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <button type="button" class="close-modal py-5 bg-slate-100 text-slate-500 font-black rounded-[2rem] hover:bg-slate-200 transition-all">
                                    GO BACK
                                </button>
                                <button type="button" id="final-submit-btn" disabled
                                        class="py-5 bg-brand-primary text-white font-black rounded-[2rem] shadow-xl shadow-brand-primary/20 opacity-50 cursor-not-allowed transition-all">
                                    CONFIRM & SUBMIT
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('voting-form');
        const voteInputs = document.querySelectorAll('.vote-input');
        const progressBar = document.getElementById('progress-bar');
        const progressText = document.getElementById('progress-text');
        const reviewBtn = document.getElementById('review-btn');
        const summaryModal = document.getElementById('summary-modal');
        const closeModalBtns = document.querySelectorAll('.close-modal');
        const modalSummaryContent = document.getElementById('modal-summary-content');
        const modalConfirm = document.getElementById('modal-confirm');
        const finalSubmitBtn = document.getElementById('final-submit-btn');
        const totalPositions = {{ $positions->count() }};

        function updateProgress() {
            const selectedPositions = new Set();
            voteInputs.forEach(input => {
                if (input.checked) {
                    selectedPositions.add(input.dataset.positionId);
                }
            });

            const count = selectedPositions.size;
            const percentage = (count / totalPositions) * 100;
            progressBar.style.width = percentage + '%';
            progressText.textContent = count + ' / ' + totalPositions;
        }

        voteInputs.forEach(input => {
            input.addEventListener('change', function() {
                const positionId = this.dataset.positionId;
                const maxVotes = parseInt(this.dataset.maxVotes);
                const card = this.closest('.candidate-card');

                if (this.type === 'radio') {
                    document.querySelectorAll(`.vote-input[data-position-id="${positionId}"]`).forEach(i => {
                        i.closest('.candidate-card').querySelector('.card-content').classList.remove('border-brand-accent', 'bg-brand-accent/[0.03]');
                        i.closest('.candidate-card').querySelector('.check-icon').classList.add('hidden');
                    });
                } else {
                    const checkedCount = document.querySelectorAll(`.vote-input[data-position-id="${positionId}"]:checked`).length;
                    if (checkedCount > maxVotes) {
                        this.checked = false;
                        alert(`You can only select up to ${maxVotes} candidates.`);
                        return;
                    }
                }

                if (this.checked) {
                    card.querySelector('.card-content').classList.add('border-brand-accent', 'bg-brand-accent/[0.03]');
                    card.querySelector('.check-icon').classList.remove('hidden');
                } else {
                    card.querySelector('.card-content').classList.remove('border-brand-accent', 'bg-brand-accent/[0.03]');
                    card.querySelector('.check-icon').classList.add('hidden');
                }

                updateProgress();
            });
        });

        reviewBtn.addEventListener('click', function() {
            const selections = {};
            let hasSelection = false;

            voteInputs.forEach(input => {
                if (input.checked) {
                    const posName = input.dataset.positionName;
                    if (!selections[posName]) selections[posName] = [];
                    selections[posName].push(input.dataset.candidateName);
                    hasSelection = true;
                }
            });

            if (!hasSelection) {
                alert('Please select at least one candidate before reviewing.');
                return;
            }

            let html = '';
            for (const [pos, candidates] of Object.entries(selections)) {
                candidates.forEach(candidate => {
                    html += `
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 transition-all hover:bg-white hover:shadow-md grid grid-cols-2 gap-4">
                            <div class="flex flex-col justify-center">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Position</span>
                                <span class="text-base font-black text-brand-primary leading-tight">${pos}</span>
                            </div>
                            <div class="flex flex-col justify-center border-l border-slate-200 pl-6">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Candidate Name</span>
                                <span class="text-base font-black text-brand-primary leading-tight">${candidate}</span>
                            </div>
                        </div>
                    `;
                });
            }

            modalSummaryContent.innerHTML = html;
            summaryModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        });

        closeModalBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                summaryModal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            });
        });

        modalConfirm.addEventListener('change', function() {
            finalSubmitBtn.disabled = !this.checked;
            finalSubmitBtn.classList.toggle('opacity-50', !this.checked);
            finalSubmitBtn.classList.toggle('cursor-not-allowed', !this.checked);
            if (this.checked) {
                finalSubmitBtn.classList.add('hover:-translate-y-1', 'hover:shadow-brand-primary/40');
            } else {
                finalSubmitBtn.classList.remove('hover:-translate-y-1', 'hover:shadow-brand-primary/40');
            }
        });

        finalSubmitBtn.addEventListener('click', function() {
            finalSubmitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> SUBMITTING...';
            finalSubmitBtn.disabled = true;
            form.submit();
        });
    });
</script>
@endpush
