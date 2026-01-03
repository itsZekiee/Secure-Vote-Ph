@extends('voter.layouts.app')

@section('title', 'Voter Registration - SecureVote PH')

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
        .input-brand {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .input-brand:focus {
            border-color: #00D4AA;
            box-shadow: 0 0 0 3px rgba(0, 212, 170, 0.2);
        }
        .feature-icon {
            background: linear-gradient(135deg, rgba(0, 49, 83, 0.1) 0%, rgba(0, 212, 170, 0.1) 100%);
        }
        .custom-checkbox {
            position: relative;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            cursor: pointer;
        }
        .custom-checkbox input[type="checkbox"] {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }
        .checkmark {
            height: 22px;
            width: 22px;
            min-width: 22px;
            background-color: #fff;
            border: 2px solid #cbd5e1;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        .custom-checkbox:hover .checkmark {
            border-color: #00D4AA;
        }
        .custom-checkbox input[type="checkbox"]:checked ~ .checkmark {
            background: linear-gradient(135deg, #003153 0%, #00D4AA 100%);
            border-color: #00D4AA;
        }
        .checkmark i {
            color: white;
            font-size: 12px;
            opacity: 0;
            transform: scale(0);
            transition: all 0.2s ease;
        }
        .custom-checkbox input[type="checkbox"]:checked ~ .checkmark i {
            opacity: 1;
            transform: scale(1);
        }
        .custom-checkbox input[type="checkbox"]:focus ~ .checkmark {
            box-shadow: 0 0 0 3px rgba(0, 212, 170, 0.2);
        }
        .form-transition {
            transition: all 0.3s ease-in-out;
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
                    <a href="{{ route('voter.elections.access') }}" class="text-slate-500 hover:text-brand-primary transition-colors flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i>
                        <span class="hidden sm:inline">Back to Access</span>
                    </a>
                </div>
            </header>

            <!-- Main Content -->
            <div class="max-w-7xl mx-auto px-4 lg:px-8 py-8 lg:py-12">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-start">

                    <!-- Left Side - Form -->
                    <div class="order-2 lg:order-1">
                        <div class="bg-white rounded-3xl shadow-2xl shadow-slate-200/50 overflow-hidden border border-slate-100">
                            <!-- Card Header -->
                            <div class="gradient-brand p-8 text-center relative overflow-hidden">
                                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                                <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full translate-y-1/2 -translate-x-1/2"></div>

                                <div class="relative">
                                    <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                        <i id="form-header-icon" class="fas fa-user-plus text-white text-2xl"></i>
                                    </div>
                                    <h2 id="form-header-title" class="text-2xl font-bold text-white mb-2">Voter Registration</h2>
                                    <p id="form-header-subtitle" class="text-white/80 text-sm">Create your secure voting account</p>
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="p-8">
                                @if($errors->any())
                                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                                        <div class="flex items-center gap-2 text-red-600 mb-2">
                                            <i class="fas fa-exclamation-circle"></i>
                                            <span class="font-semibold">Please fix the following errors:</span>
                                        </div>
                                        <ul class="list-disc list-inside text-red-600 text-sm space-y-1">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if(session('success'))
                                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl">
                                        <div class="flex items-center gap-2 text-green-600">
                                            <i class="fas fa-check-circle"></i>
                                            <span>{{ session('success') }}</span>
                                        </div>
                                    </div>
                                @endif

                                <!-- Login Error Message -->
                                <div id="login-error" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl hidden">
                                    <div class="flex items-center gap-2 text-red-600">
                                        <i class="fas fa-exclamation-circle"></i>
                                        <span id="login-error-message">Invalid credentials or you are not registered as a voter.</span>
                                    </div>
                                </div>

                                <!-- Registration Form -->
                                <form id="register-form" action="{{ route('voter.register.submit') }}" method="POST" class="form-transition">
                                    @csrf

                                    <div class="mb-5">
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                                            <i class="fas fa-user text-brand-accent mr-2"></i>Full Name
                                        </label>
                                        <input type="text" name="name" value="{{ old('name') }}" required
                                               class="w-full px-4 py-3 border border-slate-200 rounded-xl input-brand focus:outline-none text-slate-700"
                                               placeholder="Enter your full name">
                                    </div>

                                    <div class="mb-5">
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                                            <i class="fas fa-envelope text-brand-accent mr-2"></i>Email Address
                                        </label>
                                        <input type="email" name="email" value="{{ old('email') }}" required
                                               class="w-full px-4 py-3 border border-slate-200 rounded-xl input-brand focus:outline-none text-slate-700"
                                               placeholder="Enter your email address">
                                    </div>

                                    <div class="mb-5">
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                                            <i class="fas fa-phone text-brand-accent mr-2"></i>Phone Number
                                        </label>
                                        <input type="tel" name="phone" value="{{ old('phone') }}"
                                               class="w-full px-4 py-3 border border-slate-200 rounded-xl input-brand focus:outline-none text-slate-700"
                                               placeholder="09XXXXXXXXX">
                                    </div>

                                    <div class="mb-5">
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                                            <i class="fas fa-id-card text-brand-accent mr-2"></i>Voter ID (Optional)
                                        </label>
                                        <input type="text" name="voter_id" value="{{ old('voter_id') }}"
                                               class="w-full px-4 py-3 border border-slate-200 rounded-xl input-brand focus:outline-none text-slate-700"
                                               placeholder="Enter your voter ID if available">
                                    </div>

                                    <div class="mb-5">
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                                            <i class="fas fa-lock text-brand-accent mr-2"></i>Password
                                        </label>
                                        <input type="password" name="password" required
                                               class="w-full px-4 py-3 border border-slate-200 rounded-xl input-brand focus:outline-none text-slate-700"
                                               placeholder="Create a secure password">
                                    </div>

                                    <div class="mb-6">
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                                            <i class="fas fa-lock text-brand-accent mr-2"></i>Confirm Password
                                        </label>
                                        <input type="password" name="password_confirmation" required
                                               class="w-full px-4 py-3 border border-slate-200 rounded-xl input-brand focus:outline-none text-slate-700"
                                               placeholder="Confirm your password">
                                    </div>

                                    <div class="mb-6">
                                        <label class="custom-checkbox">
                                            <input type="checkbox" name="terms" id="terms" required>
                                            <span class="checkmark">
                                                <i class="fas fa-check"></i>
                                            </span>
                                            <span class="text-sm text-slate-600 leading-relaxed">
                                                I agree to the <a href="#" class="text-brand-accent hover:underline font-semibold">Terms of Service</a> and <a href="#" class="text-brand-accent hover:underline font-semibold">Privacy Policy</a>.
                                            </span>
                                        </label>
                                    </div>

                                    <button type="submit" class="w-full btn-brand text-white font-bold py-4 px-6 rounded-xl flex items-center justify-center gap-3 text-lg">
                                        <i class="fas fa-user-plus"></i>
                                        Create Account
                                    </button>
                                </form>

                                <!-- Sign In Form (Hidden by default) -->
                                <form id="signin-form" action="{{ route('voter.login') }}" method="POST" class="form-transition hidden">
                                    @csrf

                                    <div class="mb-5">
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                                            <i class="fas fa-envelope text-brand-accent mr-2"></i>Email Address
                                        </label>
                                        <input type="email" name="email" required
                                               class="w-full px-4 py-3 border border-slate-200 rounded-xl input-brand focus:outline-none text-slate-700"
                                               placeholder="Enter your email address">
                                    </div>

                                    <div class="mb-6">
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                                            <i class="fas fa-lock text-brand-accent mr-2"></i>Password
                                        </label>
                                        <input type="password" name="password" required
                                               class="w-full px-4 py-3 border border-slate-200 rounded-xl input-brand focus:outline-none text-slate-700"
                                               placeholder="Enter your password">
                                    </div>

                                    <div class="mb-6 flex items-center justify-between">
                                        <label class="custom-checkbox">
                                            <input type="checkbox" name="remember">
                                            <span class="checkmark">
                                                <i class="fas fa-check"></i>
                                            </span>
                                            <span class="text-sm text-slate-600">Remember me</span>
                                        </label>
                                        <a href="{{ route('password.request') }}" class="text-sm text-brand-accent hover:underline font-semibold">Forgot password?</a>
                                    </div>

                                    <button type="submit" class="w-full btn-brand text-white font-bold py-4 px-6 rounded-xl flex items-center justify-center gap-3 text-lg">
                                        <i class="fas fa-sign-in-alt"></i>
                                        Sign In
                                    </button>
                                </form>

                                <!-- Divider -->
                                <div class="relative my-8">
                                    <div class="absolute inset-0 flex items-center">
                                        <div class="w-full border-t border-slate-200"></div>
                                    </div>
                                    <div class="relative flex justify-center text-sm">
                                        <span id="toggle-text" class="px-4 bg-white text-slate-500">Already registered?</span>
                                    </div>
                                </div>

                                <!-- Toggle Button -->
                                <button id="toggle-form-btn" type="button" class="w-full flex items-center justify-center gap-3 py-4 px-6 bg-slate-100 rounded-2xl text-slate-700 hover:bg-slate-200 transition-all font-semibold hover:shadow-lg">
                                    <i id="toggle-icon" class="fas fa-sign-in-alt"></i>
                                    <span id="toggle-btn-text">Sign In</span>
                                </button>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-center gap-2 text-slate-500 text-sm">
                            <i class="fas fa-lock text-brand-accent"></i>
                            <span>Your information is encrypted and secure</span>
                        </div>
                    </div>

                    <!-- Right Side - Features Section -->
                    <div class="order-1 lg:order-2 lg:sticky lg:top-8">
                        <div class="mb-8">
                            <span class="inline-flex items-center gap-2 px-4 py-2 bg-brand-accent/10 text-brand-accent text-sm font-semibold rounded-full mb-4">
                                <i class="fas fa-star"></i>
                                Quick & Easy Registration
                            </span>
                            <h1 class="text-4xl lg:text-5xl font-bold text-brand-primary mb-4 leading-tight">
                                Join the <span class="text-brand-accent">Future</span><br>of Voting
                            </h1>
                            <p class="text-lg text-slate-600 leading-relaxed">
                                Register in minutes and participate in secure, transparent elections. Your voice matters.
                            </p>
                        </div>

                        <div class="space-y-5">
                            <div class="flex items-start gap-4 p-5 bg-white rounded-2xl shadow-lg shadow-slate-100 border border-slate-100 hover:shadow-xl transition-shadow">
                                <div class="w-14 h-14 feature-icon rounded-2xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-bolt text-brand-accent text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-brand-primary font-bold text-lg mb-1">Quick Setup</h3>
                                    <p class="text-slate-500 text-sm">Register in under 2 minutes with just your basic information.</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4 p-5 bg-white rounded-2xl shadow-lg shadow-slate-100 border border-slate-100 hover:shadow-xl transition-shadow">
                                <div class="w-14 h-14 feature-icon rounded-2xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-shield-alt text-brand-accent text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-brand-primary font-bold text-lg mb-1">Secure & Private</h3>
                                    <p class="text-slate-500 text-sm">Your data is protected with enterprise-grade encryption.</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4 p-5 bg-white rounded-2xl shadow-lg shadow-slate-100 border border-slate-100 hover:shadow-xl transition-shadow">
                                <div class="w-14 h-14 feature-icon rounded-2xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-envelope-open-text text-brand-accent text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-brand-primary font-bold text-lg mb-1">Instant Confirmation</h3>
                                    <p class="text-slate-500 text-sm">Receive email confirmation with your unique voter credentials.</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4 p-5 bg-white rounded-2xl shadow-lg shadow-slate-100 border border-slate-100 hover:shadow-xl transition-shadow">
                                <div class="w-14 h-14 feature-icon rounded-2xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-fingerprint text-brand-accent text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-brand-primary font-bold text-lg mb-1">Anonymous Voting</h3>
                                    <p class="text-slate-500 text-sm">Your vote is completely anonymous—no one can trace it back to you.</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 p-6 bg-gradient-to-br from-brand-primary/5 to-brand-accent/5 rounded-2xl border border-brand-accent/20">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="flex -space-x-2">
                                    <div class="w-8 h-8 rounded-full bg-brand-primary flex items-center justify-center text-white text-xs font-bold">JD</div>
                                    <div class="w-8 h-8 rounded-full bg-brand-accent flex items-center justify-center text-white text-xs font-bold">MR</div>
                                    <div class="w-8 h-8 rounded-full bg-slate-400 flex items-center justify-center text-white text-xs font-bold">+5K</div>
                                </div>
                                <div>
                                    <p class="text-brand-primary font-semibold text-sm">Trusted by thousands</p>
                                    <p class="text-slate-500 text-xs">Over 5,000 successful elections conducted</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="flex items-center gap-1 text-brand-accent">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <span class="text-slate-500 text-sm">4.9/5 user satisfaction</span>
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
            const toggleBtn = document.getElementById('toggle-form-btn');
            const registerForm = document.getElementById('register-form');
            const signinForm = document.getElementById('signin-form');
            const toggleText = document.getElementById('toggle-text');
            const toggleBtnText = document.getElementById('toggle-btn-text');
            const toggleIcon = document.getElementById('toggle-icon');
            const headerIcon = document.getElementById('form-header-icon');
            const headerTitle = document.getElementById('form-header-title');
            const headerSubtitle = document.getElementById('form-header-subtitle');

            let isSignInMode = false;

            toggleBtn.addEventListener('click', function() {
                isSignInMode = !isSignInMode;

                if (isSignInMode) {
                    registerForm.classList.add('hidden');
                    signinForm.classList.remove('hidden');
                    toggleText.textContent = "Don't have an account?";
                    toggleBtnText.textContent = 'Create Account';
                    toggleIcon.className = 'fas fa-user-plus';
                    headerIcon.className = 'fas fa-sign-in-alt text-white text-2xl';
                    headerTitle.textContent = 'Welcome Back';
                    headerSubtitle.textContent = 'Sign in to access your elections';
                } else {
                    signinForm.classList.add('hidden');
                    registerForm.classList.remove('hidden');
                    toggleText.textContent = 'Already registered?';
                    toggleBtnText.textContent = 'Sign In';
                    toggleIcon.className = 'fas fa-sign-in-alt';
                    headerIcon.className = 'fas fa-user-plus text-white text-2xl';
                    headerTitle.textContent = 'Voter Registration';
                    headerSubtitle.textContent = 'Create your secure voting account';
                }

                document.getElementById('login-error').classList.add('hidden');
            });

            // Phone number formatting
            document.querySelector('input[name="phone"]')?.addEventListener('input', function(e) {
                let value = this.value.replace(/\D/g, '');
                if (value.length > 11) value = value.slice(0, 11);
                this.value = value;
            });

            // Voter ID uppercase
            document.querySelector('input[name="voter_id"]')?.addEventListener('input', function(e) {
                this.value = this.value.toUpperCase();
            });
        });
    </script>
@endpush
