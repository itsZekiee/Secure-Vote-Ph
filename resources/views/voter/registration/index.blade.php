@extends('voter.layouts.app')

@section('title', 'Voter Registration - ' . ($election->title ?? 'SecureVote PH'))

@push('styles')
    <style>
        .gradient-brand {
            background: linear-gradient(135deg, #003153 0%, #00D4AA 100%);
        }

        .gradient-brand-reverse {
            background: linear-gradient(135deg, #00D4AA 0%, #003153 100%);
        }

        .text-brand-primary {
            color: #003153;
        }

        .text-brand-accent {
            color: #00D4AA;
        }

        .bg-brand-primary {
            background-color: #003153;
        }

        .bg-brand-accent {
            background-color: #00D4AA;
        }

        .border-brand-accent {
            border-color: #00D4AA;
        }

        .shadow-brand {
            box-shadow: 0 20px 40px -10px rgba(0, 49, 83, 0.3);
        }

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

        .custom-checkbox input[type="checkbox"]:checked~.checkmark {
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

        .custom-checkbox input[type="checkbox"]:checked~.checkmark i {
            opacity: 1;
            transform: scale(1);
        }

        .custom-checkbox input[type="checkbox"]:focus~.checkmark {
            box-shadow: 0 0 0 3px rgba(0, 212, 170, 0.2);
        }

        .form-transition {
            transition: all 0.3s ease-in-out;
        }

        .election-badge {
            background: linear-gradient(135deg, rgba(0, 212, 170, 0.1) 0%, rgba(0, 49, 83, 0.1) 100%);
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
                        <div
                            class="w-12 h-12 rounded-2xl gradient-brand flex items-center justify-center shadow-lg shadow-brand-primary/30 group-hover:scale-110 transition-transform">
                            <i class="fas fa-vote-yea text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-brand-primary">SecureVote</h1>
                            <p class="text-xs text-slate-500">Philippines</p>
                        </div>
                    </a>
                    <a href="{{ route('voter.elections.access') }}"
                        class="text-slate-500 hover:text-brand-primary transition-colors flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i>
                        <span class="hidden sm:inline">Back to Access</span>
                    </a>
                </div>
            </header>

            <!-- Election Info Banner -->
            @isset($election)
                <div class="max-w-7xl mx-auto px-4 lg:px-8 mb-6">
                    <div class="election-badge rounded-2xl p-4 border border-brand-accent/20">
                        <div class="flex items-center justify-between flex-wrap gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 gradient-brand rounded-xl flex items-center justify-center">
                                    <i class="fas fa-ballot-check text-white text-lg"></i>
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold text-brand-primary">{{ $election->title }}</h2>
                                    <p class="text-sm text-slate-500">Election Code: <span
                                            class="font-mono font-bold text-brand-accent">{{ $election->code }}</span></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 text-sm">
                                <div class="flex items-center gap-2 text-slate-600">
                                    <i class="fas fa-calendar text-brand-accent"></i>
                                    <span>{{ $election->start_date->format('M d, Y') }} -
                                        {{ $election->end_date->format('M d, Y') }}</span>
                                </div>
                                @if($election->organization)
                                    <div class="flex items-center gap-2 text-slate-600">
                                        <i class="fas fa-building text-brand-accent"></i>
                                        <span>{{ $election->organization->name }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endisset

            <!-- Main Content -->
            <div class="max-w-7xl mx-auto px-4 lg:px-8 py-8 lg:py-12">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-start">

                    <!-- Left Side - Form -->
                    <div class="order-2 lg:order-1">
                        <div
                            class="bg-white rounded-3xl shadow-2xl shadow-slate-200/50 overflow-hidden border border-slate-100">
                            <!-- Card Header -->
                            <div class="gradient-brand p-8 text-center relative overflow-hidden">
                                <div
                                    class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2">
                                </div>
                                <div
                                    class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full translate-y-1/2 -translate-x-1/2">
                                </div>

                                <div class="relative">
                                    <div
                                        class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                        <i id="form-header-icon" class="fas fa-user-plus text-white text-2xl"></i>
                                    </div>
                                    <h2 id="form-header-title" class="text-2xl font-bold text-white mb-2">Voter Registration
                                    </h2>
                                    <p id="form-header-subtitle" class="text-white/80 text-sm">Create your secure voting
                                        account</p>
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

                                @if(session('info'))
                                    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-xl">
                                        <div class="flex items-center gap-2 text-blue-600">
                                            <i class="fas fa-info-circle"></i>
                                            <span>{{ session('info') }}</span>
                                        </div>
                                    </div>
                                @endif

                                <!-- Login Error Message -->
                                <div id="login-error" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl hidden">
                                    <div class="flex items-center gap-2 text-red-600">
                                        <i class="fas fa-exclamation-circle"></i>
                                        <span id="login-error-message">Invalid credentials or you are not registered as a
                                            voter.</span>
                                    </div>
                                </div>

                                @if($registrationOver)
                                    <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                                        <div class="flex items-start gap-3 text-amber-700">
                                            <i class="fas fa-info-circle mt-1"></i>
                                            <div>
                                                <p class="font-bold">Registration is closed</p>
                                                <p class="text-sm">The deadline for registration has passed. If you have already
                                                    registered, please sign in to cast your vote.</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Registration Form -->
                                <form id="register-form"
                                    action="{{ route('voter.registration.store', $election->code ?? '') }}" method="POST"
                                    class="form-transition {{ $registrationOver ? 'hidden' : '' }}">
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
                                        <input type="tel" name="phone" value="{{ old('phone') }}" required
                                            class="w-full px-4 py-3 border border-slate-200 rounded-xl input-brand focus:outline-none text-slate-700"
                                            placeholder="09XXXXXXXXX">
                                    </div>

                                    <div class="mb-5">
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                                            <i class="fas fa-id-card text-brand-accent mr-2"></i>Student/Employee ID
                                            (Optional)
                                        </label>
                                        <input type="text" name="student_id" value="{{ old('student_id') }}"
                                            class="w-full px-4 py-3 border border-slate-200 rounded-xl input-brand focus:outline-none text-slate-700"
                                            placeholder="Enter your ID if available">
                                    </div>

                                    <div class="mb-5">
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                                            <i class="fas fa-lock text-brand-accent mr-2"></i>Password
                                        </label>
                                        <div class="relative">
                                            <input type="password" name="password" id="register-password" required
                                                class="w-full px-4 py-3 border border-slate-200 rounded-xl input-brand focus:outline-none text-slate-700 pr-12"
                                                placeholder="Create a secure password">
                                            <button type="button"
                                                onclick="togglePassword('register-password', 'register-eye')"
                                                class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-brand-accent transition-colors">
                                                <i id="register-eye" class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mb-6">
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                                            <i class="fas fa-lock text-brand-accent mr-2"></i>Confirm Password
                                        </label>
                                        <div class="relative">
                                            <input type="password" name="password_confirmation"
                                                id="register-password-confirm" required
                                                class="w-full px-4 py-3 border border-slate-200 rounded-xl input-brand focus:outline-none text-slate-700 pr-12"
                                                placeholder="Confirm your password">
                                            <button type="button"
                                                onclick="togglePassword('register-password-confirm', 'register-confirm-eye')"
                                                class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-brand-accent transition-colors">
                                                <i id="register-confirm-eye" class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mb-6">
                                        <label class="custom-checkbox">
                                            <input type="checkbox" name="terms" id="terms" required>
                                            <span class="checkmark">
                                                <i class="fas fa-check"></i>
                                            </span>
                                            <span class="text-sm text-slate-600 leading-relaxed">
                                                I agree to the <a href="#"
                                                    class="text-brand-accent hover:underline font-semibold terms-link"
                                                    data-modal="terms">Terms of Service</a> and <a href="#"
                                                    class="text-brand-accent hover:underline font-semibold terms-link"
                                                    data-modal="privacy">Privacy Policy</a>.
                                            </span>
                                        </label>
                                    </div>

                                    @if($election->require_geo_registration)
                                        <div id="geo-status"
                                            class="mb-6 p-4 rounded-xl text-sm border flex items-center gap-3 bg-blue-50 border-blue-100 text-blue-700">
                                            <i class="fas fa-location-dot animate-pulse"></i>
                                            <span>Initializing location verification...</span>
                                        </div>
                                        <input type="hidden" name="latitude" id="reg-lat">
                                        <input type="hidden" name="longitude" id="reg-lng">
                                    @endif

                                    <button type="submit" id="register-submit-btn"
                                        class="w-full btn-brand text-white font-bold py-4 px-6 rounded-xl flex items-center justify-center gap-3 text-lg {{ $election->require_geo_registration ? 'opacity-50 pointer-events-none' : '' }}">
                                        <i class="fas fa-user-plus"></i>
                                        Register & Continue
                                    </button>
                                </form>

                                <!-- Sign In Form (Hidden by default) -->
                                <form id="signin-form"
                                    action="{{ route('voter.registration.login', $election->code ?? '') }}" method="POST"
                                    class="form-transition {{ $registrationOver ? '' : 'hidden' }}">
                                    @csrf
                                    <input type="hidden" name="latitude" id="login-lat">
                                    <input type="hidden" name="longitude" id="login-lng">

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
                                        <div class="relative">
                                            <input type="password" name="password" id="signin-password" required
                                                class="w-full px-4 py-3 border border-slate-200 rounded-xl input-brand focus:outline-none text-slate-700 pr-12"
                                                placeholder="Enter your password">
                                            <button type="button" onclick="togglePassword('signin-password', 'signin-eye')"
                                                class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-brand-accent transition-colors">
                                                <i id="signin-eye" class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mb-6 flex items-center justify-between">
                                        <label class="custom-checkbox">
                                            <input type="checkbox" name="remember">
                                            <span class="checkmark">
                                                <i class="fas fa-check"></i>
                                            </span>
                                            <span class="text-sm text-slate-600">Remember me</span>
                                        </label>
                                        <a href="#" class="text-sm text-brand-accent hover:underline font-semibold">Forgot
                                            password?</a>
                                    </div>

                                    <button type="submit"
                                        class="w-full btn-brand text-white font-bold py-4 px-6 rounded-xl flex items-center justify-center gap-3 text-lg {{ $election->require_geo_registration ? 'opacity-50 pointer-events-none' : '' }}">
                                        <i class="fas fa-sign-in-alt"></i>
                                        Sign In & Continue
                                    </button>
                                </form>

                                <!-- Divider -->
                                <div class="relative my-8 {{ $registrationOver ? 'hidden' : '' }}">
                                    <div class="absolute inset-0 flex items-center">
                                        <div class="w-full border-t border-slate-200"></div>
                                    </div>
                                    <div class="relative flex justify-center text-sm">
                                        <span id="toggle-text" class="px-4 bg-white text-slate-500">Already
                                            registered?</span>
                                    </div>
                                </div>

                                <!-- Toggle Button -->
                                <button id="toggle-form-btn" type="button"
                                    class="w-full flex items-center justify-center gap-3 py-4 px-6 bg-slate-100 rounded-2xl text-slate-700 hover:bg-slate-200 transition-all font-semibold hover:shadow-lg {{ $registrationOver ? 'hidden' : '' }}">
                                    <i id="toggle-icon" class="fas fa-sign-in-alt"></i>
                                    <span id="toggle-btn-text">Sign In Instead</span>
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
                            <span
                                class="inline-flex items-center gap-2 px-4 py-2 bg-brand-accent/10 text-brand-accent text-sm font-semibold rounded-full mb-4">
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
                            <div
                                class="flex items-start gap-4 p-5 bg-white rounded-2xl shadow-lg shadow-slate-100 border border-slate-100 hover:shadow-xl transition-shadow">
                                <div
                                    class="w-14 h-14 feature-icon rounded-2xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-bolt text-brand-accent text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-brand-primary font-bold text-lg mb-1">Quick Setup</h3>
                                    <p class="text-slate-500 text-sm">Register in under 2 minutes with just your basic
                                        information.</p>
                                </div>
                            </div>

                            <div
                                class="flex items-start gap-4 p-5 bg-white rounded-2xl shadow-lg shadow-slate-100 border border-slate-100 hover:shadow-xl transition-shadow">
                                <div
                                    class="w-14 h-14 feature-icon rounded-2xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-shield-alt text-brand-accent text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-brand-primary font-bold text-lg mb-1">Secure & Private</h3>
                                    <p class="text-slate-500 text-sm">Your data is protected with enterprise-grade
                                        encryption.</p>
                                </div>
                            </div>

                            <div
                                class="flex items-start gap-4 p-5 bg-white rounded-2xl shadow-lg shadow-slate-100 border border-slate-100 hover:shadow-xl transition-shadow">
                                <div
                                    class="w-14 h-14 feature-icon rounded-2xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-envelope-open-text text-brand-accent text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-brand-primary font-bold text-lg mb-1">Instant Confirmation</h3>
                                    <p class="text-slate-500 text-sm">Receive email confirmation with your unique voter
                                        credentials.</p>
                                </div>
                            </div>

                            <div
                                class="flex items-start gap-4 p-5 bg-white rounded-2xl shadow-lg shadow-slate-100 border border-slate-100 hover:shadow-xl transition-shadow">
                                <div
                                    class="w-14 h-14 feature-icon rounded-2xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-fingerprint text-brand-accent text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-brand-primary font-bold text-lg mb-1">Anonymous Voting</h3>
                                    <p class="text-slate-500 text-sm">Your vote is completely anonymous—no one can trace it
                                        back to you.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Election Details Card -->
                        @isset($election)
                            <div class="mt-8 p-6 bg-white rounded-2xl shadow-lg border border-slate-100">
                                <h3 class="font-bold text-brand-primary mb-4 flex items-center gap-2">
                                    <i class="fas fa-info-circle text-brand-accent"></i>
                                    Election Details
                                </h3>
                                @if($election->description)
                                    <p class="text-slate-600 text-sm mb-4">{{ $election->description }}</p>
                                @endif
                                <div class="space-y-3 text-sm">
                                    <div class="flex items-center justify-between py-2 border-b border-slate-100">
                                        <span class="text-slate-500">Start Date</span>
                                        <span
                                            class="font-semibold text-brand-primary">{{ $election->start_date->format('M d, Y h:i A') }}</span>
                                    </div>
                                    <div class="flex items-center justify-between py-2 border-b border-slate-100">
                                        <span class="text-slate-500">End Date</span>
                                        <span
                                            class="font-semibold text-brand-primary">{{ $election->end_date->format('M d, Y h:i A') }}</span>
                                    </div>
                                    <div class="flex items-center justify-between py-2">
                                        <span class="text-slate-500">Positions</span>
                                        <span
                                            class="font-semibold text-brand-primary">{{ $election->positions()->count() }}</span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div
                                class="mt-8 p-6 bg-gradient-to-br from-brand-primary/5 to-brand-accent/5 rounded-2xl border border-brand-accent/20">
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="flex -space-x-2">
                                        <div
                                            class="w-8 h-8 rounded-full bg-brand-primary flex items-center justify-center text-white text-xs font-bold">
                                            JD</div>
                                        <div
                                            class="w-8 h-8 rounded-full bg-brand-accent flex items-center justify-center text-white text-xs font-bold">
                                            MR</div>
                                        <div
                                            class="w-8 h-8 rounded-full bg-slate-400 flex items-center justify-center text-white text-xs font-bold">
                                            +5K</div>
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
                        @endisset
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('toggle-form-btn');
            const registerForm = document.getElementById('register-form');
            const signinForm = document.getElementById('signin-form');
            const toggleText = document.getElementById('toggle-text');
            const toggleBtnText = document.getElementById('toggle-btn-text');
            const toggleIcon = document.getElementById('toggle-icon');
            const headerIcon = document.getElementById('form-header-icon');
            const headerTitle = document.getElementById('form-header-title');
            const headerSubtitle = document.getElementById('form-header-subtitle');

            let isSignInMode = {{ $registrationOver ? 'true' : 'false' }};

            if (isSignInMode) {
                headerIcon.className = 'fas fa-sign-in-alt text-white text-2xl';
                headerTitle.textContent = 'Welcome Back';
                headerSubtitle.textContent = 'Sign in to access your election';
            }

            toggleBtn.addEventListener('click', function () {
                isSignInMode = !isSignInMode;

                if (isSignInMode) {
                    registerForm.classList.add('hidden');
                    signinForm.classList.remove('hidden');
                    toggleText.textContent = "Don't have an account?";
                    toggleBtnText.textContent = 'Create Account';
                    toggleIcon.className = 'fas fa-user-plus';
                    headerIcon.className = 'fas fa-sign-in-alt text-white text-2xl';
                    headerTitle.textContent = 'Welcome Back';
                    headerSubtitle.textContent = 'Sign in to access your election';
                } else {
                    signinForm.classList.add('hidden');
                    registerForm.classList.remove('hidden');
                    toggleText.textContent = 'Already registered?';
                    toggleBtnText.textContent = 'Sign In Instead';
                    toggleIcon.className = 'fas fa-sign-in-alt';
                    headerIcon.className = 'fas fa-user-plus text-white text-2xl';
                    headerTitle.textContent = 'Voter Registration';
                    headerSubtitle.textContent = 'Create your secure voting account';
                }

                document.getElementById('login-error').classList.add('hidden');
            });

            // Phone number formatting
            const phoneInput = document.querySelector('input[name="phone"]');
            if (phoneInput) {
                phoneInput.addEventListener('input', function (e) {
                    let value = this.value.replace(/\D/g, '');
                    if (value.length > 11) value = value.slice(0, 11);
                    this.value = value;
                });
            }

            // Student ID uppercase
            const studentIdInput = document.querySelector('input[name="student_id"]');
            if (studentIdInput) {
                studentIdInput.addEventListener('input', function (e) {
                    this.value = this.value.toUpperCase();
                });
            }

            // Password validation
            const passwordInput = document.getElementById('register-password');
            const confirmInput = document.getElementById('register-password-confirm');

            if (confirmInput) {
                confirmInput.addEventListener('input', function () {
                    if (passwordInput.value !== this.value) {
                        this.setCustomValidity('Passwords do not match');
                    } else {
                        this.setCustomValidity('');
                    }
                });
            }
            @if($election->require_geo_registration)
                const geoStatus = document.getElementById('geo-status');
                const submitBtn = document.getElementById('register-submit-btn');
                const loginSubmitBtn = document.getElementById('signin-form') ? document.getElementById('signin-form').querySelector('button[type="submit"]') : null;
                const latInput = document.getElementById('reg-lat');
                const lngInput = document.getElementById('reg-lng');
                const loginLatInput = document.getElementById('login-lat');
                const loginLngInput = document.getElementById('login-lng');

                function getGeoLocation() {
                    if (navigator.geolocation) {
                        geoStatus.className = 'mb-6 p-4 rounded-xl text-sm border flex items-center gap-3 bg-blue-50 border-blue-100 text-blue-700';
                        geoStatus.innerHTML = '<i class="fas fa-location-dot animate-pulse"></i><span>Requesting location access...</span>';

                        navigator.geolocation.getCurrentPosition(
                            (position) => {
                                latInput.value = position.coords.latitude;
                                lngInput.value = position.coords.longitude;
                                if (loginLatInput) loginLatInput.value = position.coords.latitude;
                                if (loginLngInput) loginLngInput.value = position.coords.longitude;

                                geoStatus.className = 'mb-6 p-4 rounded-xl text-sm border flex items-center gap-3 bg-emerald-50 border-emerald-100 text-emerald-700';
                                geoStatus.innerHTML = '<i class="fas fa-check-circle"></i><span>Location verified successfully</span>';
                                submitBtn.classList.remove('opacity-50', 'pointer-events-none');
                                if (loginSubmitBtn) loginSubmitBtn.classList.remove('opacity-50', 'pointer-events-none');
                            },
                            (error) => {
                                let message = 'Error getting location';
                                switch (error.code) {
                                    case error.PERMISSION_DENIED:
                                        message = 'Location access denied. Please enable GPS and allow location access to continue.';
                                        break;
                                    case error.POSITION_UNAVAILABLE:
                                        message = 'Location information is unavailable.';
                                        break;
                                    case error.TIMEOUT:
                                        message = 'The request to get user location timed out.';
                                        break;
                                }
                                geoStatus.className = 'mb-6 p-4 rounded-xl text-sm border flex items-center gap-3 bg-red-50 border-red-100 text-red-700';
                                geoStatus.innerHTML = '<i class="fas fa-exclamation-triangle"></i><span>' + message + '</span>';
                            },
                            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                        );
                    } else {
                        geoStatus.className = 'mb-6 p-4 rounded-xl text-sm border flex items-center gap-3 bg-red-50 border-red-100 text-red-700';
                        geoStatus.innerHTML = '<i class="fas fa-exclamation-triangle"></i><span>Geolocation is not supported by this browser.</span>';
                    }
                }

                getGeoLocation();
            @endif
                            });

        // Toggle password visibility
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }
    </script>
    <script>
        // Terms / Privacy modal logic
        document.addEventListener('DOMContentLoaded', function () {
            // create modal element
            const modalHtml = `
            <div id="tos-privacy-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
                                    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
                                    <div class="relative bg-white rounded-3xl shadow-2xl max-w-3xl w-full mx-4 overflow-hidden">
                                        <div class="gradient-brand p-6 text-white flex items-center justify-between">
                                            <div class="flex items-center gap-4">
                                                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-file-contract text-white text-lg"></i>
                                                </div>
                                                <div>
                                                    <h3 id="modal-title" class="text-lg font-bold">Title</h3>
                                                    <p id="modal-sub" class="text-sm opacity-80">Details</p>
                                                </div>
                                            </div>
                                            <button id="modal-close" class="text-white text-xl">&times;</button>
                                        </div>
                                        <div id="modal-body" class="p-6 text-slate-700 max-h-[60vh] overflow-y-auto">
                                            <!-- content injected -->
                                        </div>
                                    </div>
                                </div>`;

            document.body.insertAdjacentHTML('beforeend', modalHtml);

            const modal = document.getElementById('tos-privacy-modal');
            const modalTitle = document.getElementById('modal-title');
            const modalSub = document.getElementById('modal-sub');
            const modalBody = document.getElementById('modal-body');
            const modalClose = document.getElementById('modal-close');

            const termsContent = `
                                    <h4 class="font-bold mb-3">Terms of Service</h4>
                                    <p class="text-sm text-slate-600 mb-2">1. Purpose of the System</p>

                                    <p class="text-sm text-slate-600">This Voting System is created to provide a secure and reliable way to conduct elections. It ensures fair voting,</p>
                                    <p class="text-sm text-slate-600">protects user data, and keeps all votes confidential.</p>
                                    <p class="text-sm text-slate-600">By using this system, users agree to follow these terms and allow their data to be processed as described below.</p>

                                    <h4 class="font-bold mb-3">Terms of Use</h4>

                                    <h4 class="font-bold mb-3">2. Authorized Use</h4>
                                    <p class="text-sm text-slate-600">Only registered and authorized users may access the system. Each user is responsible for keeping their</p>
                                    <p class="text-sm text-slate-600">account credentials private and secure.</p>

                                    <h4 class="font-bold mb-3">3. Fair Use</h4>
                                    <p class="text-sm text-slate-600">Users must use the system only for its intended purpose. Any attempt to manipulate votes, access other</p>
                                    <p class="text-sm text-slate-600">users’ data, or disrupt the system is strictly prohibited.</p>

                                    <h4 class="font-bold mb-3">4. One Person, One Vote</h4>
                                    <p class="text-sm text-slate-600">Each eligible voter is allowed to vote only once per election. The system includes controls to prevent</p>
                                    <p class="text-sm text-slate-600">duplicate or unauthorized voting.</p>

                                    <h4 class="font-bold mb-3">Privacy Policy (RA 10173 Compliant)</h4>

                                    <h5 class="font-bold mb-3">5. Data Collection</h5>
                                    <p class="text-sm text-slate-600">The system collects only the necessary personal information, such as:</p>
                                    <p class="text-sm text-slate-600">Name, username, and email</p>
                                    <p class="text-sm text-slate-600">Login credentials (securely encrypted)</p>
                                    <p class="text-sm text-slate-600">Voting participation status</p>
                                    <p class="text-sm text-slate-600">No unnecessary personal data is collected.</p>

                                    <h5 class="font-bold mb-3">6. Data Usage</h5>
                                    <p class="text-sm text-slate-600">Collected data is used solely for:</p>
                                    <p class="text-sm text-slate-600">Verifying voter eligibility   </p>
                                    <p class="text-sm text-slate-600">Ensuring one person, one vote</p>
                                    <p class="text-sm text-slate-600">Maintaining system security</p>
                                    <p class="text-sm text-slate-600">Improving user experience</p>
                                    <p class="text-sm text-slate-600">Data will not be used for marketing or shared with third parties without explicit consent.</p>

                                    <h5 class="font-bold mb-3">7. Data Protection</h5>
                                    <p class="text-sm text-slate-600">The system employs robust security measures to protect user data, including:</p>
                                    <p class="text-sm text-slate-600">Encryption of sensitive information</p>
                                    <p class="text-sm text-slate-600">Regular security audits</p>
                                    <p class="text-sm text-slate-600">Access controls to prevent unauthorized data access</p>
                                    <p class="text-sm text-slate-600">Users are encouraged to use strong passwords and report any suspicious activity.</p>

                                    <h5 class="font-bold mb-3">8. User Rights</h5>
                                    <p class="text-sm text-slate-600">Users have the right to:</p>
                                    <p class="text-sm text-slate-600">Access their personal data</p>
                                    <p class="text-sm text-slate-600">Request corrections to inaccurate data</p>
                                    <p class="text-sm text-slate-600">Request deletion of their data, subject to legal obligations</p>
                                    <p class="text-sm text-slate-600">Withdraw consent for data processing at any time</p>
                                    <p class="text-sm text-slate-600">To exercise these rights, users can contact the system administrator.</p>

                                    <h5 class="font-bold mb-3">9. Changes to Terms and Privacy Policy</h5>
                                    <p class="text-sm text-slate-600">The system reserves the right to update these Terms of Service and Privacy Policy as needed. Users will be notified of any significant changes.</p>
                                    <p class="text-sm text-slate-600">Continued use of the system after changes constitutes acceptance of the new terms.</p>

                                    <h5 class="font-bold mb-3">10. Contact Information</h5>
                                    <p class="text-sm text-slate-600">For questions or concerns regarding these Terms of Service or Privacy Policy, users can contact:</p>
                                    <p class="text-sm text-slate-600">Access their personal data</p>
                                    <p class="text-sm text-slate-600">Request corrections to inaccurate information</p>
                                    <p class="text-sm text-slate-600">Request deletion of their data, subject to legal obligations</p>
                                    <p class="text-sm text-slate-600">Withdraw consent for data processing at any time</p>
                                    <p class="text-sm text-slate-600">To exercise these rights, users can contact the system administrator.</p>

                                    11. Acceptance of Terms
                                    <p class="text-sm text-slate-600">By using this Voting System, users acknowledge that they have read, understood, and agree to be bound by these Terms of Service and Privacy Policy.</p>

                                    12. Effective Date
                                    <p class="text-sm text-slate-600">These Terms of Service and Privacy Policy are effective as of January 1, 2024.</p>

                                `;

            const privacyContent = `
                                    <h4 class="font-bold mb-3">Privacy Policy</h4>
                                    <p class="text-sm text-slate-600">Your privacy is important to us. This Privacy Policy explains how we collect, use, and protect your personal information when you use our Voting System.</p>

                                    <h4 class="font-bold mb-3">1. Information We Collect</h4>
                                    <p class="text-sm text-slate-600">We collect the following types of information:</p>
                                    <p class="text-sm text-slate-600">Personal Information: Name, email address, and other contact details</p>
                                    <p class="text-sm text-slate-600">Login Credentials: Username and password (securely encrypted)</p>
                                    <p class="text-sm text-slate-600">Voting Data: Information about your voting participation</p>
                                    <p class="text-sm text-slate-600">Usage Data: Information about how you use the system, including IP address and browser type</p>

                                    <h4 class="font-bold mb-3">2. How We Use Your Information</h4>
                                    <p class="text-sm text-slate-600">We use your information for the following purposes:</p>
                                    <p class="text-sm text-slate-600">To verify voter eligibility</p>
                                    <p class="text-sm text-slate-600">To ensure one person, one vote</p>
                                    <p class="text-sm text-slate-600">To maintain system security and prevent fraud</p>
                                    <p class="text-sm text-slate-600">To improve user experience and system functionality</p>
                                    <p class="text-sm text-slate-600">To communicate important updates and information</p>

                                    <h4 class="font-bold mb-3">3. Data Protection</h4>
                                    <p class="text-sm text-slate-600">We implement robust security measures to protect your personal information, including:</p>
                                    <p class="text-sm text-slate-600">Encryption of sensitive data</p>
                                    <p class="text-sm text-slate-600">Regular security audits</p>
                                    <p class="text-sm text-slate-600">Access controls to prevent unauthorized access</p>
                                    <p class="text-sm text-slate-600">We encourage you to use strong passwords and report any suspicious activity.</p>

                                    <h4 class="font-bold mb-3">4. Your Rights</h4>
                                    <p class="text-sm text-slate-600">You have the right to:<//p>
                                    <p class="text-sm text-slate-600">Access your personal data</p>
                                    <p class="text-sm text-slate-600">Request corrections to inaccurate data</p>
                                    <p class="text-sm text-slate-600">Request deletion of your data, subject to legal obligations</p>
                                    <p class="text-sm text-slate-600">Withdraw consent for data processing at any time</p>
                                    <p class="text-sm text-slate-600">To exercise these rights, please contact the system administrator.</p>

                                    <h4 class="font-bold mb-3">5. Changes to This Privacy Policy</h4>
                                    <p class="text-sm text-slate-600">We may update this Privacy Policy from time to time. We will notify you of any significant changes.</p>
                                    <p class="text-sm text-slate-600">Your continued use of the system after changes constitutes acceptance of the new policy.</p>

                                    <h4 class="font-bold mb-3">6. Contact Us</h4>
                                    <p class="text-sm text-slate-600">If you have any questions or concerns about this Privacy Policy, please contact the system administrator.</p>

                                `;

            function openModal(type) {
                if (type === 'terms') {
                    modalTitle.textContent = 'Terms of Service';
                    modalSub.textContent = 'Please read our terms carefully';
                    modalBody.innerHTML = termsContent;
                } else {
                    modalTitle.textContent = 'Privacy Policy';
                    modalSub.textContent = 'How we handle your data';
                    modalBody.innerHTML = privacyContent;
                }
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeModal() {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }

            document.querySelectorAll('.terms-link').forEach(el => {
                el.addEventListener('click', function (e) {
                    e.preventDefault();
                    const t = this.getAttribute('data-modal');
                    openModal(t);
                });
            });

            modalClose.addEventListener('click', closeModal);
            modal.addEventListener('click', function (e) {
                if (e.target === modal) closeModal();
            });
        });
    </script>
@endpush