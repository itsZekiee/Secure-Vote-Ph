@extends('voter.layouts.app')

@section('title', 'Cast Your Vote - ' . $election->title)

@push('styles')
    <style>
        :root {
            --brand-primary: #003153;
            --brand-accent: #00D4AA;
            --brand-bg: #F8FAFC;
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

        .bg-brand-primary {
            background-color: var(--brand-primary);
        }

        .bg-brand-accent {
            background-color: var(--brand-accent);
        }

        .position-card {
            background: white;
            border-radius: 1.5rem;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            border: 1px solid #E2E8F0;
        }

        .position-header {
            background: linear-gradient(to right, var(--brand-primary) 0%, var(--brand-accent) 100%);
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: white;
        }

        .candidate-grid {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 0.75rem;
            padding: 1.5rem;
        }

        @media (min-width: 768px) {
            .candidate-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        .candidate-card {
            position: relative;
            cursor: pointer;
            border: 2px solid #E2E8F0;
            border-radius: 0.75rem;
            padding: 1rem 1.25rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .candidate-card:hover {
            border-color: var(--brand-accent);
            background-color: rgba(0, 212, 170, 0.02);
        }

        .candidate-card.selected {
            border-color: var(--brand-accent);
            background-color: rgba(0, 212, 170, 0.05);
        }

        .check-circle {
            width: 28px;
            height: 28px;
            border: 2px solid #E2E8F0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .candidate-card.selected .check-circle {
            background-color: var(--brand-accent);
            border-color: var(--brand-accent);
        }

        .check-circle i {
            color: white;
            font-size: 0.875rem;
            display: none;
        }

        .candidate-card.selected .check-circle i {
            display: block;
        }

        .progress-indicator {
            font-weight: 700;
            color: var(--brand-primary);
        }

        .btn-review {
            background: var(--brand-primary);
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 9999px;
            font-weight: 800;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.875rem;
        }

        .btn-review:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(0, 49, 83, 0.3);
        }

        .abstain-card {
            grid-column: 1 / -1;
            border-style: dashed;
            justify-content: center;
            background-color: #F8FAFC;
        }

        /* Glass effect for header */
        .glass-header {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .success-overlay {
            position: fixed;
            inset: 0;
            background: white;
            z-index: 200;
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem;
        }

        .success-checkmark {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-accent) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem;
            color: white;
            font-size: 3.5rem;
            box-shadow: 0 20px 40px -10px rgba(0, 212, 170, 0.3);
        }

        .encryption-badge {
            display: inline-flex;
            items-center: center;
            gap: 0.75rem;
            padding: 0.75rem 1.5rem;
            background: rgba(0, 212, 170, 0.05);
            border: 1px solid rgba(0, 212, 170, 0.2);
            border-radius: 9999px;
            color: var(--brand-primary);
            font-weight: 700;
            margin-top: 3rem;
        }
    </style>
@endpush

@section('content')
    <div class="min-h-screen bg-slate-50">
        <!-- Top Navigation -->
        <header class="glass-header sticky top-0 z-50 px-6 py-4">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <a href="{{ route('voter.elections.welcome', $election->code) }}"
                    class="flex items-center gap-2 text-brand-primary font-bold hover:opacity-70 transition-opacity">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Home</span>
                </a>

                <div class="progress-indicator flex items-center gap-2">
                    <span>Progress:</span>
                    <span id="progress-text-top" class="text-brand-accent">0 / {{ $positions->count() }}</span>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-4xl mx-auto px-6 py-8">
            <!-- Title Section -->
            <div class="text-center mb-10">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 bg-brand-accent/10 text-brand-accent rounded-full text-[10px] font-black uppercase tracking-widest mb-4 border border-brand-accent/20">
                    <i class="fas fa-check-double"></i>
                    <span>Official Ballot</span>
                </div>
                <h1 class="text-4xl font-black text-brand-primary mb-3 tracking-tight uppercase">Cast Your Ballot</h1>
                <p class="text-slate-500 text-base font-medium">Please review each position and select your preferred
                    candidates.</p>
            </div>

            <form action="{{ route('voter.elections.submit', $election->code) }}" method="POST" id="voting-form">
                @csrf
                <input type="hidden" name="latitude" id="lat-input">
                <input type="hidden" name="longitude" id="lng-input">
                <div class="space-y-12">
                    @foreach($positions as $position)
                        <div class="position-card" data-position-id="{{ $position->id }}">
                            <!-- Position Header -->
                            <div class="position-header">
                                <div class="flex items-center gap-4">
                                    <i class="fas fa-user text-lg opacity-80"></i>
                                    <h2 class="text-xl font-bold uppercase tracking-wide">{{ $position->title }}</h2>
                                </div>
                                <div
                                    class="selection-badge hidden items-center gap-2 px-3 py-1 bg-white/20 rounded-full text-xs font-bold backdrop-blur-sm">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Selected</span>
                                </div>
                            </div>

                            <!-- Candidates Grid -->
                            <div class="candidate-grid">
                                @foreach($position->candidates as $candidate)
                                    <label class="candidate-card group">
                                        <input type="{{ ($position->max_votes ?? 1) > 1 ? 'checkbox' : 'radio' }}"
                                            name="votes[{{ $position->id }}]{{ ($position->max_votes ?? 1) > 1 ? '[]' : '' }}"
                                            value="{{ $candidate->id }}" class="vote-input hidden"
                                            data-position-id="{{ $position->id }}" data-position-name="{{ $position->title }}"
                                            data-candidate-name="{{ $candidate->name }}"
                                            data-max-votes="{{ $position->max_votes ?? 1 }}">

                                        <div class="flex flex-col">
                                            <span
                                                class="text-lg font-bold text-brand-primary group-hover:text-brand-accent transition-colors">
                                                {{ $candidate->name }}
                                            </span>
                                            @if($candidate->partylist)
                                                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">
                                                    {{ $candidate->partylist->name }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="check-circle">
                                            <i class="fas fa-check"></i>
                                        </div>
                                    </label>
                                @endforeach

                                <!-- Abstain Option -->
                                <label class="candidate-card abstain-card group">
                                    <input type="radio" name="votes[{{ $position->id }}]" value="abstain"
                                        class="vote-input hidden abstain-input" data-position-id="{{ $position->id }}"
                                        data-position-name="{{ $position->title }}" data-candidate-name="Abstain">

                                    <span
                                        class="text-base font-bold text-slate-400 group-hover:text-slate-600 transition-colors">
                                        Abstain from this position
                                    </span>

                                    <div class="check-circle">
                                        <i class="fas fa-check"></i>
                                    </div>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Action Button -->
                <div class="mt-20 flex justify-center">
                    <button type="button" id="review-btn" class="btn-review">
                        Review Ballot
                    </button>
                </div>
            </form>

            <div id="review-warning" class="hidden fixed top-24 left-1/2 -translate-x-1/2 z-[999]
                                bg-red-50 border border-red-200 text-red-700
                                px-6 py-4 rounded-xl shadow-lg text-sm font-bold">
                Please select a candidate (or Abstain) for every position before reviewing your ballot.
            </div>

        </main>
    </div>

    <!-- Summary Modal (Keep structure but match styles) -->
    <div id="summary-modal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-brand-primary/80 backdrop-blur-sm" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-brand-primary p-8 flex items-center justify-between text-white">
                    <div class="flex items-center gap-4">
                        <i class="fas fa-clipboard-check text-2xl text-brand-accent"></i>
                        <h3 class="text-2xl font-black uppercase tracking-tight" id="modal-title">Review Your Ballot</h3>
                    </div>
                    <button type="button" class="close-modal opacity-50 hover:opacity-100 transition-opacity">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div class="p-8">
                    <div id="modal-summary-content" class="space-y-3 max-h-[50vh] overflow-y-auto pr-2">
                        <!-- Dynamic Content -->
                    </div>

                    <div class="mt-8 p-6 bg-amber-50 rounded-2xl border border-amber-100 flex gap-4">
                        <i class="fas fa-exclamation-triangle text-amber-500 mt-1"></i>
                        <div>
                            <h4 class="text-amber-800 font-bold text-sm uppercase tracking-wider mb-1">Final Confirmation
                            </h4>
                            <p class="text-amber-700/80 text-xs font-medium leading-relaxed">
                                Please review your selections carefully. Once submitted, your vote cannot be changed.
                            </p>
                        </div>
                    </div>

                    <div class="mt-8 space-y-4">
                        <label
                            class="flex items-center gap-3 p-4 bg-slate-50 rounded-xl border border-slate-100 cursor-pointer">
                            <input type="checkbox" id="modal-confirm"
                                class="w-5 h-5 text-brand-accent border-slate-300 rounded focus:ring-brand-accent">
                            <span class="text-slate-600 font-bold text-xs">I confirm that my selections are final and
                                correct.</span>
                        </label>

                        <div class="grid grid-cols-2 gap-4">
                            <button type="button"
                                class="close-modal py-4 bg-slate-100 text-slate-500 font-black rounded-2xl hover:bg-slate-200 transition-all uppercase tracking-widest text-[10px]">
                                Go Back
                            </button>
                            <button type="button" id="final-submit-btn" disabled
                                class="py-4 bg-brand-primary text-white font-black rounded-2xl opacity-50 cursor-not-allowed transition-all uppercase tracking-widest text-[10px]">
                                Submit Ballot
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Screen Overlay -->
    <div id="success-screen" class="success-overlay">
        <div class="success-checkmark">
            <i class="fas fa-check"></i>
        </div>
        <h2 class="text-4xl font-black text-brand-primary mb-4 tracking-tight">Vote Successfully Cast!</h2>
        <p class="text-slate-500 text-lg font-medium max-w-md mx-auto leading-relaxed">
            Thank you for participating in the democratic process. Your vote has been securely recorded and counted.
        </p>
        <div class="encryption-badge">
            <i class="fas fa-shield-alt text-brand-accent"></i>
            <span>Encrypted & Anonymous</span>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('voting-form');
            const voteInputs = document.querySelectorAll('.vote-input');
            const progressTextTop = document.getElementById('progress-text-top');
            const reviewBtn = document.getElementById('review-btn');
            const summaryModal = document.getElementById('summary-modal');
            const closeModalBtns = document.querySelectorAll('.close-modal');
            const modalSummaryContent = document.getElementById('modal-summary-content');
            const modalConfirm = document.getElementById('modal-confirm');
            const finalSubmitBtn = document.getElementById('final-submit-btn');
            const totalPositions = {{ $positions->count() }};
            const requireGeo = {{ $election->require_geo_verification ? 'true' : 'false' }};

            // Get location on load if required
            if (requireGeo) {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            document.getElementById('lat-input').value = position.coords.latitude;
                            document.getElementById('lng-input').value = position.coords.longitude;
                            console.log('Location acquired:', position.coords.latitude, position.coords.longitude);
                        },
                        (error) => {
                            console.error('Error getting location:', error);
                            alert('This election requires geo-verification. Please enable location services to vote.');
                        },
                        { enableHighAccuracy: true }
                    );
                } else {
                    alert('Geolocation is not supported by your browser.');
                }
            }

            function updateProgress() {
                const selectedPositions = new Set();
                voteInputs.forEach(input => {
                    if (input.checked) {
                        selectedPositions.add(input.dataset.positionId);
                    }
                });

                const count = selectedPositions.size;
                progressTextTop.textContent = count + ' / ' + totalPositions;
            }

            voteInputs.forEach(input => {
                input.addEventListener('change', function () {
                    const positionId = this.dataset.positionId;
                    const maxVotes = parseInt(this.dataset.maxVotes);
                    const positionCard = this.closest('.position-card');
                    const selectionBadge = positionCard.querySelector('.selection-badge');

                    if (this.type === 'radio') {
                        document.querySelectorAll(`.vote-input[data-position-id="${positionId}"]`).forEach(i => {
                            if (i !== this) {
                                i.checked = false;
                                i.closest('.candidate-card').classList.remove('selected');
                            }
                        });
                    } else {
                        // If checking a candidate, uncheck abstain
                        const abstainInput = document.querySelector(`.abstain-input[data-position-id="${positionId}"]`);
                        if (abstainInput && abstainInput.checked) {
                            abstainInput.checked = false;
                            abstainInput.closest('.candidate-card').classList.remove('selected');
                        }

                        const checkedCount = document.querySelectorAll(`.vote-input[data-position-id="${positionId}"]:checked`).length;
                        if (checkedCount > maxVotes) {
                            this.checked = false;
                            return;
                        }
                    }

                    if (this.checked) {
                        this.closest('.candidate-card').classList.add('selected');
                    } else {
                        this.closest('.candidate-card').classList.remove('selected');
                    }

                    // Update Selection Badge
                    const hasSelection = positionCard.querySelectorAll('.vote-input:checked').length > 0;
                    if (hasSelection) {
                        selectionBadge.classList.remove('hidden');
                        selectionBadge.classList.add('flex');
                    } else {
                        selectionBadge.classList.add('hidden');
                        selectionBadge.classList.remove('flex');
                    }

                    updateProgress();
                });
            });

            reviewBtn.addEventListener('click', function () {
                const positionCards = document.querySelectorAll('.position-card');
                const warning = document.getElementById('review-warning');

                // Check if every position has a selection
                for (let position of positionCards) {
                    const selected = position.querySelector('.vote-input:checked');
                    if (!selected) {
                        // Show notification
                        warning.classList.remove('hidden');

                        // Auto-hide after 3 seconds
                        setTimeout(() => {
                            warning.classList.add('hidden');
                        }, 3000);

                        return; // STOP review modal
                    }
                }

                // Hide warning if all is good
                warning.classList.add('hidden');

                const selections = {};

                voteInputs.forEach(input => {
                    if (input.checked) {
                        const posName = input.dataset.positionName;
                        if (!selections[posName]) selections[posName] = [];
                        selections[posName].push(input.dataset.candidateName);
                    }
                });

                let html = '';
                for (const [pos, candidates] of Object.entries(selections)) {
                    candidates.forEach(candidate => {
                        html += `
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100">
                        <div>
                            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">${pos}</div>
                            <div class="text-lg font-bold text-brand-primary">${candidate}</div>
                        </div>
                        <i class="fas fa-check-circle text-brand-accent text-xl"></i>
                    </div>
                `;
                    });
                }

                modalSummaryContent.innerHTML = html;
                summaryModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            });


            closeModalBtns.forEach(btn => {
                btn.addEventListener('click', function () {
                    summaryModal.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                });
            });

            modalConfirm.addEventListener('change', function () {
                finalSubmitBtn.disabled = !this.checked;
                finalSubmitBtn.classList.toggle('opacity-50', !this.checked);
                finalSubmitBtn.classList.toggle('cursor-not-allowed', !this.checked);
            });

            finalSubmitBtn.addEventListener('click', function () {
                finalSubmitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> SUBMITTING...';
                finalSubmitBtn.disabled = true;

                if (requireGeo && navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            document.getElementById('lat-input').value = position.coords.latitude;
                            document.getElementById('lng-input').value = position.coords.longitude;
                            submitBallot();
                        },
                        (error) => {
                            console.error('Error getting location:', error);
                            alert('Location access is required to submit your vote. Please enable GPS.');
                            finalSubmitBtn.innerHTML = 'Submit Ballot';
                            finalSubmitBtn.disabled = false;
                        },
                        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                    );
                } else {
                    submitBallot();
                }
            });

            function submitBallot() {
                const formData = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                    .then(async response => {
                        if (response.ok) {
                            // Successfully cast vote
                            summaryModal.classList.add('hidden');
                            const successScreen = document.getElementById('success-screen');
                            successScreen.style.display = 'flex';

                            // Update success message if there are remaining votes
                            const responseData = await response.json();
                            if (responseData.remaining_votes > 0) {
                                const successTitle = successScreen.querySelector('h2');
                                const successMessage = successScreen.querySelector('p');
                                if (successTitle) successTitle.textContent = 'Ballot Submitted!';
                                if (successMessage) successMessage.textContent = responseData.message + ' Redirecting you back to cast your next vote...';
                            }

                            setTimeout(() => {
                                window.location.href = "{{ route('voter.elections.welcome', $election->code) }}";
                            }, 3000);
                        } else {
                            const data = await response.json();
                            throw new Error(data.message || data.error || 'Something went wrong');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error submitting vote: ' + error.message);
                        finalSubmitBtn.innerHTML = 'Submit Ballot';
                        finalSubmitBtn.disabled = false;
                    });
            }
        });
    </script>
@endpush