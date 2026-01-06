@extends('layouts.app-main-admin')

@section('content')

    <div x-data="{
        activeTab: 'account',
        showToast: false,
        toastMessage: '',
        toastType: 'success'
    }" class="flex-1 flex flex-col">

        <x-admin-header title="Settings" />

        <!-- Main Content Area -->
        <main class="flex-1 min-h-screen">

            <!-- Mobile Header -->
            <header class="lg:hidden bg-white shadow-sm border-b px-4 py-3 flex items-center justify-between">
                <button @click="collapsed = false"
                        class="p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                    <i class="ri-menu-fold-line text-lg rotate-180"></i>
                </button>
                <h1 class="text-lg font-semibold text-slate-800">Settings</h1>
                <div class="w-10"></div>
            </header>

            <!-- Page Content -->
            <div class="p-6">
                <div class="max-w-4xl mx-auto">
                    <!-- Page Header -->
                    <div class="mb-8">
                        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Settings</h1>
                        <p class="text-slate-500 mt-2">Manage your account, security preferences, and system configurations</p>
                    </div>

                    <!-- Tabs Navigation -->
                    <div class="flex items-center space-x-1 bg-slate-100 p-1 rounded-xl mb-8 overflow-x-auto no-scrollbar">
                        <button @click="activeTab = 'account'"
                                :class="activeTab === 'account' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                                class="flex-1 py-2.5 text-sm font-semibold rounded-lg transition-all">
                            Account Details
                        </button>
                        <button @click="activeTab = 'security'"
                                :class="activeTab === 'security' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                                class="flex-1 py-2.5 text-sm font-semibold rounded-lg transition-all">
                            Security
                        </button>
                        <button @click="activeTab = 'notifications'"
                                :class="activeTab === 'notifications' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                                class="flex-1 py-2.5 text-sm font-semibold rounded-lg transition-all">
                            Notifications & Communication
                        </button>
                        <button @click="activeTab = 'privacy'"
                                :class="activeTab === 'privacy' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                                class="flex-1 py-2.5 text-sm font-semibold rounded-lg transition-all">
                            Privacy & Data Control
                        </button>
                    </div>

                    <!-- Account Tab -->
                    <div x-show="activeTab === 'account'" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="space-y-6">

                        <form action="{{ route('admin.profile.update') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                            @csrf
                            @method('PUT')
                            <div class="p-8">
                                <h3 class="text-lg font-bold text-slate-800 mb-6">Profile Information</h3>
                                <div class="grid grid-cols-1 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">Full Name</label>
                                        <input type="text" name="name" value="{{ auth()->user()->name }}"
                                               class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 transition-all px-4 py-3 bg-slate-50 focus:bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">Email Address</label>
                                        <input type="email" name="email" value="{{ auth()->user()->email }}"
                                               class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 transition-all px-4 py-3 bg-slate-50 focus:bg-white">
                                    </div>
                                </div>
                            </div>
                            <div class="px-8 py-4 bg-slate-50 border-t border-slate-100 flex justify-end">
                                <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Security Tab -->
                    <div x-show="activeTab === 'security'" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="space-y-6">

                        <form action="{{ route('admin.profile.password') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                            @csrf
                            @method('PUT')
                            <div class="p-8">
                                <h3 class="text-lg font-bold text-slate-800 mb-6">Change Password</h3>
                                <div class="grid grid-cols-1 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">Current Password</label>
                                        <input type="password" name="current_password" required
                                               class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 transition-all px-4 py-3 bg-slate-50 focus:bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">New Password</label>
                                        <input type="password" name="password" required
                                               class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 transition-all px-4 py-3 bg-slate-50 focus:bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">Confirm New Password</label>
                                        <input type="password" name="password_confirmation" required
                                               class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 transition-all px-4 py-3 bg-slate-50 focus:bg-white">
                                    </div>
                                </div>
                            </div>
                            <div class="px-8 py-4 bg-slate-50 border-t border-slate-100 flex justify-end">
                                <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all">
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Notifications & Communication Tab -->
                    <div x-show="activeTab === 'notifications'" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="space-y-6">

                        <form action="{{ route('admin.settings.update') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                            @csrf
                            @method('PUT')
                            <div class="p-8">
                                <h3 class="text-lg font-bold text-slate-800 mb-6">Notifications & Communication</h3>
                                <div class="space-y-6">
                                    <label class="flex items-center justify-between p-4 bg-slate-50 rounded-xl cursor-pointer hover:bg-slate-100 transition-colors">
                                        <div>
                                            <span class="block text-sm font-bold text-slate-800">Email Notifications</span>
                                            <span class="block text-xs text-slate-500">Send system alerts and voting receipts via email</span>
                                        </div>
                                        <input type="checkbox" name="email_notifications" value="1" {{ ($settings['email_notifications']->value ?? '1') == '1' ? 'checked' : '' }}
                                               class="w-5 h-5 text-indigo-600 rounded-lg border-slate-300 focus:ring-indigo-500">
                                    </label>

                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">Contact Email for Voters</label>
                                        <input type="email" name="contact_email" value="{{ $settings['contact_email']->value ?? 'support@securevote.ph' }}"
                                               class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 transition-all px-4 py-3 bg-slate-50 focus:bg-white">
                                    </div>
                                </div>
                            </div>
                            <div class="px-8 py-4 bg-slate-50 border-t border-slate-100 flex justify-end">
                                <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all">
                                    Save Notification Settings
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Privacy & Data Control Tab -->
                    <div x-show="activeTab === 'privacy'" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="space-y-6">

                        <form action="{{ route('admin.settings.update') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                            @csrf
                            @method('PUT')
                            <div class="p-8">
                                <h3 class="text-lg font-bold text-slate-800 mb-6">Privacy & Data Control</h3>
                                <div class="space-y-6">
                                    <label class="flex items-center justify-between p-4 bg-slate-50 rounded-xl cursor-pointer hover:bg-slate-100 transition-colors">
                                        <div>
                                            <span class="block text-sm font-bold text-slate-800">Maintenance Mode</span>
                                            <span class="block text-xs text-slate-500">Prevent voters from accessing the portal during maintenance</span>
                                        </div>
                                        <input type="checkbox" name="maintenance_mode" value="1" {{ ($settings['maintenance_mode']->value ?? '0') == '1' ? 'checked' : '' }}
                                               class="w-5 h-5 text-indigo-600 rounded-lg border-slate-300 focus:ring-indigo-500">
                                    </label>

                                    <label class="flex items-center justify-between p-4 bg-slate-50 rounded-xl cursor-pointer hover:bg-slate-100 transition-colors">
                                        <div>
                                            <span class="block text-sm font-bold text-slate-800">Results Privacy</span>
                                            <span class="block text-xs text-slate-500">Publicly visible election results analytics</span>
                                        </div>
                                        <input type="checkbox" name="results_public" value="1" {{ ($settings['results_public']->value ?? '0') == '1' ? 'checked' : '' }}
                                               class="w-5 h-5 text-indigo-600 rounded-lg border-slate-300 focus:ring-indigo-500">
                                    </label>

                                    <div class="pt-4 border-t border-slate-100">
                                        <h4 class="text-sm font-bold text-slate-800 mb-4">Data Management</h4>
                                        <div class="flex flex-wrap gap-4">
                                            <a href="{{ route('admin.settings.backup') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 transition-all text-sm font-bold">
                                                <i class="ri-download-cloud-line"></i>
                                                Export System Backup
                                            </a>
                                            <button type="button" @click="if(confirm('Reset all system settings to default?')) document.getElementById('reset-settings').submit()"
                                                    class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-all text-sm font-bold">
                                                <i class="ri-refresh-line"></i>
                                                Reset to Defaults
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="px-8 py-4 bg-slate-50 border-t border-slate-100 flex justify-end">
                                <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all">
                                    Save Privacy Settings
                                </button>
                            </div>
                        </form>
                    </div>

                    <form id="reset-settings" action="{{ route('admin.settings.reset') }}" method="POST" class="hidden">
                        @csrf
                    </form>

                    <!-- Global Feedback Messages -->
                    @if(session('success'))
                        <div class="mt-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center gap-3">
                            <i class="ri-checkbox-circle-line text-xl"></i>
                            <span class="font-medium">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mt-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl">
                            <div class="flex items-center gap-3 mb-2">
                                <i class="ri-error-warning-line text-xl"></i>
                                <span class="font-bold">Please correct the following:</span>
                            </div>
                            <ul class="list-disc list-inside text-sm space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </main>

    </div>

@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
    </style>
@endpush
