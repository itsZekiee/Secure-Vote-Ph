<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<x-default-header title="{{ $title ?? config('app.name', 'SecureVote') }}" />

@stack('styles')

<body class="font-sans antialiased bg-slate-50 min-h-screen">
    <!-- Global Success/Error Messages (Fixed Position) -->
    @if(session('success'))
        <div
            class="fixed top-20 right-4 z-[9999] bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-xl shadow-lg transform transition-all duration-300">
            <div class="flex items-center space-x-3">
                <i class="ri-check-line text-lg"></i>
                <span class="font-medium">{{ session('success') }}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-gray-400 hover:text-gray-600">
                    <i class="ri-close-line"></i>
                </button>
            </div>
        </div>
    @endif

    @if(session('logged_out'))
        <div
            class="fixed top-20 right-4 z-[9999] bg-blue-50 border border-blue-200 text-blue-700 px-6 py-4 rounded-xl shadow-lg transform transition-all duration-300">
            <div class="flex items-center space-x-3">
                <i class="ri-information-line text-lg"></i>
                <span class="font-medium">{{ session('logged_out') }}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-gray-400 hover:text-gray-600">
                    <i class="ri-close-line"></i>
                </button>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div
            class="fixed top-20 right-4 z-[9999] bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-xl shadow-lg max-w-md">
            <div class="flex items-start space-x-3">
                <i class="ri-error-warning-line text-lg mt-0.5"></i>
                <div class="flex-1">
                    @foreach($errors->all() as $error)
                        <p class="font-medium">{{ $error }}</p>
                        @if(str_contains($error, 'permanently blocked') || str_contains($error, 'contact the Administrator'))
                            <div class="mt-3 p-3 bg-white rounded-lg border border-red-100 shadow-sm">
                                <p class="text-xs font-bold text-slate-800 mb-1 uppercase tracking-wider">How to restore access?</p>
                                <p class="text-xs text-slate-500">Please send an email to <span
                                        class="text-primary font-bold">admin@securevote.ph</span> or contact your organization's IT
                                    department.</p>
                            </div>
                        @endif
                    @endforeach
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-gray-400 hover:text-gray-600">
                    <i class="ri-close-line"></i>
                </button>
            </div>
        </div>
    @endif

    <div class="font-sans antialiased bg-gradient-to-br from-gray-50 to-white min-h-screen">
        <!-- Navigation Component -->
        <x-default-nav />

        <!-- Main Content -->
        <main class="relative">
            <div class="max-w-7xl mx-auto">
                <!-- Welcome Section -->
                <section
                    class="welcome-section px-6 lg:px-8 py-20 lg:py-32 bg-gradient-to-br from-gray-50 to-white min-h-screen flex items-center">
                    <div class="w-full">
                        <div class="grid lg:grid-cols-2 gap-16 items-center">
                            <!-- Left Content -->
                            <div class="space-y-8">
                                <h1 class="text-5xl lg:text-6xl xl:text-7xl font-bold text-secondary leading-tight">
                                    Secure,
                                    <span class="bg-gradient-to-r from-primary to-accent bg-clip-text text-transparent">
                                        Digital
                                    </span>
                                    Voting
                                </h1>
                                <p class="text-xl text-gray-600 leading-relaxed max-w-2xl">
                                    Experience the future of democratic participation with cutting-edge security,
                                    real-time analytics, and geo-location verification.
                                </p>
                                <div class="flex flex-col sm:flex-row gap-4">
                                    <a href="{{ route('voter.elections.join') }}"
                                        class="group bg-gradient-to-r from-primary to-primary/90 hover:from-primary/90 hover:to-primary text-white px-8 py-4 rounded-2xl font-semibold transition-all duration-300 transform hover:scale-105 hover:shadow-2xl inline-flex items-center">
                                        Start Voting Now
                                        <i
                                            class="ri-arrow-right-line ml-2 group-hover:translate-x-1 transition-transform"></i>
                                    </a>
                                </div>
                            </div>
                            <!-- Right Visual -->
                            <div class="relative">
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-primary/20 to-accent/20 rounded-3xl blur-3xl">
                                </div>
                                <div class="relative bg-white p-8 rounded-3xl shadow-2xl">
                                    <img src="{{ asset('assets/Voting-amico.svg') }}" alt="Secure Voting"
                                        class="w-full h-auto object-contain">
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Geographic Section -->
                <section id="geo" class="px-6 lg:px-8 py-20 bg-gradient-to-br from-gray-50 to-white min-h-screen">
                    <div class="w-full">
                        <div class="text-center mb-16 max-w-3xl mx-auto">
                            <div
                                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-primary/10 to-accent/10 rounded-full mb-6">
                                <span class="text-primary text-sm font-medium">🌍 Geo Location Features</span>
                            </div>
                            <h2 class="text-4xl lg:text-5xl font-bold text-secondary mb-6">
                                Powerful Features for Modern Voting
                            </h2>
                            <p class="text-xl text-gray-600 leading-relaxed">
                                Built with the latest technology to ensure secure, transparent, and efficient elections
                            </p>
                        </div>
                        <div class="grid lg:grid-cols-2 gap-16 items-center">
                            <div class="space-y-8">
                                <div class="bg-white p-8 rounded-2xl shadow-lg border border-gray-100">
                                    <div class="flex items-center mb-6">
                                        <i class="ri-map-pin-line text-primary text-2xl mr-3"></i>
                                        <h3 class="text-2xl font-bold text-secondary">Geo Location Verification</h3>
                                    </div>
                                    <p class="text-gray-600 text-lg leading-relaxed">
                                        Define precise geographic boundaries for your elections. Control where voters
                                        can participate with customizable radius settings.
                                    </p>
                                </div>
                                <div class="space-y-6">
                                    <div
                                        class="group bg-gradient-to-r from-indigo-50 to-indigo-100/50 p-8 rounded-2xl border-l-4 border-indigo-500 hover:shadow-lg transition-all duration-300">
                                        <div class="flex items-center mb-4">
                                            <i class="ri-settings-3-line text-indigo-600 text-2xl mr-3"></i>
                                            <h4
                                                class="text-xl font-bold bg-gradient-to-r from-secondary to-primary bg-clip-text text-transparent">
                                                Custom Voting Boundaries</h4>
                                        </div>
                                        <p class="text-gray-700">Set custom voting boundaries with flexible radius
                                            controls</p>
                                    </div>
                                    <div
                                        class="group bg-gradient-to-r from-orange-50 to-orange-100/50 p-8 rounded-2xl border-l-4 border-orange-500 hover:shadow-lg transition-all duration-300">
                                        <div class="flex items-center mb-4">
                                            <i class="ri-radar-line text-orange-600 text-2xl mr-3"></i>
                                            <h4
                                                class="text-xl font-bold bg-gradient-to-r from-secondary to-primary bg-clip-text text-transparent">
                                                Real-time Location Monitoring</h4>
                                        </div>
                                        <p class="text-gray-700">Monitor voter locations in real-time for security
                                            purposes</p>
                                    </div>
                                    <div
                                        class="group bg-gradient-to-r from-teal-50 to-teal-100/50 p-8 rounded-2xl border-l-4 border-teal-500 hover:shadow-lg transition-all duration-300">
                                        <div class="flex items-center mb-4">
                                            <i class="ri-building-2-line text-teal-600 text-2xl mr-3"></i>
                                            <h4
                                                class="text-xl font-bold bg-gradient-to-r from-secondary to-primary bg-clip-text text-transparent">
                                                Multiple Voting Zones</h4>
                                        </div>
                                        <p class="text-gray-700">Configure multiple voting zones with different
                                            parameters</p>
                                    </div>
                                </div>
                            </div>
                            <div class="relative">
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-primary/10 to-accent/10 rounded-3xl blur-2xl">
                                </div>
                                <div class="relative">
                                    <img src="{{ asset('assets/33633910_map.jpg') }}" alt="Geo Location Verification"
                                        class="w-full h-auto object-contain rounded-lg shadow-lg">
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Security Section -->
                <section id="security" class="px-6 lg:px-8 py-20 bg-gradient-to-br from-gray-50 to-white min-h-screen">
                    <div class="w-full max-w-6xl mx-auto">
                        <div class="text-center mb-16">
                            <div
                                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500/10 to-blue-600/10 rounded-full mb-6">
                                <span class="text-blue-600 text-sm font-medium">🔒 Security First</span>
                            </div>
                            <h2
                                class="text-4xl lg:text-5xl font-bold bg-gradient-to-r from-secondary to-primary bg-clip-text text-transparent mb-6">
                                Enterprise-Grade Security
                            </h2>
                        </div>
                        <div class="grid lg:grid-cols-2 gap-12">
                            <div class="relative">
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-blue-500/10 to-purple-500/10 rounded-3xl blur-2xl">
                                </div>
                                <div class="relative">
                                    <img src="{{ asset('assets/concept-illustration-data-security-technology.png') }}"
                                        alt="Security Features"
                                        class="w-full h-auto object-contain rounded-lg shadow-lg">
                                </div>
                            </div>
                            <div class="space-y-6">
                                <div
                                    class="group bg-gradient-to-r from-blue-50 to-blue-100/50 p-8 rounded-2xl border-l-4 border-blue-500 hover:shadow-lg transition-all duration-300">
                                    <div class="flex items-center mb-4">
                                        <i class="ri-shield-check-line text-blue-600 text-2xl mr-3"></i>
                                        <h3
                                            class="text-xl font-bold bg-gradient-to-r from-secondary to-primary bg-clip-text text-transparent">
                                            End-to-End Encryption</h3>
                                    </div>
                                    <p class="text-gray-700">Military-grade AES-256 encryption protects every vote from
                                        device to server.</p>
                                </div>
                                <div
                                    class="group bg-gradient-to-r from-green-50 to-green-100/50 p-8 rounded-2xl border-l-4 border-green-500 hover:shadow-lg transition-all duration-300">
                                    <div class="flex items-center mb-4">
                                        <i class="ri-fingerprint-line text-green-600 text-2xl mr-3"></i>
                                        <h3
                                            class="text-xl font-bold bg-gradient-to-r from-secondary to-primary bg-clip-text text-transparent">
                                            Biometric Authentication</h3>
                                    </div>
                                    <p class="text-gray-700">Advanced biometric authentication using fingerprint and
                                        facial recognition.</p>
                                </div>
                                <div
                                    class="group bg-gradient-to-r from-purple-50 to-purple-100/50 p-8 rounded-2xl border-l-4 border-purple-500 hover:shadow-lg transition-all duration-300">
                                    <div class="flex items-center mb-4">
                                        <i class="ri-links-line text-purple-600 text-2xl mr-3"></i>
                                        <h3
                                            class="text-xl font-bold bg-gradient-to-r from-secondary to-primary bg-clip-text text-transparent">
                                            Blockchain Technology</h3>
                                    </div>
                                    <p class="text-gray-700">Immutable vote recording using distributed ledger for
                                        complete transparency.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Analytics Section -->
                <section id="analytics" class="px-6 lg:px-8 py-20 bg-gradient-to-br from-gray-50 to-white min-h-screen">
                    <div class="w-full max-w-6xl mx-auto">
                        <div class="text-center mb-16">
                            <div
                                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-500/10 to-indigo-500/10 rounded-full mb-6">
                                <span class="text-purple-600 text-sm font-medium">📊 Real-time Analytics</span>
                            </div>
                            <h2 class="text-4xl lg:text-5xl font-bold text-secondary mb-6">
                                Comprehensive Data Insights
                            </h2>
                            <p class="text-xl text-gray-600 leading-relaxed">
                                Make informed decisions with powerful analytics and visualization tools
                            </p>
                        </div>
                        <div class="grid lg:grid-cols-2 gap-16 items-center">
                            <div class="space-y-8">
                                <div class="bg-white p-8 rounded-2xl shadow-lg border border-gray-100">
                                    <div class="flex items-center mb-6">
                                        <i class="ri-line-chart-line text-purple-600 text-2xl mr-3"></i>
                                        <h3 class="text-2xl font-bold text-secondary">Real-time Data Visualization</h3>
                                    </div>
                                    <p class="text-gray-600 text-lg leading-relaxed">
                                        Monitor voting patterns, participation rates, and results in real-time with
                                        interactive dashboards and comprehensive reporting tools.
                                    </p>
                                </div>
                                <div class="space-y-6">
                                    <div
                                        class="group bg-gradient-to-r from-emerald-50 to-emerald-100/50 p-8 rounded-2xl border-l-4 border-emerald-500 hover:shadow-lg transition-all duration-300">
                                        <div class="flex items-center mb-4">
                                            <i class="ri-time-line text-emerald-600 text-2xl mr-3"></i>
                                            <h4
                                                class="text-xl font-bold bg-gradient-to-r from-secondary to-primary bg-clip-text text-transparent">
                                                Real-time Progress Tracking</h4>
                                        </div>
                                        <p class="text-gray-700">Track voting progress and system status in real-time
                                        </p>
                                    </div>
                                    <div
                                        class="group bg-gradient-to-r from-rose-50 to-rose-100/50 p-8 rounded-2xl border-l-4 border-rose-500 hover:shadow-lg transition-all duration-300">
                                        <div class="flex items-center mb-4">
                                            <i class="ri-bar-chart-grouped-line text-rose-600 text-2xl mr-3"></i>
                                            <h4
                                                class="text-xl font-bold bg-gradient-to-r from-secondary to-primary bg-clip-text text-transparent">
                                                Interactive Data Visualization</h4>
                                        </div>
                                        <p class="text-gray-700">Explore data through charts, graphs, and interactive
                                            maps</p>
                                    </div>
                                    <div
                                        class="group bg-gradient-to-r from-cyan-50 to-cyan-100/50 p-8 rounded-2xl border-l-4 border-cyan-500 hover:shadow-lg transition-all duration-300">
                                        <div class="flex items-center mb-4">
                                            <i class="ri-file-chart-line text-cyan-600 text-2xl mr-3"></i>
                                            <h4
                                                class="text-xl font-bold bg-gradient-to-r from-secondary to-primary bg-clip-text text-transparent">
                                                Advanced Report Generation</h4>
                                        </div>
                                        <p class="text-gray-700">Generate detailed reports and export data for analysis
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="relative">
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-purple-500/10 to-indigo-500/10 rounded-3xl blur-2xl">
                                </div>
                                <div class="relative">
                                    <img src="{{ asset('assets/coloured-statistics-design.png') }}"
                                        alt="Analytics Dashboard"
                                        class="w-full h-auto object-contain rounded-lg shadow-lg">
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- FAQ Section -->
                <section id="faqs" class="px-6 lg:px-8 py-20 bg-gradient-to-br from-gray-50 to-white min-h-screen"
                    x-data="{ openFaq: null }">
                    <div class="w-full max-w-4xl mx-auto">
                        <div class="text-center mb-16">
                            <div
                                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-gray-100 to-gray-200 rounded-full mb-6">
                                <span class="text-gray-700 text-sm font-medium">❓ Got Questions?</span>
                            </div>
                            <h2 class="text-4xl lg:text-5xl font-bold text-secondary mb-6">
                                Frequently Asked Questions
                            </h2>
                        </div>

                        <div class="space-y-6">
                            <div
                                class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300">
                                <button @click="openFaq = openFaq === 1 ? null : 1"
                                    class="w-full text-left p-8 flex justify-between items-center hover:bg-gray-50 transition-all duration-300">
                                    <span class="text-lg font-semibold text-secondary">How secure is the voting
                                        process?</span>
                                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                                        <i class="ri-arrow-down-s-line transition-transform duration-300 text-gray-600"
                                            :class="openFaq === 1 ? 'rotate-180' : ''"></i>
                                    </div>
                                </button>
                                <div x-show="openFaq === 1" x-collapse class="px-8 pb-8">
                                    <p class="text-gray-600 leading-relaxed">Our platform uses military-grade
                                        encryption, biometric authentication, and blockchain technology to ensure the
                                        highest level of security for all votes cast. Every vote is encrypted end-to-end
                                        and recorded immutably.</p>
                                </div>
                            </div>
                            <div
                                class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300">
                                <button @click="openFaq = openFaq === 2 ? null : 2"
                                    class="w-full text-left p-8 flex justify-between items-center hover:bg-gray-50 transition-all duration-300">
                                    <span class="text-lg font-semibold text-secondary">Can I verify my vote was
                                        counted?</span>
                                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                                        <i class="ri-arrow-down-s-line transition-transform duration-300 text-gray-600"
                                            :class="openFaq === 2 ? 'rotate-180' : ''"></i>
                                    </div>
                                </button>
                                <div x-show="openFaq === 2" x-collapse class="px-8 pb-8">
                                    <p class="text-gray-600 leading-relaxed">Yes, you receive a unique receipt code that
                                        allows you to verify your vote was properly recorded without compromising ballot
                                        secrecy. Our transparent system ensures complete accountability.</p>
                                </div>
                            </div>
                            <div
                                class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300">
                                <button @click="openFaq = openFaq === 3 ? null : 3"
                                    class="w-full text-left p-8 flex justify-between items-center hover:bg-gray-50 transition-all duration-300">
                                    <span class="text-lg font-semibold text-secondary">What devices are
                                        supported?</span>
                                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                                        <i class="ri-arrow-down-s-line transition-transform duration-300 text-gray-600"
                                            :class="openFaq === 3 ? 'rotate-180' : ''"></i>
                                    </div>
                                </button>
                                <div x-show="openFaq === 3" x-collapse class="px-8 pb-8">
                                    <p class="text-gray-600 leading-relaxed">Secure Vote PH works seamlessly on
                                        smartphones, tablets, and computers with modern web browsers. Native mobile apps
                                        are available for both iOS and Android platforms.</p>
                                </div>
                            </div>
                            <div
                                class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300">
                                <button @click="openFaq = openFaq === 4 ? null : 4"
                                    class="w-full text-left p-8 flex justify-between items-center hover:bg-gray-50 transition-all duration-300">
                                    <span class="text-lg font-semibold text-secondary">How do I register to vote?</span>
                                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                                        <i class="ri-arrow-down-s-line transition-transform duration-300 text-gray-600"
                                            :class="openFaq === 4 ? 'rotate-180' : ''"></i>
                                    </div>
                                </button>
                                <div x-show="openFaq === 4" x-collapse class="px-8 pb-8">
                                    <p class="text-gray-600 leading-relaxed">Registration requires valid government ID,
                                        proof of address, and biometric enrollment at authorized registration centers or
                                        through our mobile units. The process is simple and secure.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Sign In/Up Section -->
                <section id="auth" class="px-6 lg:px-8 py-20 bg-gradient-to-br from-gray-50 to-white min-h-screen"
                    x-data="{
                        showSignUp: false,
                        showForgotPassword: false,
                        forgotStep: 1,
                        forgotEmail: '',
                        forgotOtp: '',
                        forgotPassword: '',
                        forgotPasswordConfirmation: '',
                        forgotToken: '',
                        forgotLoading: false,
                        resetForgot() {
                            this.forgotStep = 1;
                            this.forgotEmail = '';
                            this.forgotOtp = '';
                            this.forgotPassword = '';
                            this.forgotPasswordConfirmation = '';
                            this.forgotToken = '';
                            this.forgotLoading = false;
                        }
                    }">
                    <div class="w-full max-w-md mx-auto">
                        <div class="text-center mb-16">
                            <div
                                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-primary/10 to-accent/10 rounded-full mb-6">
                                <span class="text-primary text-sm font-medium">🚀 Get Started</span>
                            </div>
                            <h2 class="text-4xl lg:text-5xl font-bold text-secondary mb-6">
                                Join Secure Vote PH Today
                            </h2>
                            <p class="text-xl text-gray-600 leading-relaxed">
                                Create your account and experience the future of secure voting
                            </p>
                        </div>

                        <div class="bg-white p-8 rounded-2xl shadow-lg border border-gray-100">
                            <!-- Sign In Form -->
                            <div x-show="!showSignUp && !showForgotPassword" x-transition>
                                <h3 class="text-2xl font-bold text-secondary mb-6 text-center">Sign In</h3>

                                <form method="POST" action="{{ url('/login') }}" class="space-y-6">
                                    @csrf
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                        <input type="email" name="email" value="{{ old('email') }}" required
                                            placeholder="Enter your email"
                                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                                        <input type="password" name="password" required
                                            placeholder="Enter your password"
                                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300">
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <label class="flex items-center">
                                            <input type="checkbox" name="remember"
                                                class="rounded border-gray-300 text-primary focus:ring-primary">
                                            <span class="ml-2 text-sm text-gray-600">Remember me</span>
                                        </label>
                                        <button type="button" @click="showForgotPassword = true; showSignUp = false; resetForgot()"
                                            class="text-sm text-primary hover:underline font-medium">
                                            Forgot password?
                                        </button>
                                    </div>
                                    <button type="submit"
                                        class="w-full bg-gradient-to-r from-primary to-primary/90 hover:from-primary/90 hover:to-primary text-white px-6 py-4 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105">
                                        Sign In
                                    </button>

                                    <!-- Google SSO Button -->
                                    <div class="mt-6">
                                        <div class="relative">
                                            <div class="absolute inset-0 flex items-center">
                                                <div class="w-full border-t border-gray-200"></div>
                                            </div>
                                            <div class="relative flex justify-center text-sm">
                                                <span class="px-2 bg-white text-gray-500">or continue with</span>
                                            </div>
                                        </div>

                                        <div class="mt-6 flex justify-center">
                                            <div id="g_id_onload"
                                                 data-client_id="{{ config('services.google.client_id') }}"
                                                 data-callback="handleGoogleCredentialResponse"
                                                 data-auto_select="true"
                                                 data-itp_support="true">
                                            </div>
                                            <div class="g_id_signin"
                                                 data-type="standard"
                                                 data-shape="pill"
                                                 data-theme="outline"
                                                 data-text="continue_with"
                                                 data-size="large"
                                                 data-logo_alignment="left">
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <p class="text-center text-sm text-gray-500 mt-6">
                                    Don't have an account?
                                    <button @click="showSignUp = true"
                                        class="text-primary hover:underline font-medium">Sign up</button>
                                </p>
                            </div>

                            <!-- Sign Up Form -->
                            <div x-show="showSignUp && !showForgotPassword" x-transition>
                                <h3 class="text-2xl font-bold text-secondary mb-6 text-center">Sign Up</h3>

                                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                                    @csrf
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                        <input type="text" name="name" value="{{ old('name') }}" required
                                            placeholder="Enter your full name"
                                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                        <input type="email" name="email" value="{{ old('email') }}" required
                                            placeholder="Enter your email"
                                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                                        <input type="password" name="password" required
                                            placeholder="Create a password (min. 8 characters)"
                                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Confirm
                                            Password</label>
                                        <input type="password" name="password_confirmation" required
                                            placeholder="Confirm your password"
                                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300">
                                    </div>
                                    <button type="submit"
                                        class="w-full bg-gradient-to-r from-accent to-accent/90 hover:from-accent/90 hover:to-accent text-white px-6 py-4 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105">
                                        Create Account
                                    </button>
                                </form>

                                <p class="text-center text-sm text-gray-500 mt-6">
                                    Already have an account?
                                    <button @click="showSignUp = false"
                                        class="text-primary hover:underline font-medium">Sign in</button>
                                </p>
                            </div>

                            <!-- Password Recovery Form -->
                            <div x-show="showForgotPassword" x-transition>
                                <h3 class="text-2xl font-bold text-secondary mb-6 text-center">Password Recovery</h3>

                                <!-- Step 1: Email -->
                                <div x-show="forgotStep === 1" class="space-y-6">
                                    <p class="text-sm text-gray-500 text-center">Enter your registered email address to receive an OTP.</p>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                        <input type="email" x-model="forgotEmail" required
                                            placeholder="Enter your email"
                                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300">
                                    </div>
                                    <button @click="sendForgotOtp()" :disabled="forgotLoading || !forgotEmail"
                                        class="w-full bg-gradient-to-r from-primary to-primary/90 text-white px-6 py-4 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 disabled:opacity-50">
                                        <span x-show="!forgotLoading">Send OTP</span>
                                        <i x-show="forgotLoading" class="ri-loader-4-line animate-spin"></i>
                                    </button>
                                </div>

                                <!-- Step 2: OTP -->
                                <div x-show="forgotStep === 2" class="space-y-6">
                                    <p class="text-sm text-gray-500 text-center">Enter the 6-digit OTP sent to <span class="font-bold text-slate-800" x-text="forgotEmail"></span></p>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">OTP Code</label>
                                        <input type="text" x-model="forgotOtp" required maxlength="6"
                                            placeholder="000000"
                                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 text-center text-2xl tracking-[0.5em]">
                                    </div>
                                    <button @click="verifyForgotOtp()" :disabled="forgotLoading || forgotOtp.length < 6"
                                        class="w-full bg-gradient-to-r from-primary to-primary/90 text-white px-6 py-4 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 disabled:opacity-50">
                                        <span x-show="!forgotLoading">Verify OTP</span>
                                        <i x-show="forgotLoading" class="ri-loader-4-line animate-spin"></i>
                                    </button>
                                    <button @click="forgotStep = 1" class="w-full text-sm text-gray-500 hover:text-primary transition-colors">Back to Email</button>
                                </div>

                                <!-- Step 3: New Password -->
                                <div x-show="forgotStep === 3" class="space-y-4">
                                    <p class="text-sm text-gray-500 text-center">Verification successful. Please enter your new password.</p>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                                        <input type="password" x-model="forgotPassword" required
                                            placeholder="Min. 8 characters"
                                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                                        <input type="password" x-model="forgotPasswordConfirmation" required
                                            placeholder="Confirm your new password"
                                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300">
                                    </div>
                                    <button @click="resetForgotPassword()" :disabled="forgotLoading || !forgotPassword || forgotPassword !== forgotPasswordConfirmation"
                                        class="w-full bg-gradient-to-r from-accent to-accent/90 text-white px-6 py-4 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 disabled:opacity-50">
                                        <span x-show="!forgotLoading">Update Password</span>
                                        <i x-show="forgotLoading" class="ri-loader-4-line animate-spin"></i>
                                    </button>
                                </div>

                                <p class="text-center text-sm text-gray-500 mt-6">
                                    Remember your password?
                                    <button @click="showForgotPassword = false; showSignUp = false"
                                        class="text-primary hover:underline font-medium">Sign in</button>
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>

        <!-- Footer Component -->
        <x-default-footer />
    </div>

    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <script>

        /* ✅ STEP 2 — AUTO-FOCUS SIGN IN (Alpine) */
        function forceSignInAlpine() {
            setTimeout(() => {
                const authSection = document.querySelector('#auth');

                if (!authSection || !authSection.__x) return;

                // Force Sign In view
                authSection.__x.$data.showSignUp = false;
            }, 150);
        }

        // Standard Sign-In triggered by button (optional fallback)
        function handleGoogleSignIn() {
            if (window.google?.accounts?.id) {
                window.google.accounts.id.prompt();
            } else {
                showNotification('Google Sign-In is not available. Please try again later.', 'error');
            }
        }

        /* ✅ PASSWORD RECOVERY FUNCTIONS */
        function sendForgotOtp() {
            const authData = document.querySelector('#auth').__x.$data;
            if (!authData.forgotEmail) return;

            authData.forgotLoading = true;
            fetch('{{ route('password.otp.send.general') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ email: authData.forgotEmail })
            })
            .then(res => res.json())
            .then(data => {
                authData.forgotLoading = false;
                if (data.success) {
                    showNotification(data.message, 'success');
                    authData.forgotStep = 2;
                } else {
                    showNotification(data.message || 'Error sending OTP', 'error');
                }
            })
            .catch(err => {
                authData.forgotLoading = false;
                showNotification('Connection error', 'error');
            });
        }

        function verifyForgotOtp() {
            const authData = document.querySelector('#auth').__x.$data;
            if (!authData.forgotOtp) return;

            authData.forgotLoading = true;
            fetch('{{ route('password.otp.verify.general') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ email: authData.forgotEmail, otp: authData.forgotOtp })
            })
            .then(res => res.json())
            .then(data => {
                authData.forgotLoading = false;
                if (data.success) {
                    authData.forgotToken = data.token;
                    authData.forgotStep = 3;
                    showNotification('OTP Verified successfully!', 'success');
                } else {
                    showNotification(data.message || 'Invalid OTP', 'error');
                }
            })
            .catch(err => {
                authData.forgotLoading = false;
                showNotification('Connection error', 'error');
            });
        }

        function resetForgotPassword() {
            const authData = document.querySelector('#auth').__x.$data;
            if (authData.forgotPassword !== authData.forgotPasswordConfirmation) {
                showNotification('Passwords do not match', 'error');
                return;
            }

            authData.forgotLoading = true;
            fetch('{{ route('password.update.otp.general') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    email: authData.forgotEmail,
                    password: authData.forgotPassword,
                    password_confirmation: authData.forgotPasswordConfirmation,
                    token: authData.forgotToken
                })
            })
            .then(res => res.json())
            .then(data => {
                authData.forgotLoading = false;
                if (data.success) {
                    showNotification(data.message, 'success');
                    setTimeout(() => {
                        authData.showForgotPassword = false;
                        authData.resetForgot();
                    }, 2000);
                } else {
                    showNotification(data.message || 'Error resetting password', 'error');
                }
            })
            .catch(err => {
                authData.forgotLoading = false;
                showNotification('Connection error', 'error');
            });
        }

        function handleGoogleCredentialResponse(response) {
            if (!response.credential) {
                showNotification('Authentication failed. Please try again.', 'error');
                return;
            }

            // Show loading state if we have a button reference (optional)
            const buttonDiv = document.getElementById('google-signin-button');
            if (buttonDiv) {
                buttonDiv.style.opacity = '0.5';
                buttonDiv.style.pointerEvents = 'none';
            }

            fetch('{{ route('auth.google.callback') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ credential: response.credential })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message || 'Successfully signed in!', 'success');
                        setTimeout(() => window.location.href = data.redirect || '/admin/dashboard', 1500);
                    } else {
                        // Display the actual error message from the backend
                        showNotification(data.message || 'Authentication failed', 'error');
                        if (buttonDiv) {
                            buttonDiv.style.opacity = '1';
                            buttonDiv.style.pointerEvents = 'auto';
                        }
                    }
                })
                .catch(error => {
                    console.error('Google Auth Error:', error);
                    showNotification('Connection error or server failure. Check browser console.', 'error');
                    if (buttonDiv) {
                        buttonDiv.style.opacity = '1';
                        buttonDiv.style.pointerEvents = 'auto';
                    }
                });
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-xl shadow-lg transform transition-all duration-300 translate-x-full ${type === 'success' ? 'bg-green-50 border border-green-200 text-green-700' :
                    type === 'error' ? 'bg-red-50 border border-red-200 text-red-700' :
                        'bg-blue-50 border border-blue-200 text-blue-700'
                }`;
            notification.innerHTML = `
            <div class="flex items-center space-x-3">
                <i class="ri-${type === 'success' ? 'check' : type === 'error' ? 'error-warning' : 'info'}-line text-lg"></i>
                <span class="font-medium">${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-gray-400 hover:text-gray-600">
                    <i class="ri-close-line"></i>
                </button>
            </div>
        `;
            document.body.appendChild(notification);
            setTimeout(() => notification.classList.remove('translate-x-full'), 100);
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => notification.remove(), 300);
            }, 5000);
        }

        window.addEventListener('load', () => {
            // Auto-dismiss messages after 5 seconds
            setTimeout(() => {
                const messages = document.querySelectorAll('.fixed.top-4.right-4');
                messages.forEach(msg => {
                    msg.style.opacity = '0';
                    setTimeout(() => msg.remove(), 300);
                });
            }, 5000);
        });
    </script>

    @stack('scripts')
</body>

</html>
