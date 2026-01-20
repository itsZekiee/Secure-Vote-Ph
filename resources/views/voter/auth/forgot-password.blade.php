@extends('voter.layouts.app')

@section('title', 'Forgot Password - ' . ($election->title ?? 'SecureVote PH'))

@push('styles')
    <style>
        .gradient-brand {
            background: linear-gradient(135deg, #003153 0%, #00D4AA 100%);
        }

        .btn-brand {
            background: linear-gradient(135deg, #003153 0%, #00D4AA 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-brand:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px -10px rgba(0, 212, 170, 0.5);
        }
    </style>
@endpush

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-white to-slate-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="text-center">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 rounded-2xl gradient-brand flex items-center justify-center shadow-lg">
                        <i class="fas fa-vote-yea text-white text-xl"></i>
                    </div>
                </a>
                <h2 class="text-3xl font-extrabold text-slate-900">Forgot your password?</h2>
                <p class="mt-2 text-sm text-slate-600">
                    No problem. Just let us know your email address and we will email you a password reset link.
                </p>
            </div>

            <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
                <div x-data="passwordRecovery()"
                    class="bg-white py-8 px-4 shadow-xl shadow-slate-200/50 sm:rounded-3xl sm:px-10 border border-slate-100">


                    <!-- STEP PROGRESS PANEL -->
                    <div class="mb-8" x-cloak>
                        <div class="flex items-center justify-start gap-3">

                            <!-- STEP 1 -->
                            <div class="flex items-center gap-2">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold transition"
                                    :class="step >= 1 ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-500'">
                                    1
                                </div>
                                <span class="text-sm font-medium text-emerald-600">
                                    Email
                                </span>
                            </div>

                            <!-- LINE -->
                            <template x-if="step >= 2">
                                <div class="w-10 h-1 bg-emerald-500"></div>
                            </template>

                            <!-- STEP 2 (HIDDEN UNTIL STEP 2) -->
                            <template x-if="step >= 2">
                                <div class="flex items-center gap-2">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold transition"
                                        :class="step >= 2 ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-500'">
                                        2
                                    </div>
                                    <span class="text-sm font-medium text-emerald-600">
                                        OTP
                                    </span>
                                </div>
                            </template>

                            <!-- LINE -->
                            <template x-if="step >= 3">
                                <div class="w-10 h-1 bg-emerald-500"></div>
                            </template>

                            <!-- STEP 3 (HIDDEN UNTIL STEP 3) -->
                            <template x-if="step >= 3">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-10 h-10 rounded-full flex items-center justify-center font-bold transition bg-emerald-500 text-white">
                                        3
                                    </div>
                                    <span class="text-sm font-medium text-emerald-600">
                                        New Password
                                    </span>
                                </div>
                            </template>

                        </div>
                    </div>
                    <!-- END STEP PANEL -->


                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-600 text-sm">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-600 text-sm">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif


                    <!-- Step 1: Email Search -->
                    <div x-show="step === 1" x-transition.opacity.duration.300ms class="space-y-6">

                        <div>
                            <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">
                                Email Address
                            </label>
                            <div class="relative">
                                <input id="email" x-model="email" @input="searchEmails" type="email" required
                                    class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-accent/20 focus:border-brand-accent transition-all"
                                    placeholder="Start typing your email...">

                                <!-- Autocomplete Dropdown -->
                                <div x-show="suggestions.length > 0"
                                    class="absolute z-10 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden">
                                    <template x-for="suggestion in suggestions">
                                        <button @click="selectEmail(suggestion)"
                                            class="w-full text-left px-4 py-3 text-sm hover:bg-slate-50 transition-colors"
                                            x-text="suggestion"></button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <button @click="sendOTP" :disabled="!email || loading"
                                class="w-full btn-brand text-white font-bold py-3 px-6 rounded-xl shadow-lg disabled:opacity-50 flex items-center justify-center gap-2">
                                <template x-if="loading">
                                    <i class="fas fa-spinner animate-spin"></i>
                                </template>
                                Send Reset OTP
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: OTP Verification -->
                    <div x-show="step === 2" x-cloak x-transition.opacity.duration.300ms class="space-y-6">
                        <div class="text-center mb-6">
                            <p class="text-sm text-slate-600">We've sent a 6-digit code to <span
                                    class="font-bold text-slate-900" x-text="email"></span></p>
                        </div>
                        <div>
                            <label for="otp" class="block text-sm font-semibold text-slate-700 mb-2">
                                Enter 6-Digit OTP
                            </label>
                            <input id="otp" x-model="otp" type="text" maxlength="8"
                                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-accent/20 focus:border-brand-accent transition-all text-center text-2xl tracking-[0.5em] font-bold"
                                placeholder="00000000">
                        </div>

                        <div class="mt-6">
                            <button @click="verifyOTP" :disabled="otp.length !== 8 || loading"
                                class="w-full btn-brand text-white font-bold py-3 px-6 rounded-xl shadow-lg disabled:opacity-50 flex items-center justify-center gap-2">
                                <template x-if="loading">
                                    <i class="fas fa-spinner animate-spin"></i>
                                </template>
                                Verify OTP
                            </button>
                        </div>
                        <button @click="step = 1" class="w-full text-sm text-slate-500 hover:text-slate-700 font-medium">
                            Change Email
                        </button>
                    </div>

                    <!-- Step 3: Change Password -->
                    <div x-show="step === 3" x-cloak x-transition.opacity.duration.300ms class="space-y-6">
                        <form action="{{ route('voter.password.update', $election->code) }}" method="POST">
                            @csrf
                            <input type="hidden" name="email" :value="email">


                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">New Password</label>
                                    <input type="password" name="password" required
                                        class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-accent/20 focus:border-brand-accent transition-all">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Confirm
                                        Password</label>
                                    <input type="password" name="password_confirmation" required
                                        class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-accent/20 focus:border-brand-accent transition-all">
                                </div>
                            </div>

                            <div class="mt-6">
                                <button type="submit"
                                    class="w-full btn-brand text-white font-bold py-3 px-6 rounded-xl shadow-lg">
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>

                    <div x-show="message"
                        :class="messageType === 'success' ? 'bg-green-50 text-green-600 border-green-200' : 'bg-red-50 text-red-600 border-red-200'"
                        class="mt-4 p-4 border rounded-xl text-sm" x-text="message">
                    </div>
                </div>

                <div class="mt-6 text-center">
                    <a href="{{ route('voter.registration.index', $election->id) }}"
                        class="text-sm font-semibold text-brand-accent hover:underline">
                        Back to Login
                    </a>
                </div>
            </div>
        </div>
    </div>
    </div>
    @push('scripts')
        <script>
            function passwordRecovery() {
                return {
                    step: 1,
                    email: '',
                    otp: '',
                    token: '',
                    suggestions: [],
                    loading: false,
                    message: '',
                    messageType: 'error',

                    searchEmails() {
                        if (this.email.length < 2) {
                            this.suggestions = [];
                            return;
                        }

                        fetch(`{{ route('voter.password.search', $election->code) }}?q=${this.email}`)
                            .then(res => res.json())
                            .then(data => {
                                this.suggestions = data;
                            });
                    },

                    selectEmail(email) {
                        this.email = email;
                        this.suggestions = [];
                    },

                    sendOTP() {
                        this.loading = true;
                        this.message = '';

                        fetch(`{{ route('voter.password.otp.send', $election->code) }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ email: this.email })
                        })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    this.step = 2;
                                    this.message = data.message;
                                    this.messageType = 'success';
                                } else {
                                    this.message = data.message;
                                    this.messageType = 'error';
                                }
                            })
                            .catch(() => {
                                this.message = 'An error occurred. Please try again.';
                                this.messageType = 'error';
                            })
                            .finally(() => {
                                this.loading = false;
                            });
                    },

                    verifyOTP() {
                        this.loading = true;
                        this.message = '';

                        fetch(`{{ route('voter.password.otp.verify', $election->code) }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ email: this.email, otp: this.otp })
                        })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    this.step = 3;
                                    this.message = 'OTP Verified! Please enter your new password.';
                                    this.messageType = 'success';
                                } else {
                                    this.message = data.message;
                                    this.messageType = 'error';
                                }
                            })
                            .catch(() => {
                                this.message = 'An error occurred. Please try again.';
                                this.messageType = 'error';
                            })
                            .finally(() => {
                                this.loading = false;
                            });
                    }
                }
            }
        </script>
    @endpush
@endsection