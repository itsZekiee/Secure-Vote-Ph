@extends('layouts.app-main-admin')

@section('content')
    <div x-data="{
        activeTab: 'account',
        showToast: false,
        toastMessage: '',
        toastType: 'success'
    }" class="flex min-h-screen bg-[#F8FAFC]">

        <x-admin-sidebar />

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Header -->
            <header class="bg-white border-b border-slate-200 sticky top-0 z-20">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        <div class="flex items-center gap-3">
                            <div class="lg:hidden">
                                <button @click="collapsed = false" class="p-2 rounded-lg text-slate-600 hover:bg-slate-100 transition-colors">
                                    <i class="ri-menu-2-fill text-xl"></i>
                                </button>
                            </div>
                            <div>
                                <h1 class="text-xl font-bold text-slate-900">System Settings</h1>
                                <p class="text-[11px] text-slate-500 font-medium">Platform preferences & security</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="hidden sm:flex items-center px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-full text-[10px] font-bold uppercase tracking-wider border border-indigo-100">
                                <span class="relative flex h-1.5 w-1.5 mr-1.5">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-indigo-500"></span>
                                </span>
                                Admin Mode
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="flex-1 overflow-y-auto custom-scrollbar">
                <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

                    <div class="grid lg:grid-cols-12 gap-8">
                        <!-- Sidebar Navigation -->
                        <aside class="lg:col-span-4 space-y-2">
                            <nav class="sticky top-24 space-y-1">
                                <button @click="activeTab = 'account'"
                                        :class="activeTab === 'account' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 hover:bg-white hover:text-indigo-600'"
                                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 group">
                                    <div :class="activeTab === 'account' ? 'bg-white/20' : 'bg-slate-100 group-hover:bg-indigo-50'" class="w-9 h-9 rounded-lg flex items-center justify-center transition-colors">
                                        <i class="ri-user-settings-line text-base"></i>
                                    </div>
                                    <div class="text-left">
                                        <p class="leading-none text-xs">Account Profile</p>
                                        <p :class="activeTab === 'account' ? 'text-indigo-100' : 'text-slate-400'" class="text-[10px] mt-0.5 font-medium">Personal Info</p>
                                    </div>
                                </button>

                                <button @click="activeTab = 'security'"
                                        :class="activeTab === 'security' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 hover:bg-white hover:text-indigo-600'"
                                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 group">
                                    <div :class="activeTab === 'security' ? 'bg-white/20' : 'bg-slate-100 group-hover:bg-indigo-50'" class="w-9 h-9 rounded-lg flex items-center justify-center transition-colors">
                                        <i class="ri-shield-keyhole-line text-base"></i>
                                    </div>
                                    <div class="text-left">
                                        <p class="leading-none text-xs">Security & Access</p>
                                        <p :class="activeTab === 'security' ? 'text-indigo-100' : 'text-slate-400'" class="text-[10px] mt-0.5 font-medium">Protection</p>
                                    </div>
                                </button>

                                <button @click="activeTab = 'notifications'"
                                        :class="activeTab === 'notifications' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 hover:bg-white hover:text-indigo-600'"
                                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 group">
                                    <div :class="activeTab === 'notifications' ? 'bg-white/20' : 'bg-slate-100 group-hover:bg-indigo-50'" class="w-9 h-9 rounded-lg flex items-center justify-center transition-colors">
                                        <i class="ri-notification-3-line text-base"></i>
                                    </div>
                                    <div class="text-left">
                                        <p class="leading-none text-xs">Communications</p>
                                        <p :class="activeTab === 'notifications' ? 'text-indigo-100' : 'text-slate-400'" class="text-[10px] mt-0.5 font-medium">Alerts</p>
                                    </div>
                                </button>

                                <button @click="activeTab = 'privacy'"
                                        :class="activeTab === 'privacy' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 hover:bg-white hover:text-indigo-600'"
                                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 group">
                                    <div :class="activeTab === 'privacy' ? 'bg-white/20' : 'bg-slate-100 group-hover:bg-indigo-50'" class="w-9 h-9 rounded-lg flex items-center justify-center transition-colors">
                                        <i class="ri-lock-2-line text-base"></i>
                                    </div>
                                    <div class="text-left">
                                        <p class="leading-none text-xs">System Control</p>
                                        <p :class="activeTab === 'privacy' ? 'text-indigo-100' : 'text-slate-400'" class="text-[10px] mt-0.5 font-medium">Maintenance</p>
                                    </div>
                                </button>
                            </nav>

                            @if(session('success'))
                                <div class="mt-6 p-4 bg-emerald-50 border border-emerald-100 rounded-xl animate-in fade-in slide-in-from-bottom-4 duration-500">
                                    <div class="flex items-center gap-2 text-emerald-700 font-bold text-xs mb-1">
                                        <i class="ri-checkbox-circle-fill text-lg"></i>
                                        Success!
                                    </div>
                                    <p class="text-emerald-600 text-[10px] font-medium leading-relaxed">{{ session('success') }}</p>
                                </div>
                            @endif

                            @if($errors->any())
                                <div class="mt-6 p-4 bg-red-50 border border-red-100 rounded-xl animate-in fade-in slide-in-from-bottom-4 duration-500">
                                    <div class="flex items-center gap-2 text-red-700 font-bold text-xs mb-2">
                                        <i class="ri-error-warning-fill text-lg"></i>
                                        Review Errors
                                    </div>
                                    <ul class="text-red-600 text-[10px] font-medium space-y-1">
                                        @foreach($errors->all() as $error)
                                            <li class="flex gap-2"><span>•</span> {{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </aside>

                        <!-- Main Content Tabs -->
                        <div class="lg:col-span-8">
                            <!-- Account Tab -->
                            <div x-show="activeTab === 'account'" x-cloak
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 translate-y-8"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="space-y-6">

                                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
                                    <div class="p-6 sm:p-8">
                                        <div class="flex items-center gap-4 mb-8">
                                            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center text-white text-lg font-bold shadow-sm">
                                                {{ substr(auth()->user()->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-slate-900">Personal Profile</h3>
                                                <p class="text-xs text-slate-500 font-medium">Update account identity</p>
                                            </div>
                                        </div>

                                        <form action="{{ route('admin.profile.update') }}" method="POST" class="space-y-6">
                                            @csrf
                                            @method('PUT')

                                            <div class="grid sm:grid-cols-2 gap-4">
                                                <div class="space-y-1.5">
                                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Full Name</label>
                                                    <div class="relative group">
                                                        <i class="ri-user-3-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                                        <input type="text" name="name" value="{{ auth()->user()->name }}"
                                                               class="w-full bg-slate-50 border-none rounded-xl px-12 py-3 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white transition-all">
                                                    </div>
                                                </div>
                                                <div class="space-y-1.5">
                                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Email Address</label>
                                                    <div class="relative group">
                                                        <i class="ri-mail-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                                        <input type="email" name="email" value="{{ auth()->user()->email }}"
                                                               class="w-full bg-slate-50 border-none rounded-xl px-12 py-3 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white transition-all">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="pt-4 border-t border-slate-100 flex justify-end">
                                                <button type="submit" class="px-8 py-3 bg-slate-900 text-white rounded-xl font-bold text-xs hover:bg-indigo-600 transition-all duration-300">
                                                    SAVE PROFILE
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Security Tab -->
                            <div x-show="activeTab === 'security'" x-cloak
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 translate-y-8"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="space-y-6">

                                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
                                    <div class="p-6 sm:p-8">
                                        <div class="flex items-center gap-4 mb-8">
                                            <div class="w-12 h-12 bg-slate-900 rounded-xl flex items-center justify-center text-white text-lg shadow-sm">
                                                <i class="ri-lock-password-line"></i>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-slate-900">Change Password</h3>
                                                <p class="text-xs text-slate-500 font-medium">Use a long, random password</p>
                                            </div>
                                        </div>

                                        <form action="{{ route('admin.profile.password') }}" method="POST" class="space-y-5">
                                            @csrf
                                            @method('PUT')

                                            <div class="space-y-1.5">
                                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Current Password</label>
                                                <div class="relative group">
                                                    <i class="ri-key-2-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                                    <input type="password" name="current_password" required
                                                           class="w-full bg-slate-50 border-none rounded-xl px-12 py-3 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white transition-all">
                                                </div>
                                            </div>

                                            <div class="grid sm:grid-cols-2 gap-4">
                                                <div class="space-y-1.5">
                                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">New Password</label>
                                                    <div class="relative group">
                                                        <i class="ri-shield-flash-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                                        <input type="password" name="password" required
                                                               class="w-full bg-slate-50 border-none rounded-xl px-12 py-3 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white transition-all">
                                                    </div>
                                                </div>
                                                <div class="space-y-1.5">
                                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Confirm New Password</label>
                                                    <div class="relative group">
                                                        <i class="ri-shield-check-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                                        <input type="password" name="password_confirmation" required
                                                               class="w-full bg-slate-50 border-none rounded-xl px-12 py-3 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white transition-all">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="pt-4 border-t border-slate-100 flex justify-end">
                                                <button type="submit" class="px-8 py-3 bg-indigo-600 text-white rounded-xl font-bold text-xs hover:bg-indigo-700 transition-all duration-300">
                                                    UPDATE PASSWORD
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Notifications Tab -->
                            <div x-show="activeTab === 'notifications'" x-cloak
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 translate-y-8"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="space-y-6">

                                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
                                    <div class="p-6 sm:p-8">
                                        <div class="flex items-center gap-4 mb-8">
                                            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-lg shadow-sm">
                                                <i class="ri-customer-service-2-line"></i>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-slate-900">Communication Settings</h3>
                                                <p class="text-xs text-slate-500 font-medium">Manage notifications</p>
                                            </div>
                                        </div>

                                        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
                                            @csrf
                                            @method('PUT')

                                            <div class="space-y-4">
                                                <label class="flex items-center justify-between p-6 bg-slate-50 rounded-[2rem] cursor-pointer hover:bg-slate-100/80 transition-all border border-transparent hover:border-slate-200 group">
                                                    <div class="flex items-center gap-4">
                                                        <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-indigo-600 shadow-sm group-hover:scale-110 transition-transform">
                                                            <i class="ri-mail-send-line text-xl"></i>
                                                        </div>
                                                        <div>
                                                            <span class="block text-sm font-black text-slate-800 uppercase tracking-tight">Email Notifications</span>
                                                            <span class="block text-xs text-slate-500 font-medium">Send system alerts and voting receipts</span>
                                                        </div>
                                                    </div>
                                                    <div class="relative inline-flex items-center cursor-pointer">
                                                        <input type="checkbox" name="email_notifications" value="1" {{ ($settings['email_notifications']->value ?? '1') == '1' ? 'checked' : '' }} class="sr-only peer">
                                                        <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-1 after:left-1 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                                    </div>
                                                </label>

                                                <div class="space-y-2">
                                                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">Voter Support Email</label>
                                                    <div class="relative group">
                                                        <i class="ri-customer-service-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                                        <input type="email" name="contact_email" value="{{ $settings['contact_email']->value ?? 'support@securevote.ph' }}"
                                                               class="w-full bg-slate-50 border-none rounded-2xl px-12 py-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white transition-all">
                                                    </div>
                                                    <p class="text-[10px] text-slate-400 font-medium ml-1">This email will be displayed to voters for support inquiries.</p>
                                                </div>
                                            </div>

                                            <div class="pt-6 border-t border-slate-100 flex justify-end">
                                                <button type="submit" class="px-10 py-4 bg-slate-900 text-white rounded-2xl font-black text-sm hover:bg-indigo-600 transition-all duration-300">
                                                    SAVE CHANGES
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Privacy Tab -->
                            <div x-show="activeTab === 'privacy'" x-cloak
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 translate-y-8"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="space-y-6">

                                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200/60 overflow-hidden">
                                    <div class="p-8 sm:p-10">
                                        <div class="flex items-center gap-5 mb-10">
                                            <div class="w-16 h-16 bg-amber-50 text-amber-600 rounded-3xl flex items-center justify-center text-2xl shadow-sm">
                                                <i class="ri-settings-4-line"></i>
                                            </div>
                                            <div>
                                                <h3 class="text-xl font-black text-slate-900">System Control</h3>
                                                <p class="text-sm text-slate-500 font-medium">Global system flags and data management tools</p>
                                            </div>
                                        </div>

                                        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
                                            @csrf
                                            @method('PUT')

                                            <div class="grid gap-4">
                                                <label class="flex items-center justify-between p-6 bg-slate-50 rounded-[2rem] cursor-pointer hover:bg-slate-100/80 transition-all border border-transparent hover:border-slate-200 group">
                                                    <div class="flex items-center gap-4">
                                                        <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-amber-500 shadow-sm group-hover:scale-110 transition-transform">
                                                            <i class="ri-hammer-line text-xl"></i>
                                                        </div>
                                                        <div>
                                                            <span class="block text-sm font-black text-slate-800 uppercase tracking-tight">Maintenance Mode</span>
                                                            <span class="block text-xs text-slate-500 font-medium">Restrict voter access for updates</span>
                                                        </div>
                                                    </div>
                                                    <div class="relative inline-flex items-center cursor-pointer">
                                                        <input type="checkbox" name="maintenance_mode" value="1" {{ ($settings['maintenance_mode']->value ?? '0') == '1' ? 'checked' : '' }} class="sr-only peer">
                                                        <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-1 after:left-1 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                                                    </div>
                                                </label>

                                                <label class="flex items-center justify-between p-6 bg-slate-50 rounded-[2rem] cursor-pointer hover:bg-slate-100/80 transition-all border border-transparent hover:border-slate-200 group">
                                                    <div class="flex items-center gap-4">
                                                        <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-indigo-600 shadow-sm group-hover:scale-110 transition-transform">
                                                            <i class="ri-bar-chart-2-line text-xl"></i>
                                                        </div>
                                                        <div>
                                                            <span class="block text-sm font-black text-slate-800 uppercase tracking-tight">Public Results</span>
                                                            <span class="block text-xs text-slate-500 font-medium">Make analytics visible to everyone</span>
                                                        </div>
                                                    </div>
                                                    <div class="relative inline-flex items-center cursor-pointer">
                                                        <input type="checkbox" name="results_public" value="1" {{ ($settings['results_public']->value ?? '0') == '1' ? 'checked' : '' }} class="sr-only peer">
                                                        <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-1 after:left-1 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                                    </div>
                                                </label>
                                            </div>

                                            <div class="pt-10 mt-6 border-t border-slate-100">
                                                <h4 class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-6 ml-1">Data Management</h4>
                                                <div class="grid sm:grid-cols-2 gap-4">
                                                    <a href="{{ route('admin.settings.backup') }}" class="flex items-center justify-center gap-3 px-6 py-4 bg-slate-100 text-slate-700 rounded-2xl hover:bg-slate-200 transition-all text-sm font-black group">
                                                        <i class="ri-download-cloud-2-line text-lg group-hover:animate-bounce"></i>
                                                        SYSTEM BACKUP
                                                    </a>
                                                    <button type="button" @click="if(confirm('Reset all system settings to default?')) document.getElementById('reset-settings').submit()"
                                                            class="flex items-center justify-center gap-3 px-6 py-4 bg-red-50 text-red-600 rounded-2xl hover:bg-red-100 transition-all text-sm font-black">
                                                        <i class="ri-refresh-line text-lg"></i>
                                                        RESET DEFAULTS
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="pt-8 flex justify-end">
                                                <button type="submit" class="px-10 py-4 bg-slate-900 text-white rounded-2xl font-black text-sm hover:bg-indigo-600 transition-all duration-300">
                                                    SAVE SYSTEM FLAGS
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <form id="reset-settings" action="{{ route('admin.settings.reset') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #E2E8F0;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #CBD5E1;
        }
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
@endpush
