@extends('layouts.app-main-admin')

@section('content')
    <div x-data="{
        activeTab: 'account',
        showToast: @if(session('success') || $errors->any()) true @else false @endif,
        toastMessage: '@if(session('success')){{ session('success') }}@elseif($errors->any()){{ $errors->first() }}@endif',
        toastType: '@if(session('success'))success @else error @endif',
        isEditing: false,
        showRecoveryCodes: false,
        recoveryCodes: [],
        passwordForCodes: '',
        isReconfirming: false,
        securityPrefs: {
            notify_unrecognized_device: {{ (auth()->user()->security_preferences['notify_unrecognized_device'] ?? false) ? 'true' : 'false' }},
            notify_failed_login: {{ (auth()->user()->security_preferences['notify_failed_login'] ?? false) ? 'true' : 'false' }},
            notify_sensitive_action: {{ (auth()->user()->security_preferences['notify_sensitive_action'] ?? false) ? 'true' : 'false' }}
        }
    }" x-init="
        $watch('securityPrefs', value => {
            fetch('{{ route('admin.settings.security-preferences.update') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(value)
            });
        }, { deep: true })
    " class="flex min-h-screen bg-[#F8FAFC]">

        <x-admin-sidebar />

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Header -->
            <header class="bg-white border-b border-slate-200 sticky top-0 z-20">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="lg:hidden flex-shrink-0">
                                <button @click="collapsed = false" class="p-2 -ml-2 rounded-lg text-slate-600 hover:bg-slate-100 transition-colors">
                                    <i class="ri-menu-2-fill text-xl"></i>
                                </button>
                            </div>
                            <div class="truncate">
                                <h1 class="text-lg font-bold text-slate-900 truncate">Settings</h1>
                                <p class="text-[11px] text-slate-500 font-medium hidden sm:block">Platform preferences & security</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 flex-shrink-0">
                            <div class="hidden xs:flex items-center px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-full text-[10px] font-bold uppercase tracking-wider border border-indigo-100">
                                <span class="relative flex h-1.5 w-1.5 mr-1.5">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-indigo-500"></span>
                                </span>
                                <span class="hidden sm:inline">Admin Mode</span>
                                <span class="sm:hidden">Admin</span>
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
                            <nav class="flex lg:flex-col overflow-x-auto lg:overflow-visible sticky top-16 lg:top-24 space-y-0 lg:space-y-1 gap-2 lg:gap-0 pb-4 lg:pb-0 no-scrollbar -mx-4 px-4 sm:mx-0 sm:px-0 bg-[#F8FAFC] lg:bg-transparent z-10">
                                <button @click="activeTab = 'account'"
                                        :class="activeTab === 'account' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 hover:bg-white hover:text-indigo-600'"
                                        class="flex-shrink-0 w-auto lg:w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 group">
                                    <div :class="activeTab === 'account' ? 'bg-white/20' : 'bg-slate-100 group-hover:bg-indigo-50'" class="w-9 h-9 rounded-lg flex items-center justify-center transition-colors">
                                        <i class="ri-user-settings-line text-base"></i>
                                    </div>
                                    <div class="text-left">
                                        <p class="leading-none text-xs">Account</p>
                                        <p :class="activeTab === 'account' ? 'text-indigo-100' : 'text-slate-400'" class="text-[10px] mt-0.5 font-medium hidden sm:block">Personal Info</p>
                                    </div>
                                </button>

                                <button @click="activeTab = 'security'"
                                        :class="activeTab === 'security' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 hover:bg-white hover:text-indigo-600'"
                                        class="flex-shrink-0 w-auto lg:w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 group">
                                    <div :class="activeTab === 'security' ? 'bg-white/20' : 'bg-slate-100 group-hover:bg-indigo-50'" class="w-9 h-9 rounded-lg flex items-center justify-center transition-colors">
                                        <i class="ri-shield-keyhole-line text-base"></i>
                                    </div>
                                    <div class="text-left">
                                        <p class="leading-none text-xs">Security</p>
                                        <p :class="activeTab === 'security' ? 'text-indigo-100' : 'text-slate-400'" class="text-[10px] mt-0.5 font-medium hidden sm:block">Protection</p>
                                    </div>
                                </button>

                                <button @click="activeTab = 'notifications'"
                                        :class="activeTab === 'notifications' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 hover:bg-white hover:text-indigo-600'"
                                        class="flex-shrink-0 w-auto lg:w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 group">
                                    <div :class="activeTab === 'notifications' ? 'bg-white/20' : 'bg-slate-100 group-hover:bg-indigo-50'" class="w-9 h-9 rounded-lg flex items-center justify-center transition-colors">
                                        <i class="ri-notification-3-line text-base"></i>
                                    </div>
                                    <div class="text-left">
                                        <p class="leading-none text-xs">Communications</p>
                                        <p :class="activeTab === 'notifications' ? 'text-indigo-100' : 'text-slate-400'" class="text-[10px] mt-0.5 font-medium hidden sm:block">Alerts</p>
                                    </div>
                                </button>

                                <button @click="activeTab = 'privacy'"
                                        :class="activeTab === 'privacy' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 hover:bg-white hover:text-indigo-600'"
                                        class="flex-shrink-0 w-auto lg:w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 group">
                                    <div :class="activeTab === 'privacy' ? 'bg-white/20' : 'bg-slate-100 group-hover:bg-indigo-50'" class="w-9 h-9 rounded-lg flex items-center justify-center transition-colors">
                                        <i class="ri-lock-2-line text-base"></i>
                                    </div>
                                    <div class="text-left">
                                        <p class="leading-none text-xs">System</p>
                                        <p :class="activeTab === 'privacy' ? 'text-indigo-100' : 'text-slate-400'" class="text-[10px] mt-0.5 font-medium hidden sm:block">Configuration</p>
                                    </div>
                                </button>
                            </nav>

                            {{-- @if(session('success'))
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
                            @endif --}}
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
                                        <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                                            @csrf
                                            @method('PUT')

                                            <div class="flex items-center justify-between gap-4 mb-8">
                                                <div class="flex items-center gap-4">
                                                    <div class="relative group/avatar">
                                                        <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center text-white text-2xl font-bold shadow-md overflow-hidden">
                                                            @if(auth()->user()->profile_photo)
                                                                <img src="{{ auth()->user()->avatar_url }}" alt="Profile" class="w-full h-full object-cover">
                                                            @else
                                                                {{ substr(auth()->user()->name, 0, 1) }}
                                                            @endif
                                                        </div>
                                                        <label class="absolute -bottom-1 -right-1 w-7 h-7 bg-white rounded-lg shadow-sm border border-slate-100 flex items-center justify-center text-slate-600 hover:text-indigo-600 cursor-pointer transition-all hover:scale-110">
                                                            <i class="ri-camera-line text-sm"></i>
                                                            <input type="file" name="profile_photo" class="hidden" onchange="this.form.submit()">
                                                        </label>
                                                    </div>
                                                    <div>
                                                        <h3 class="text-lg font-bold text-slate-900">Personal Profile</h3>
                                                        <p class="text-xs text-slate-500 font-medium">Update account identity</p>
                                                    </div>
                                                </div>
                                                <div x-show="!isEditing">
                                                    <button type="button" @click="isEditing = true" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg text-xs font-bold hover:bg-slate-50 transition-all flex items-center gap-2">
                                                        <i class="ri-edit-line"></i>
                                                        EDIT PROFILE
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Identity Information -->
                                            <div class="space-y-6">
                                                <h4 class="text-[11px] font-bold text-indigo-600 uppercase tracking-widest flex items-center gap-2">
                                                    <span class="w-8 h-px bg-indigo-100"></span>
                                                    Identity Information
                                                </h4>

                                                <template x-if="!isEditing">
                                                    <div class="grid sm:grid-cols-2 gap-4">
                                                        <div class="space-y-1.5">
                                                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Full Name</label>
                                                            <div class="w-full bg-slate-50 border border-slate-100 rounded-xl px-5 py-3 text-sm font-bold text-slate-700">
                                                                {{ auth()->user()->first_name }} {{ auth()->user()->middle_name }} {{ auth()->user()->last_name }}
                                                                @if(!auth()->user()->first_name)
                                                                    {{ auth()->user()->name }}
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="space-y-1.5">
                                                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Username</label>
                                                            <div class="w-full bg-slate-50 border border-slate-100 rounded-xl px-5 py-3 text-sm font-bold text-slate-700">
                                                                {{ auth()->user()->username ?? 'Not set' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>

                                                <div x-show="isEditing" x-transition class="space-y-6">
                                                    <div class="grid sm:grid-cols-3 gap-4">
                                                        <div class="space-y-1.5">
                                                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">First Name</label>
                                                            <div class="relative group">
                                                                <i class="ri-user-3-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                                                <input type="text" name="first_name" value="{{ auth()->user()->first_name ?? explode(' ', auth()->user()->name)[0] }}"
                                                                       class="w-full bg-white ring-1 ring-slate-200 rounded-xl px-12 py-3 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500/20 transition-all border-none">
                                                            </div>
                                                        </div>
                                                        <div class="space-y-1.5">
                                                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Middle Name</label>
                                                            <div class="relative group">
                                                                <i class="ri-user-3-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                                                <input type="text" name="middle_name" value="{{ auth()->user()->middle_name }}"
                                                                       class="w-full bg-white ring-1 ring-slate-200 rounded-xl px-12 py-3 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500/20 transition-all border-none">
                                                            </div>
                                                        </div>
                                                        <div class="space-y-1.5">
                                                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Last Name</label>
                                                            <div class="relative group">
                                                                <i class="ri-user-3-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                                                @php
                                                                    $nameParts = explode(' ', auth()->user()->name);
                                                                    $lastNameDefault = count($nameParts) > 1 ? end($nameParts) : '';
                                                                @endphp
                                                                <input type="text" name="last_name" value="{{ auth()->user()->last_name ?? $lastNameDefault }}"
                                                                       class="w-full bg-white ring-1 ring-slate-200 rounded-xl px-12 py-3 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500/20 transition-all border-none">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="grid sm:grid-cols-2 gap-4">
                                                        <div class="space-y-1.5">
                                                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Username</label>
                                                            <div class="relative group">
                                                                <i class="ri-at-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                                                <input type="text" name="username" value="{{ auth()->user()->username }}"
                                                                       class="w-full bg-white ring-1 ring-slate-200 rounded-xl px-12 py-3 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500/20 transition-all border-none">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="grid sm:grid-cols-2 gap-4">
                                                    <div class="space-y-1.5">
                                                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Role / Account Type</label>
                                                        <div class="relative">
                                                            <i class="ri-shield-user-line absolute left-4 top-1/2 -translate-y-1/2 text-indigo-500"></i>
                                                            <div class="w-full bg-indigo-50/50 border border-indigo-100 rounded-xl px-12 py-3 text-sm font-bold text-indigo-700">
                                                                @if(auth()->user()->hasRole('super-admin'))
                                                                    Super Admin
                                                                @elseif(auth()->user()->hasRole('admin'))
                                                                    Admin
                                                                @elseif(auth()->user()->hasRole('voter'))
                                                                    Voter
                                                                @else
                                                                    {{ ucfirst(auth()->user()->role) }}
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="space-y-1.5">
                                                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Account Status</label>
                                                        <div class="relative">
                                                            <i class="ri-checkbox-circle-line absolute left-4 top-1/2 -translate-y-1/2 text-emerald-500"></i>
                                                            <div class="w-full bg-emerald-50/50 border border-emerald-100 rounded-xl px-12 py-3 text-sm font-bold text-emerald-700">
                                                                {{ auth()->user()->is_active ? 'Active' : 'Flagged/Inactive' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Contact Details -->
                                            <div class="space-y-6 pt-4">
                                                <h4 class="text-[11px] font-bold text-indigo-600 uppercase tracking-widest flex items-center gap-2">
                                                    <span class="w-8 h-px bg-indigo-100"></span>
                                                    Contact Details
                                                </h4>

                                                <div class="grid sm:grid-cols-2 gap-4">
                                                    <div class="space-y-1.5">
                                                        <div class="flex justify-between items-center px-1">
                                                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Email Address</label>
                                                            <a href="#" class="text-[10px] font-bold text-indigo-600 hover:underline">VERIFY</a>
                                                        </div>
                                                        <template x-if="!isEditing">
                                                            <div class="w-full bg-slate-50 border border-slate-100 rounded-xl px-5 py-3 text-sm font-bold text-slate-700">
                                                                {{ auth()->user()->email }}
                                                            </div>
                                                        </template>
                                                        <div x-show="isEditing" class="relative group">
                                                            <i class="ri-mail-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                                            <input type="email" name="email" value="{{ auth()->user()->email }}"
                                                                   class="w-full bg-white ring-1 ring-slate-200 rounded-xl px-12 py-3 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500/20 transition-all border-none">
                                                        </div>
                                                    </div>
                                                    <div class="space-y-1.5">
                                                        <div class="flex justify-between items-center px-1">
                                                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Phone Number</label>
                                                            <a href="#" class="text-[10px] font-bold text-indigo-600 hover:underline">VERIFY</a>
                                                        </div>
                                                        <template x-if="!isEditing">
                                                            <div class="w-full bg-slate-50 border border-slate-100 rounded-xl px-5 py-3 text-sm font-bold text-slate-700">
                                                                {{ auth()->user()->phone ?? 'Not set' }}
                                                            </div>
                                                        </template>
                                                        <div x-show="isEditing" class="relative group">
                                                            <i class="ri-phone-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                                            <input type="text" name="phone" value="{{ auth()->user()->phone }}"
                                                                   class="w-full bg-white ring-1 ring-slate-200 rounded-xl px-12 py-3 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500/20 transition-all border-none"
                                                                   placeholder="+63 9XX XXX XXXX">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Organization Info -->
                                            <div class="space-y-6 pt-4">
                                                <h4 class="text-[11px] font-bold text-indigo-600 uppercase tracking-widest flex items-center gap-2">
                                                    <span class="w-8 h-px bg-indigo-100"></span>
                                                    Organization Info
                                                </h4>

                                                <div class="grid sm:grid-cols-2 gap-4">
                                                    <div class="space-y-1.5">
                                                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Student/Employee ID</label>
                                                        <template x-if="!isEditing">
                                                            <div class="w-full bg-slate-50 border border-slate-100 rounded-xl px-5 py-3 text-sm font-bold text-slate-700">
                                                                {{ auth()->user()->student_id ?? 'Not set' }}
                                                            </div>
                                                        </template>
                                                        <div x-show="isEditing" class="relative group">
                                                            <i class="ri-id-card-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                                            <input type="text" name="student_id" value="{{ auth()->user()->student_id }}"
                                                                   class="w-full bg-white ring-1 ring-slate-200 rounded-xl px-12 py-3 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500/20 transition-all border-none"
                                                                   placeholder="2021-00123-MN-0">
                                                        </div>
                                                    </div>
                                                    <div class="space-y-1.5">
                                                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Department / Unit</label>
                                                        <template x-if="!isEditing">
                                                            <div class="w-full bg-slate-50 border border-slate-100 rounded-xl px-5 py-3 text-sm font-bold text-slate-700">
                                                                {{ auth()->user()->department ?? 'Not set' }}
                                                            </div>
                                                        </template>
                                                        <div x-show="isEditing" class="relative group">
                                                            <i class="ri-building-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                                            <input type="text" name="department" value="{{ auth()->user()->department }}"
                                                                   class="w-full bg-white ring-1 ring-slate-200 rounded-xl px-12 py-3 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500/20 transition-all border-none"
                                                                   placeholder="College of Computer Studies">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Regional Settings -->
                                            <div class="space-y-6 pt-4">
                                                <h4 class="text-[11px] font-bold text-indigo-600 uppercase tracking-widest flex items-center gap-2">
                                                    <span class="w-8 h-px bg-indigo-100"></span>
                                                    Regional Settings
                                                </h4>

                                                <div class="grid sm:grid-cols-2 gap-4">
                                                    <div class="space-y-1.5">
                                                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Timezone</label>
                                                        <template x-if="!isEditing">
                                                            <div class="w-full bg-slate-50 border border-slate-100 rounded-xl px-5 py-3 text-sm font-bold text-slate-700">
                                                                {{ auth()->user()->timezone ?? 'Asia/Manila (GMT+8)' }}
                                                            </div>
                                                        </template>
                                                        <div x-show="isEditing" class="relative group">
                                                            <i class="ri-time-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors z-10"></i>
                                                            <select name="timezone" class="w-full bg-white ring-1 ring-slate-200 rounded-xl px-12 py-3 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500/20 transition-all border-none appearance-none">
                                                                <option value="Asia/Manila" {{ auth()->user()->timezone == 'Asia/Manila' ? 'selected' : '' }}>Asia/Manila (GMT+8)</option>
                                                                <option value="UTC" {{ auth()->user()->timezone == 'UTC' ? 'selected' : '' }}>UTC (GMT+0)</option>
                                                                <option value="Asia/Singapore" {{ auth()->user()->timezone == 'Asia/Singapore' ? 'selected' : '' }}>Asia/Singapore (GMT+8)</option>
                                                            </select>
                                                            <i class="ri-arrow-down-s-line absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                                                        </div>
                                                    </div>
                                                    <div class="space-y-1.5">
                                                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Language / Locale</label>
                                                        <template x-if="!isEditing">
                                                            <div class="w-full bg-slate-50 border border-slate-100 rounded-xl px-5 py-3 text-sm font-bold text-slate-700">
                                                                {{ auth()->user()->language == 'en' ? 'English (US)' : (auth()->user()->language == 'fil' ? 'Filipino' : 'English (US)') }}
                                                            </div>
                                                        </template>
                                                        <div x-show="isEditing" class="relative group">
                                                            <i class="ri-global-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors z-10"></i>
                                                            <select name="language" class="w-full bg-white ring-1 ring-slate-200 rounded-xl px-12 py-3 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500/20 transition-all border-none appearance-none">
                                                                <option value="en" {{ auth()->user()->language == 'en' ? 'selected' : '' }}>English (US)</option>
                                                                <option value="fil" {{ auth()->user()->language == 'fil' ? 'selected' : '' }}>Filipino</option>
                                                            </select>
                                                            <i class="ri-arrow-down-s-line absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="grid sm:grid-cols-2 gap-4">
                                                    <div class="space-y-1.5">
                                                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Date Format preference</label>
                                                        <template x-if="!isEditing">
                                                            <div class="w-full bg-slate-50 border border-slate-100 rounded-xl px-5 py-3 text-sm font-bold text-slate-700">
                                                                {{ auth()->user()->date_format ?? 'Oct 24, 2025' }}
                                                            </div>
                                                        </template>
                                                        <div x-show="isEditing" class="relative group">
                                                            <i class="ri-calendar-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors z-10"></i>
                                                            <select name="date_format" class="w-full bg-white ring-1 ring-slate-200 rounded-xl px-12 py-3 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500/20 transition-all border-none appearance-none">
                                                                <option value="M d, Y" {{ auth()->user()->date_format == 'M d, Y' ? 'selected' : '' }}>Oct 24, 2025 (M d, Y)</option>
                                                                <option value="Y-m-d" {{ auth()->user()->date_format == 'Y-m-d' ? 'selected' : '' }}>2025-10-24 (Y-m-d)</option>
                                                                <option value="d/m/Y" {{ auth()->user()->date_format == 'd/m/Y' ? 'selected' : '' }}>24/10/2025 (d/m/Y)</option>
                                                            </select>
                                                            <i class="ri-arrow-down-s-line absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Security Metadata (Read-Only) -->
                                            <div class="space-y-6 pt-4">
                                                <h4 class="text-[11px] font-bold text-indigo-600 uppercase tracking-widest flex items-center gap-2">
                                                    <span class="w-8 h-px bg-indigo-100"></span>
                                                    Security Metadata
                                                </h4>

                                                <div class="grid sm:grid-cols-1 gap-4">
                                                    <div class="space-y-1.5">
                                                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Last Login</label>
                                                        <div class="w-full bg-slate-50 border border-slate-100 rounded-xl px-5 py-3 text-sm font-bold text-slate-700 flex items-center gap-3">
                                                            <i class="ri-history-line text-slate-400"></i>
                                                            <span>
                                                                @if(auth()->user()->last_login_at)
                                                                    {{ auth()->user()->last_login_at->format('M d, Y, h:i A') }} from {{ auth()->user()->last_login_ip ?? 'Unknown IP' }}
                                                                @else
                                                                    No login history recorded
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div x-show="isEditing" class="pt-6 border-t border-slate-100 flex justify-end gap-3"
                                                 x-transition:enter="transition ease-out duration-200"
                                                 x-transition:enter-start="opacity-0 translate-y-2">
                                                <button type="button" @click="isEditing = false" class="px-6 py-3 bg-slate-100 text-slate-600 rounded-xl font-bold text-xs hover:bg-slate-200 transition-all">
                                                    CANCEL
                                                </button>
                                                <button type="submit" class="px-8 py-3 bg-indigo-600 text-white rounded-xl font-bold text-xs hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all">
                                                    SAVE CHANGES
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

                                <!-- Active Sessions Manager -->
                                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
                                    <div class="p-6 sm:p-8">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                                            <div class="flex items-center gap-4">
                                                <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 text-lg shadow-sm">
                                                    <i class="ri-macbook-line"></i>
                                                </div>
                                                <div>
                                                    <h3 class="text-lg font-bold text-slate-900">Active Sessions</h3>
                                                    <p class="text-xs text-slate-500 font-medium">Manage your active sessions across devices</p>
                                                </div>
                                            </div>
                                            @if($sessions->count() > 1)
                                                <form action="{{ route('admin.settings.sessions.logout-others') }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-slate-100 text-slate-700 rounded-lg font-bold text-[10px] uppercase tracking-wider hover:bg-slate-200 transition-all">
                                                        Log Out Other Sessions
                                                    </button>
                                                </form>
                                            @endif
                                        </div>

                                        <div class="space-y-4">
                                            @foreach($sessions as $session)
                                                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-100">
                                                    <div class="flex items-center gap-4">
                                                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-slate-400">
                                                            @if($session->platform === 'Windows') <i class="ri-windows-fill text-xl"></i>
                                                            @elseif($session->platform === 'OS X') <i class="ri-apple-fill text-xl"></i>
                                                            @elseif($session->platform === 'Android') <i class="ri-android-fill text-xl"></i>
                                                            @elseif($session->platform === 'iOS') <i class="ri-smartphone-line text-xl"></i>
                                                            @else <i class="ri-device-line text-xl"></i> @endif
                                                        </div>
                                                        <div>
                                                            <div class="flex items-center gap-2">
                                                                <p class="text-sm font-bold text-slate-700">{{ $session->browser }} on {{ $session->platform }}</p>
                                                                @if($session->is_current_device)
                                                                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[8px] font-black uppercase tracking-wider rounded-md">This Device</span>
                                                                @endif
                                                            </div>
                                                            <p class="text-[10px] text-slate-500 font-medium uppercase tracking-tight">
                                                                {{ $session->ip_address }} • {{ $session->last_active->diffForHumans() }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    @if(!$session->is_current_device)
                                                        <form action="{{ route('admin.settings.sessions.logout', $session->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <input type="hidden" name="session_id" value="{{ $session->id }}">
                                                            <button type="submit" class="p-2 text-slate-400 hover:text-red-500 transition-colors">
                                                                <i class="ri-logout-box-r-line"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <!-- Login Activity Audit -->
                                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden mb-8">
                                    <div class="p-6 sm:p-8">
                                        <div class="flex items-center gap-4 mb-8">
                                            <div class="w-12 h-12 bg-slate-50 rounded-xl flex items-center justify-center text-slate-600 text-lg shadow-sm">
                                                <i class="ri-history-line"></i>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-slate-900">Session History</h3>
                                                <p class="text-xs text-slate-500 font-medium">Previous and current login sessions</p>
                                            </div>
                                        </div>

                                        <div class="space-y-4">
                                            @php
                                                $currentSessionId = session()->getId();
                                                $sortedSessions = collect($sessions)->sortByDesc('last_activity');
                                            @endphp
                                            @foreach($sortedSessions as $session)
                                                <div class="flex items-center justify-between p-4 {{ $session->id === $currentSessionId ? 'bg-indigo-50 border-indigo-100' : 'bg-slate-50 border-slate-100' }} rounded-xl border transition-all">
                                                    <div class="flex items-center gap-4">
                                                        <div class="w-10 h-10 {{ $session->id === $currentSessionId ? 'bg-indigo-100 text-indigo-600' : 'bg-white text-slate-400' }} rounded-lg flex items-center justify-center shadow-sm">
                                                            <i class="ri-computer-line text-lg"></i>
                                                        </div>
                                                        <div>
                                                            <div class="flex items-center gap-2">
                                                                <span class="text-sm font-bold text-slate-900">{{ $session->ip_address }}</span>
                                                                @if($session->id === $currentSessionId)
                                                                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded text-[8px] font-black uppercase tracking-widest">Current Session</span>
                                                                @endif
                                                            </div>
                                                            <p class="text-[10px] text-slate-500 font-medium truncate max-w-[200px] sm:max-w-xs">{{ $session->user_agent }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="text-right">
                                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Last Activity</p>
                                                        <p class="text-xs font-bold text-slate-700">{{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans() }}</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <!-- Login Activity Audit -->
                                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
                                    <div class="p-6 sm:p-8">
                                        <div class="flex items-center gap-4 mb-8">
                                            <div class="w-12 h-12 bg-slate-50 rounded-xl flex items-center justify-center text-slate-600 text-lg shadow-sm">
                                                <i class="ri-history-line"></i>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-slate-900">Login Activity</h3>
                                                <p class="text-xs text-slate-500 font-medium">Recent authentication attempts on your account</p>
                                            </div>
                                        </div>

                                        <div class="overflow-x-auto">
                                            <table class="w-full text-left border-collapse">
                                                <thead>
                                                    <tr class="border-b border-slate-100">
                                                        <th class="pb-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Status</th>
                                                        <th class="pb-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">IP Address</th>
                                                        <th class="pb-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Timestamp</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-slate-50">
                                                    @foreach($loginActivity as $activity)
                                                        <tr>
                                                            <td class="py-4">
                                                                <div class="flex items-center gap-2">
                                                                    <div class="w-2 h-2 rounded-full {{ $activity->status === 'Success' ? 'bg-emerald-500' : 'bg-red-500' }}"></div>
                                                                    <span class="text-xs font-bold {{ $activity->status === 'Success' ? 'text-emerald-700' : 'text-red-700' }}">
                                                                        {{ $activity->status }}
                                                                    </span>
                                                                </div>
                                                            </td>
                                                            <td class="py-4">
                                                                <span class="text-xs font-bold text-slate-600">{{ $activity->ip_address }}</span>
                                                            </td>
                                                            <td class="py-4 text-right">
                                                                <span class="text-xs font-medium text-slate-500">{{ \Carbon\Carbon::parse($activity->timestamp)->format('M d, Y H:i') }}</span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                @if(auth()->user()->hasRole(\App\Models\User::ROLE_SUPER_ADMIN))
                                <!-- 2FA Recovery Codes -->
                                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
                                    <div class="p-6 sm:p-8">
                                        <div class="flex items-center gap-4 mb-8">
                                            <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600 text-lg shadow-sm">
                                                <i class="ri-shield-user-line"></i>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-slate-900">Recovery Codes</h3>
                                                <p class="text-xs text-slate-500 font-medium">Backup access for 2nd-factor authentication</p>
                                            </div>
                                        </div>

                                        <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100">
                                            <div x-show="!showRecoveryCodes" class="text-center">
                                                <p class="text-xs text-slate-600 font-medium mb-4">Security codes are hidden to protect your account.</p>
                                                <button @click="isReconfirming = true" class="px-6 py-2 bg-slate-900 text-white rounded-lg font-bold text-[10px] uppercase tracking-wider hover:bg-slate-800 transition-all">
                                                    Show Recovery Codes
                                                </button>
                                            </div>

                                            <div x-show="showRecoveryCodes" x-cloak>
                                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                                                    <template x-for="code in recoveryCodes" :key="code">
                                                        <div class="p-2 bg-white rounded-lg border border-slate-200 text-center font-mono text-sm font-bold text-slate-700" x-text="code"></div>
                                                    </template>
                                                </div>
                                                <div class="flex justify-center gap-3">
                                                    <button @click="
                                                        if(confirm('Generating new codes will invalidate your old ones. Proceed?')) {
                                                            fetch('{{ route('admin.settings.recovery-codes.generate') }}', {
                                                                method: 'POST',
                                                                headers: {
                                                                    'Content-Type': 'application/json',
                                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                                }
                                                            }).then(r => r.json()).then(d => {
                                                                recoveryCodes = d.codes;
                                                                showToast = true;
                                                                toastMessage = 'New codes generated!';
                                                            });
                                                        }
                                                    " class="px-4 py-2 bg-indigo-50 text-indigo-700 rounded-lg font-bold text-[10px] uppercase tracking-wider hover:bg-indigo-100 transition-all">
                                                        Generate New Codes
                                                    </button>
                                                    <button @click="showRecoveryCodes = false" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 rounded-lg font-bold text-[10px] uppercase tracking-wider hover:bg-slate-50 transition-all">
                                                        Hide Codes
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Security Notification Preferences -->
                                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
                                    <div class="p-6 sm:p-8">
                                        <div class="flex items-center gap-4 mb-8">
                                            <div class="w-12 h-12 bg-rose-50 rounded-xl flex items-center justify-center text-rose-600 text-lg shadow-sm">
                                                <i class="ri-notification-badge-line"></i>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-slate-900">Security Notifications</h3>
                                                <p class="text-xs text-slate-500 font-medium">Get alerted about critical account activity</p>
                                            </div>
                                        </div>

                                        <div class="space-y-4">
                                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100 transition-all hover:bg-white hover:border-indigo-100 group">
                                                <div>
                                                    <p class="text-xs font-black text-slate-900 uppercase tracking-tight">Unrecognized Devices</p>
                                                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">Alert on sign-ins from new devices</p>
                                                </div>
                                                <label class="relative inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" x-model="securityPrefs.notify_unrecognized_device" class="sr-only peer">
                                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                                </label>
                                            </div>

                                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100 transition-all hover:bg-white hover:border-indigo-100 group">
                                                <div>
                                                    <p class="text-xs font-black text-slate-900 uppercase tracking-tight">Failed Attempts</p>
                                                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">Alert after unsuccessful logins</p>
                                                </div>
                                                <label class="relative inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" x-model="securityPrefs.notify_failed_login" class="sr-only peer">
                                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                                </label>
                                            </div>

                                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100 transition-all hover:bg-white hover:border-indigo-100 group">
                                                <div>
                                                    <p class="text-xs font-black text-slate-900 uppercase tracking-tight">Sensitive Actions</p>
                                                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">Alert on system backups & critical changes</p>
                                                </div>
                                                <label class="relative inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" x-model="securityPrefs.notify_sensitive_action" class="sr-only peer">
                                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
                                    <div class="p-6 sm:p-8">
                                        <div class="flex items-center gap-4 mb-8">
                                            <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 text-lg shadow-sm">
                                                <i class="ri-fingerprint-line"></i>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-slate-900">Biometrics Security</h3>
                                                <p class="text-xs text-slate-500 font-medium">Advanced identity verification methods</p>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <!-- ID Verification -->
                                            <div class="p-5 bg-slate-50 rounded-[1.5rem] border-2 border-transparent relative group opacity-75">
                                                <div class="flex items-center justify-between mb-4">
                                                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-slate-400 shadow-sm">
                                                        <i class="ri-file-user-line text-lg"></i>
                                                    </div>
                                                    <span class="px-2 py-1 bg-amber-100 text-amber-700 text-[9px] font-black uppercase tracking-wider rounded-md">Not Available Yet</span>
                                                </div>
                                                <div>
                                                    <span class="block text-xs font-black text-slate-900 uppercase tracking-tight">ID Verification</span>
                                                    <span class="block text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-1">AI-Powered Identity Check</span>
                                                </div>
                                                <div class="mt-4 flex items-center justify-end">
                                                    <div class="relative inline-block w-12 h-6 transition duration-200 ease-in-out flex-shrink-0 cursor-not-allowed">
                                                        <input type="checkbox" disabled class="peer opacity-0 w-0 h-0">
                                                        <span class="absolute top-0 left-0 right-0 bottom-0 bg-slate-200 transition-all duration-300 rounded-full before:absolute before:content-[''] before:h-4 before:w-4 before:left-1 before:bottom-1 before:bg-white before:transition-all before:duration-300 before:rounded-full"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Facial Recognition -->
                                            <div class="p-5 bg-slate-50 rounded-[1.5rem] border-2 border-transparent relative group opacity-75">
                                                <div class="flex items-center justify-between mb-4">
                                                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-slate-400 shadow-sm">
                                                        <i class="ri-scan-face-line text-lg"></i>
                                                    </div>
                                                    <span class="px-2 py-1 bg-amber-100 text-amber-700 text-[9px] font-black uppercase tracking-wider rounded-md">Not Available Yet</span>
                                                </div>
                                                <div>
                                                    <span class="block text-xs font-black text-slate-900 uppercase tracking-tight">Facial Recognition</span>
                                                    <span class="block text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-1">Biometric Authentication</span>
                                                </div>
                                                <div class="mt-4 flex items-center justify-end">
                                                    <div class="relative inline-block w-12 h-6 transition duration-200 ease-in-out flex-shrink-0 cursor-not-allowed">
                                                        <input type="checkbox" disabled class="peer opacity-0 w-0 h-0">
                                                        <span class="absolute top-0 left-0 right-0 bottom-0 bg-slate-200 transition-all duration-300 rounded-full before:absolute before:content-[''] before:h-4 before:w-4 before:left-1 before:bottom-1 before:bg-white before:transition-all before:duration-300 before:rounded-full"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-6 flex items-center gap-3 p-4 bg-indigo-50/50 rounded-xl border border-indigo-100/50">
                                            <i class="ri-information-line text-indigo-600 text-lg"></i>
                                            <p class="text-[10px] text-indigo-700 font-bold uppercase tracking-widest leading-relaxed">
                                                These security features are currently in development and will be released in a future update.
                                            </p>
                                        </div>
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
                                                    <label class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-4 sm:p-6 bg-slate-50 rounded-2xl sm:rounded-[2rem] cursor-pointer hover:bg-slate-100/80 transition-all border border-transparent hover:border-slate-200 group gap-4">
                                                        <div class="flex items-center gap-4">
                                                            <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-indigo-600 shadow-sm group-hover:scale-110 transition-transform">
                                                                <i class="ri-mail-send-line text-xl"></i>
                                                            </div>
                                                            <div>
                                                                <span class="block text-sm font-black text-slate-800 uppercase tracking-tight">Email Notifications</span>
                                                                <span class="block text-xs text-slate-500 font-medium">System alerts & receipts</span>
                                                            </div>
                                                        </div>
                                                        <div class="relative inline-flex items-center cursor-pointer ml-auto sm:ml-0">
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
                                                    <button type="submit" class="w-full sm:w-auto px-10 py-4 bg-slate-900 text-white rounded-2xl font-black text-sm hover:bg-indigo-600 transition-all duration-300">
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
                                                <label class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-4 sm:p-6 bg-slate-50 rounded-2xl sm:rounded-[2rem] cursor-pointer hover:bg-slate-100/80 transition-all border border-transparent hover:border-slate-200 group gap-4">
                                                    <div class="flex items-center gap-4">
                                                        <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-indigo-600 shadow-sm group-hover:scale-110 transition-transform">
                                                            <i class="ri-bar-chart-2-line text-xl"></i>
                                                        </div>
                                                        <div>
                                                            <span class="block text-sm font-black text-slate-800 uppercase tracking-tight">Public Results</span>
                                                            <span class="block text-xs text-slate-500 font-medium">Analytics visible to everyone</span>
                                                        </div>
                                                    </div>
                                                    <div class="relative inline-flex items-center cursor-pointer ml-auto sm:ml-0">
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
                                                <button type="submit" class="w-full sm:w-auto px-10 py-4 bg-slate-900 text-white rounded-2xl font-black text-sm hover:bg-indigo-600 transition-all duration-300">
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

        <!-- Re-confirmation Modal for Recovery Codes -->
        <div x-show="isReconfirming"
             x-cloak
             class="fixed inset-0 z-[100] overflow-y-auto"
             aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="isReconfirming"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="isReconfirming = false"
                     class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="isReconfirming"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-middle bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full border border-slate-200">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-xl bg-indigo-50 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="ri-lock-2-line text-indigo-600 text-xl"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-bold text-slate-900" id="modal-title">Confirm Password</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-slate-500">Please enter your password to view your recovery codes. This is an extra security step.</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <input type="password" x-model="passwordForCodes" placeholder="Enter your password"
                                   class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white transition-all">
                        </div>
                    </div>
                    <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                        <button type="button"
                                @click="
                                    fetch('{{ route('admin.settings.recovery-codes.show') }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        body: JSON.stringify({ password: passwordForCodes })
                                    }).then(r => r.json()).then(d => {
                                        if(d.success) {
                                            recoveryCodes = d.codes;
                                            showRecoveryCodes = true;
                                            isReconfirming = false;
                                            passwordForCodes = '';
                                        } else {
                                            alert(d.message);
                                        }
                                    })
                                "
                                class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-2 bg-indigo-600 text-xs font-bold text-white hover:bg-indigo-700 focus:outline-none sm:w-auto uppercase tracking-wider transition-all">
                            Verify
                        </button>
                        <button type="button"
                                @click="isReconfirming = false; passwordForCodes = ''"
                                class="mt-3 w-full inline-flex justify-center rounded-xl border border-slate-200 shadow-sm px-6 py-2 bg-white text-xs font-bold text-slate-700 hover:bg-slate-50 focus:outline-none sm:mt-0 sm:w-auto uppercase tracking-wider transition-all">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <form id="reset-settings" action="{{ route('admin.settings.reset') }}" method="POST" class="hidden">
            @csrf
        </form>

        <!-- Toast Notifications -->
        <div x-show="showToast"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-8"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-8"
             class="alert-toaster"
             x-cloak>
            <div :class="toastType === 'success' ? 'bg-emerald-50 border-emerald-100' : 'bg-red-50 border-red-100'"
                 class="p-4 rounded-2xl border shadow-xl flex items-start gap-4">
                <div :class="toastType === 'success' ? 'bg-emerald-500' : 'bg-red-500'"
                     class="w-10 h-10 rounded-xl flex items-center justify-center text-white flex-shrink-0">
                    <i :class="toastType === 'success' ? 'ri-checkbox-circle-line' : 'ri-error-warning-line'" class="text-xl"></i>
                </div>
                <div class="flex-1 pt-0.5">
                    <p :class="toastType === 'success' ? 'text-emerald-900' : 'text-red-900'"
                       class="text-sm font-bold" x-text="toastType === 'success' ? 'Success!' : 'Review Errors'"></p>
                    <p :class="toastType === 'success' ? 'text-emerald-600' : 'text-red-600'"
                       class="text-xs font-medium mt-1" x-text="toastMessage"></p>
                </div>
                <button @click="showToast = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <i class="ri-close-line text-lg"></i>
                </button>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        .alert-toaster {
            position: fixed;
            top: 2rem;
            right: 2rem;
            z-index: 1000;
            max-width: 24rem;
            width: calc(100% - 4rem);
        }
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
