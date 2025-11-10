@extends('layouts.app-main-admin')

@section('content')
    <!-- Global Success/Error Messages (Fixed Position) -->
    @if(session('success'))
        <div class="fixed top-4 right-4 z-50 bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-xl shadow-lg transform transition-all duration-300">
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
        <div class="fixed top-4 right-4 z-50 bg-blue-50 border border-blue-200 text-blue-700 px-6 py-4 rounded-xl shadow-lg transform transition-all duration-300">
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
        <div class="fixed top-4 right-4 z-50 bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-xl shadow-lg max-w-md">
            <div class="flex items-start space-x-3">
                <i class="ri-error-warning-line text-lg mt-0.5"></i>
                <div class="flex-1">
                    @foreach($errors->all() as $error)
                        <p class="font-medium">{{ $error }}</p>
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
        <main>
            <div class="max-w-7xl mx-auto">
                <!-- Welcome Section -->
                <section class="welcome-section px-6 lg:px-8 py-20 lg:py-32 bg-gradient-to-br from-gray-50 to-white min-h-screen flex items-center">
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
                                    Experience the future of democratic participation with cutting-edge security, real-time analytics, and geo-location verification.
                                </p>
                                <div class="flex flex-col sm:flex-row gap-4">
                                    <button class="group bg-gradient-to-r from-primary to-primary/90 hover:from-primary/90 hover:to-primary text-white px-8 py-4 rounded-2xl font-semibold transition-all duration-300 transform hover:scale-105 hover:shadow-2xl">
                                        Start Voting Now
                                        <i class="ri-arrow-right-line ml-2 group-hover:translate-x-1 transition-transform"></i>
                                    </button>
                                    <button class="bg-white border-2 border-gray-200 hover:border-primary text-gray-700 hover:text-primary px-8 py-4 rounded-2xl font-semibold transition-all duration-300 hover:shadow-lg">
                                        Learn More
                                    </button>
                                </div>
                            </div>
                            <!-- Right Visual -->
                            <div class="relative">
                                <div class="absolute inset-0 bg-gradient-to-r from-primary/20 to-accent/20 rounded-3xl blur-3xl"></div>
                                <div class="relative bg-white p-8 rounded-3xl shadow-2xl">
                                    <img src="{{ asset('asset/Voting-amico.svg') }}" alt="Secure Voting" class="w-full h-auto object-contain">
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Geographic Section -->
                <section id="geo" class="px-6 lg:px-8 py-20 bg-gradient-to-br from-gray-50 to-white min-h-screen">
                    <div class="w-full">
                        <div class="text-center mb-16 max-w-3xl mx-auto">
                            <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-primary/10 to-accent/10 rounded-full mb-6">
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
                                        Define precise geographic boundaries for your elections. Control where voters can participate with customizable radius settings.
                                    </p>
                                </div>
                                <div class="space-y-6">
                                    <div class="group bg-gradient-to-r from-indigo-50 to-indigo-100/50 p-8 rounded-2xl border-l-4 border-indigo-500 hover:shadow-lg transition-all duration-300">
                                        <div class="flex items-center mb-4">
                                            <i class="ri-settings-3-line text-indigo-600 text-2xl mr-3"></i>
                                            <h4 class="text-xl font-bold bg-gradient-to-r from-secondary to-primary bg-clip-text text-transparent">Custom Voting Boundaries</h4>
                                        </div>
                                        <p class="text-gray-700">Set custom voting boundaries with flexible radius controls</p>
                                    </div>
                                    <div class="group bg-gradient-to-r from-orange-50 to-orange-100/50 p-8 rounded-2xl border-l-4 border-orange-500 hover:shadow-lg transition-all duration-300">
                                        <div class="flex items-center mb-4">
                                            <i class="ri-radar-line text-orange-600 text-2xl mr-3"></i>
                                            <h4 class="text-xl font-bold bg-gradient-to-r from-secondary to-primary bg-clip-text text-transparent">Real-time Location Monitoring</h4>
                                        </div>
                                        <p class="text-gray-700">Monitor voter locations in real-time for security purposes</p>
                                    </div>
                                    <div class="group bg-gradient-to-r from-teal-50 to-teal-100/50 p-8 rounded-2xl border-l-4 border-teal-500 hover:shadow-lg transition-all duration-300">
                                        <div class="flex items-center mb-4">
                                            <i class="ri-building-2-line text-teal-600 text-2xl mr-3"></i>
                                            <h4 class="text-xl font-bold bg-gradient-to-r from-secondary to-primary bg-clip-text text-transparent">Multiple Voting Zones</h4>
                                        </div>
                                        <p class="text-gray-700">Configure multiple voting zones with different parameters</p>
                                    </div>
                                </div>
                            </div>
                            <div class="relative">
                                <div class="absolute inset-0 bg-gradient-to-r from-primary/10 to-accent/10 rounded-3xl blur-2xl"></div>
                                <div class="relative">
                                    <img src="{{ asset('asset/33633910_map.jpg') }}" alt="Geo Location Verification" class="w-full h-auto object-contain rounded-lg shadow-lg">
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Security Section -->
                <section id="security" class="px-6 lg:px-8 py-20 bg-gradient-to-br from-gray-50 to-white min-h-screen">
                    <div class="w-full max-w-6xl mx-auto">
                        <div class="text-center mb-16">
                            <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500/10 to-blue-600/10 rounded-full mb-6">
                                <span class="text-blue-600 text-sm font-medium">🔒 Security First</span>
                            </div>
                            <h2 class="text-4xl lg:text-5xl font-bold bg-gradient-to-r from-secondary to-primary bg-clip-text text-transparent mb-6">
                                Enterprise-Grade Security
                            </h2>
                        </div>
                        <div class="grid lg:grid-cols-2 gap-12">
                            <div class="relative">
                                <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 to-purple-500/10 rounded-3xl blur-2xl"></div>
                                <div class="relative">
                                    <img src="{{ asset('asset/concept-illustration-data-security-technology.png') }}" alt="Security Features" class="w-full h-auto object-contain rounded-lg shadow-lg">
                                </div>
                            </div>
                            <div class="space-y-6">
                                <div class="group bg-gradient-to-r from-blue-50 to-blue-100/50 p-8 rounded-2xl border-l-4 border-blue-500 hover:shadow-lg transition-all duration-300">
                                    <div class="flex items-center mb-4">
                                        <i class="ri-shield-check-line text-blue-600 text-2xl mr-3"></i>
                                        <h3 class="text-xl font-bold bg-gradient-to-r from-secondary to-primary bg-clip-text text-transparent">End-to-End Encryption</h3>
                                    </div>
                                    <p class="text-gray-700">Military-grade AES-256 encryption protects every vote from device to server.</p>
                                </div>
                                <div class="group bg-gradient-to-r from-green-50 to-green-100/50 p-8 rounded-2xl border-l-4 border-green-500 hover:shadow-lg transition-all duration-300">
                                    <div class="flex items-center mb-4">
                                        <i class="ri-fingerprint-line text-green-600 text-2xl mr-3"></i>
                                        <h3 class="text-xl font-bold bg-gradient-to-r from-secondary to-primary bg-clip-text text-transparent">Biometric Authentication</h3>
                                    </div>
                                    <p class="text-gray-700">Advanced biometric authentication using fingerprint and facial recognition.</p>
                                </div>
                                <div class="group bg-gradient-to-r from-purple-50 to-purple-100/50 p-8 rounded-2xl border-l-4 border-purple-500 hover:shadow-lg transition-all duration-300">
                                    <div class="flex items-center mb-4">
                                        <i class="ri-links-line text-purple-600 text-2xl mr-3"></i>
                                        <h3 class="text-xl font-bold bg-gradient-to-r from-secondary to-primary bg-clip-text text-transparent">Blockchain Technology</h3>
                                    </div>
                                    <p class="text-gray-700">Immutable vote recording using distributed ledger for complete transparency.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Analytics Section -->
                <section id="analytics" class="px-6 lg:px-8 py-20 bg-gradient-to-br from-gray-50 to-white min-h-screen">
                    <div class="w-full max-w-6xl mx-auto">
                        <div class="text-center mb-16">
                            <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-500/10 to-indigo-500/10 rounded-full mb-6">
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
                                        Monitor voting patterns, participation rates, and results in real-time with interactive dashboards and comprehensive reporting tools.
                                    </p>
                                </div>
                                <div class="space-y-6">
                                    <div class="group bg-gradient-to-r from-emerald-50 to-emerald-100/50 p-8 rounded-2xl border-l-4 border-emerald-500 hover:shadow-lg transition-all duration-300">
                                        <div class="flex items-center mb-4">
                                            <i class="ri-time-line text-emerald-600 text-2xl mr-3"></i>
                                            <h4 class="text-xl font-bold bg-gradient-to-r from-secondary to-primary bg-clip-text text-transparent">Real-time Progress Tracking</h4>
                                        </div>
                                        <p class="text-gray-700">Track voting progress and system status in real-time</p>
                                    </div>
                                    <div class="group bg-gradient-to-r from-rose-50 to-rose-100/50 p-8 rounded-2xl border-l-4 border-rose-500 hover:shadow-lg transition-all duration-300">
                                        <div class="flex items-center mb-4">
                                            <i class="ri-bar-chart-grouped-line text-rose-600 text-2xl mr-3"></i>
                                            <h4 class="text-xl font-bold bg-gradient-to-r from-secondary to-primary bg-clip-text text-transparent">Interactive Data Visualization</h4>
                                        </div>
                                        <p class="text-gray-700">Explore data through charts, graphs, and interactive maps</p>
                                    </div>
                                    <div class="group bg-gradient-to-r from-cyan-50 to-cyan-100/50 p-8 rounded-2xl border-l-4 border-cyan-500 hover:shadow-lg transition-all duration-300">
                                        <div class="flex items-center mb-4">
                                            <i class="ri-file-chart-line text-cyan-600 text-2xl mr-3"></i>
                                            <h4 class="text-xl font-bold bg-gradient-to-r from-secondary to-primary bg-clip-text text-transparent">Advanced Report Generation</h4>
                                        </div>
                                        <p class="text-gray-700">Generate detailed reports and export data for analysis</p>
                                    </div>
                                </div>
                            </div>
                            <div class="relative">
                                <div class="absolute inset-0 bg-gradient-to-r from-purple-500/10 to-indigo-500/10 rounded-3xl blur-2xl"></div>
                                <div class="relative">
                                    <img src="{{ asset('asset/coloured-statistics-design.png') }}" alt="Analytics Dashboard" class="w-full h-auto object-contain rounded-lg shadow-lg">
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- FAQ Section -->
                <section id="faqs" class="px-6 lg:px-8 py-20 bg-gradient-to-br from-gray-50 to-white min-h-screen" x-data="{ openFaq: null }">
                    <div class="w-full max-w-4xl mx-auto">
                        <div class="text-center mb-16">
                            <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-gray-100 to-gray-200 rounded-full mb-6">
                                <span class="text-gray-700 text-sm font-medium">❓ Got Questions?</span>
                            </div>
                            <h2 class="text-4xl lg:text-5xl font-bold text-secondary mb-6">
                                Frequently Asked Questions
                            </h2>
                        </div>

                        <div class="space-y-6">
                            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300">
                                <button @click="openFaq = openFaq === 1 ? null : 1"
                                        class="w-full text-left p-8 flex justify-between items-center hover:bg-gray-50 transition-all duration-300">
                                    <span class="text-lg font-semibold text-secondary">How secure is the voting process?</span>
                                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                                        <i class="ri-arrow-down-s-line transition-transform duration-300 text-gray-600" :class="openFaq === 1 ? 'rotate-180' : ''"></i>
                                    </div>
                                </button>
                                <div x-show="openFaq === 1" x-collapse class="px-8 pb-8">
                                    <p class="text-gray-600 leading-relaxed">Our platform uses military-grade encryption, biometric authentication, and blockchain technology to ensure the highest level of security for all votes cast. Every vote is encrypted end-to-end and recorded immutably.</p>
                                </div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300">
                                <button @click="openFaq = openFaq === 2 ? null : 2"
                                        class="w-full text-left p-8 flex justify-between items-center hover:bg-gray-50 transition-all duration-300">
                                    <span class="text-lg font-semibold text-secondary">Can I verify my vote was counted?</span>
                                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                                        <i class="ri-arrow-down-s-line transition-transform duration-300 text-gray-600" :class="openFaq === 2 ? 'rotate-180' : ''"></i>
                                    </div>
                                </button>
                                <div x-show="openFaq === 2" x-collapse class="px-8 pb-8">
                                    <p class="text-gray-600 leading-relaxed">Yes, you receive a unique receipt code that allows you to verify your vote was properly recorded without compromising ballot secrecy. Our transparent system ensures complete accountability.</p>
                                </div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300">
                                <button @click="openFaq = openFaq === 3 ? null : 3"
                                        class="w-full text-left p-8 flex justify-between items-center hover:bg-gray-50 transition-all duration-300">
                                    <span class="text-lg font-semibold text-secondary">What devices are supported?</span>
                                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                                        <i class="ri-arrow-down-s-line transition-transform duration-300 text-gray-600" :class="openFaq === 3 ? 'rotate-180' : ''"></i>
                                    </div>
                                </button>
                                <div x-show="openFaq === 3" x-collapse class="px-8 pb-8">
                                    <p class="text-gray-600 leading-relaxed">Secure Vote PH works seamlessly on smartphones, tablets, and computers with modern web browsers. Native mobile apps are available for both iOS and Android platforms.</p>
                                </div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300">
                                <button @click="openFaq = openFaq === 4 ? null : 4"
                                        class="w-full text-left p-8 flex justify-between items-center hover:bg-gray-50 transition-all duration-300">
                                    <span class="text-lg font-semibold text-secondary">How do I register to vote?</span>
                                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                                        <i class="ri-arrow-down-s-line transition-transform duration-300 text-gray-600" :class="openFaq === 4 ? 'rotate-180' : ''"></i>
                                    </div>
                                </button>
                                <div x-show="openFaq === 4" x-collapse class="px-8 pb-8">
                                    <p class="text-gray-600 leading-relaxed">Registration requires valid government ID, proof of address, and biometric enrollment at authorized registration centers or through our mobile units. The process is simple and secure.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Sign In/Up Section -->
                <section id="auth" class="px-6 lg:px-8 py-20 bg-gradient-to-br from-gray-50 to-white min-h-screen" x-data="{ showSignUp: false }">
                    <div class="w-full max-w-md mx-auto">
                        <div class="text-center mb-16">
                            <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-primary/10 to-accent/10 rounded-full mb-6">
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
                            <div x-show="!showSignUp" x-transition>
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
                                            <input type="checkbox" name="remember" class="rounded border-gray-300 text-primary focus:ring-primary">
                                            <span class="ml-2 text-sm text-gray-600">Remember me</span>
                                        </label>
                                    </div>
                                    <button type="submit" class="w-full bg-gradient-to-r from-primary to-primary/90 hover:from-primary/90 hover:to-primary text-white px-6 py-4 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105">
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

                                        <button type="button"
                                                onclick="handleGoogleSignIn()"
                                                class="w-full mt-6 group bg-white border-2 border-gray-200 hover:border-gray-300 hover:shadow-lg text-gray-700 px-6 py-4 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 hover:bg-gray-50 flex items-center justify-center space-x-3">
                                            <svg class="w-5 h-5 transition-transform group-hover:scale-110" viewBox="0 0 24 24">
                                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                            </svg>
                                            <span>Continue with Google</span>
                                        </button>
                                    </div>
                                </form>

                                <p class="text-center text-sm text-gray-500 mt-6">
                                    Don't have an account?
                                    <button @click="showSignUp = true" class="text-primary hover:underline font-medium">Sign up</button>
                                </p>
                            </div>

                            <!-- Sign Up Form -->
                            <div x-show="showSignUp" x-transition>
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
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                                        <input type="password" name="password_confirmation" required
                                               placeholder="Confirm your password"
                                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300">
                                    </div>
                                    <button type="submit" class="w-full bg-gradient-to-r from-accent to-accent/90 hover:from-accent/90 hover:to-accent text-white px-6 py-4 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105">
                                        Create Account
                                    </button>
                                </form>

                                <p class="text-center text-sm text-gray-500 mt-6">
                                    Already have an account?
                                    <button @click="showSignUp = false" class="text-primary hover:underline font-medium">Sign in</button>
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
        function initializeGoogleSignIn() {
            window.google?.accounts?.id?.initialize({
                client_id: "22952197713-bnf9d78ndf30r0u3ct3ehk152aljq3ji.apps.googleusercontent.com",
                callback: handleGoogleCredentialResponse,
                auto_select: false,
                cancel_on_tap_outside: false
            });
        }

        function handleGoogleSignIn() {
            if (window.google?.accounts?.id) {
                window.google.accounts.id.prompt();
            } else {
                showNotification('Google Sign-In is not available. Please try again later.', 'error');
            }
        }

        function handleGoogleCredentialResponse(response) {
            if (!response.credential) {
                showNotification('Authentication failed. Please try again.', 'error');
                return;
            }

            const button = document.querySelector('button[onclick="handleGoogleSignIn()"]');
            const originalContent = button.innerHTML;
            button.innerHTML = '<svg class="animate-spin w-5 h-5 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            button.disabled = true;

            fetch('/auth/google/callback', {
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
                        throw new Error(data.message || 'Authentication failed');
                    }
                })
                .catch(error => {
                    showNotification(error.message || 'Authentication failed. Please try again.', 'error');
                })
                .finally(() => {
                    button.innerHTML = originalContent;
                    button.disabled = false;
                });
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-xl shadow-lg transform transition-all duration-300 translate-x-full ${
                type === 'success' ? 'bg-green-50 border border-green-200 text-green-700' :
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
            if (window.google?.accounts?.id) {
                initializeGoogleSignIn();
            } else {
                const checkGoogle = setInterval(() => {
                    if (window.google?.accounts?.id) {
                        clearInterval(checkGoogle);
                        initializeGoogleSignIn();
                    }
                }, 100);
                setTimeout(() => clearInterval(checkGoogle), 10000);
            }

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

@endsection
