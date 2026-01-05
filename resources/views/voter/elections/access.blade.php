@extends('voter.layouts.app')

@section('title', 'Access Election - SecureVote PH')

@push('styles')
    <style>
        .gradient-brand {
            background: linear-gradient(135deg, #003153 0%, #00D4AA 100%);
        }
        .gradient-brand-reverse {
            background: linear-gradient(135deg, #00D4AA 0%, #003153 100%);
        }
        .text-brand-primary { color: #003153; }
        .text-brand-accent { color: #00D4AA; }
        .bg-brand-primary { background-color: #003153; }
        .bg-brand-accent { background-color: #00D4AA; }
        .border-brand-accent { border-color: #00D4AA; }
        .shadow-brand { box-shadow: 0 20px 40px -10px rgba(0, 49, 83, 0.3); }
        .btn-brand {
            background: linear-gradient(135deg, #003153 0%, #00D4AA 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-brand:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px -10px rgba(0, 212, 170, 0.5);
        }
        .tab-brand-active {
            background: linear-gradient(135deg, #003153 0%, #00D4AA 100%);
            color: white;
            box-shadow: 0 10px 30px -5px rgba(0, 49, 83, 0.4);
        }
        .input-brand:focus {
            border-color: #00D4AA;
            box-shadow: 0 0 0 3px rgba(0, 212, 170, 0.2);
        }
        .feature-icon {
            background: linear-gradient(135deg, rgba(0, 49, 83, 0.1) 0%, rgba(0, 212, 170, 0.1) 100%);
        }
    </style>
@endpush

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-white to-slate-50 relative overflow-hidden">
        <!-- Background Decorative Elements -->
        <div class="absolute top-0 right-0 w-1/2 h-full gradient-brand opacity-5"></div>
        <div class="absolute bottom-0 left-1/4 w-96 h-96 bg-brand-accent/10 rounded-full blur-3xl"></div>
        <div class="absolute top-1/4 right-1/4 w-64 h-64 bg-brand-primary/10 rounded-full blur-3xl"></div>

        <div class="relative z-10 min-h-screen">
            <!-- Header -->
            <header class="py-6 px-4 lg:px-8">
                <div class="max-w-7xl mx-auto flex items-center justify-between">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                        <div class="w-12 h-12 rounded-2xl gradient-brand flex items-center justify-center shadow-lg shadow-brand-primary/30 group-hover:scale-110 transition-transform">
                            <i class="fas fa-vote-yea text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-brand-primary">SecureVote</h1>
                            <p class="text-xs text-slate-500">Philippines</p>
                        </div>
                    </a>
                    <a href="{{ route('home') }}" class="text-slate-500 hover:text-brand-primary transition-colors flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i>
                        <span class="hidden sm:inline">Back to Home</span>
                    </a>
                </div>
            </header>

            <!-- Main Content - Two Column Layout -->
            <div class="max-w-7xl mx-auto px-4 lg:px-8 py-8 lg:py-16">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">

                    <!-- Left Side - Form -->
                    <div class="order-2 lg:order-1">
                        <div class="bg-white rounded-3xl shadow-2xl shadow-slate-200/50 overflow-hidden border border-slate-100">
                            <!-- Card Header -->
                            <div class="gradient-brand p-8 text-center relative overflow-hidden">
                                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                                <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full translate-y-1/2 -translate-x-1/2"></div>

                                <div class="relative">
                                    <div class="w-20 h-20 bg-white/20 rounded-3xl flex items-center justify-center mx-auto mb-4 backdrop-blur-sm">
                                        <i class="fas fa-shield-alt text-white text-3xl"></i>
                                    </div>
                                    <h2 class="text-2xl font-bold text-white mb-2">Access Election</h2>
                                    <p class="text-white/80 text-sm">Enter your election code or paste the voting link</p>
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="p-8">
                                @if($errors->any())
                                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-xl mb-6 flex items-start gap-3">
                                        <i class="fas fa-exclamation-circle mt-0.5 text-red-500"></i>
                                        <div>
                                            @foreach($errors->all() as $error)
                                                <p class="text-sm">{{ $error }}</p>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if(session('success'))
                                    <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-xl mb-6 flex items-start gap-3">
                                        <i class="fas fa-check-circle mt-0.5 text-emerald-500"></i>
                                        <p class="text-sm">{{ session('success') }}</p>
                                    </div>
                                @endif

                                <form action="{{ route('voter.elections.verify') }}" method="POST">
                                    @csrf

                                    <!-- Tab Selection -->
                                    <div class="flex mb-8 bg-slate-100 rounded-2xl p-1.5">
                                        <button type="button" id="codeTab" onclick="switchTab('code')" class="flex-1 py-3 px-4 rounded-xl text-sm font-semibold transition-all duration-300 tab-brand-active">
                                            <i class="fas fa-hashtag mr-2"></i>Code
                                        </button>
                                        <button type="button" id="linkTab" onclick="switchTab('link')" class="flex-1 py-3 px-4 rounded-xl text-sm font-semibold transition-all duration-300 text-slate-500 hover:text-slate-700">
                                            <i class="fas fa-link mr-2"></i>Link
                                        </button>
                                    </div>

                                    <input type="hidden" name="input_type" id="input_type" value="code">

                                    <!-- Code Input -->
                                    <div id="codeInput" class="mb-6">
                                        <label class="block text-sm font-semibold text-slate-700 mb-3">
                                            <i class="fas fa-key text-brand-accent mr-2"></i>Election Code
                                        </label>
                                        <input type="text" name="election_code" placeholder="XXXXXX" maxlength="6"
                                               class="input-brand w-full px-5 py-4 text-center text-2xl font-bold tracking-[0.5em] border-2 border-slate-200 rounded-2xl focus:outline-none transition-all uppercase"
                                               value="{{ old('election_code') }}">
                                        <p class="text-xs text-slate-500 mt-3 text-center">Enter the 6-character code provided by your organization</p>
                                    </div>

                                    <!-- Link Input -->
                                    <div id="linkInput" class="mb-6 hidden">
                                        <label class="block text-sm font-semibold text-slate-700 mb-3">
                                            <i class="fas fa-globe text-brand-accent mr-2"></i>Election Link
                                        </label>
                                        <input type="url" name="election_link" placeholder="https://securevote.ph/vote/..."
                                               class="input-brand w-full pl-12 pr-5 py-4 border-2 border-slate-200 rounded-2xl focus:outline-none transition-all"
                                               value="{{ old('election_link') }}">
                                        <p class="text-xs text-slate-500 mt-3 text-center">Paste the complete election URL shared with you</p>
                                    </div>

                                    <button type="submit" class="btn-brand w-full py-4 text-white font-bold rounded-2xl shadow-lg flex items-center justify-center gap-3 text-lg">
                                        <span>Continue</span>
                                        <i class="fas fa-arrow-right"></i>
                                    </button>
                                </form>

                                <!-- Divider -->
                                <div class="relative my-8">
                                    <div class="absolute inset-0 flex items-center">
                                        <div class="w-full border-t border-slate-200"></div>
                                    </div>
                                    <div class="relative flex justify-center text-sm">
                                        <span class="px-4 bg-white text-slate-500">Need help?</span>
                                    </div>
                                </div>

                                <!-- Help Links -->
                                <div class="grid grid-cols-2 gap-4">
                                    <a href="#" class="flex items-center justify-center gap-2 py-3 px-4 bg-slate-50 rounded-xl text-slate-600 hover:bg-slate-100 transition-colors text-sm font-medium">
                                        <i class="fas fa-question-circle text-brand-accent"></i>
                                        <span>How it works</span>
                                    </a>
                                    <a href="#" class="flex items-center justify-center gap-2 py-3 px-4 bg-slate-50 rounded-xl text-slate-600 hover:bg-slate-100 transition-colors text-sm font-medium">
                                        <i class="fas fa-headset text-brand-accent"></i>
                                        <span>Contact Support</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Security Badge -->
                        <div class="mt-6 flex items-center justify-center gap-2 text-slate-500 text-sm">
                            <i class="fas fa-lock text-brand-accent"></i>
                            <span>256-bit SSL encrypted connection</span>
                        </div>
                    </div>

                    <!-- Right Side - Features Section -->
                    <div class="order-1 lg:order-2">
                        <div class="mb-8">
                            <h1 class="text-4xl lg:text-5xl font-bold text-brand-primary mb-4 leading-tight">
                                Secure, <span class="text-brand-accent">Digital</span><br>Voting
                            </h1>
                            <p class="text-lg text-slate-600 leading-relaxed">
                                Experience the future of democratic participation with cutting-edge security, real-time analytics, and verified results.
                            </p>
                        </div>

                        <div class="space-y-6">
                            <!-- Feature 1 -->
                            <div class="flex items-start gap-4 p-5 bg-white rounded-2xl shadow-lg shadow-slate-100 border border-slate-100 hover:shadow-xl transition-shadow">
                                <div class="w-14 h-14 feature-icon rounded-2xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-bolt text-brand-accent text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-brand-primary font-bold text-lg mb-1">Instant Access</h3>
                                    <p class="text-slate-500 text-sm">Vote in seconds with your unique election code. No complicated registration required.</p>
                                </div>
                            </div>

                            <!-- Feature 2 -->
                            <div class="flex items-start gap-4 p-5 bg-white rounded-2xl shadow-lg shadow-slate-100 border border-slate-100 hover:shadow-xl transition-shadow">
                                <div class="w-14 h-14 feature-icon rounded-2xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-shield-alt text-brand-accent text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-brand-primary font-bold text-lg mb-1">Bank-Level Security</h3>
                                    <p class="text-slate-500 text-sm">End-to-end encryption ensures your vote remains private and tamper-proof.</p>
                                </div>
                            </div>

                            <!-- Feature 3 -->
                            <div class="flex items-start gap-4 p-5 bg-white rounded-2xl shadow-lg shadow-slate-100 border border-slate-100 hover:shadow-xl transition-shadow">
                                <div class="w-14 h-14 feature-icon rounded-2xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-check-double text-brand-accent text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-brand-primary font-bold text-lg mb-1">Verified Results</h3>
                                    <p class="text-slate-500 text-sm">Transparent and auditable election results you can trust.</p>
                                </div>
                            </div>

                            <!-- Feature 4 -->
                            <div class="flex items-start gap-4 p-5 bg-white rounded-2xl shadow-lg shadow-slate-100 border border-slate-100 hover:shadow-xl transition-shadow">
                                <div class="w-14 h-14 feature-icon rounded-2xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-mobile-alt text-brand-accent text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-brand-primary font-bold text-lg mb-1">Vote Anywhere</h3>
                                    <p class="text-slate-500 text-sm">Access from any device—mobile, tablet, or desktop—anytime, anywhere.</p>
                                </div>
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
        function switchTab(tab) {
            const codeTab = document.getElementById('codeTab');
            const linkTab = document.getElementById('linkTab');
            const codeInput = document.getElementById('codeInput');
            const linkInput = document.getElementById('linkInput');
            const inputType = document.getElementById('input_type');

            if (tab === 'code') {
                codeTab.classList.add('tab-brand-active');
                codeTab.classList.remove('text-slate-500', 'hover:text-slate-700');
                linkTab.classList.remove('tab-brand-active');
                linkTab.classList.add('text-slate-500', 'hover:text-slate-700');
                codeInput.classList.remove('hidden');
                linkInput.classList.add('hidden');
                inputType.value = 'code';
            } else {
                linkTab.classList.add('tab-brand-active');
                linkTab.classList.remove('text-slate-500', 'hover:text-slate-700');
                codeTab.classList.remove('tab-brand-active');
                codeTab.classList.add('text-slate-500', 'hover:text-slate-700');
                linkInput.classList.remove('hidden');
                codeInput.classList.add('hidden');
                inputType.value = 'link';
            }
        }

        document.querySelector('input[name="election_code"]')?.addEventListener('input', function(e) {
            this.value = this.value.toUpperCase();
        });
    </script>
@endpush
