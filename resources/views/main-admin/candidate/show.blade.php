@extends('layouts.app-main-admin')

@section('content')
<div class="min-h-screen bg-slate-50/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
                <li>
                    <div>
                        <a href="{{ route('admin.dashboard') }}" class="text-slate-400 hover:text-slate-500 transition-colors">
                            <i class="ri-home-4-line text-lg"></i>
                        </a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="ri-arrow-right-s-line text-slate-400 text-lg"></i>
                        <a href="{{ route('admin.candidates.index') }}" class="ml-4 text-sm font-medium text-slate-500 hover:text-slate-700 transition-colors">Candidates</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="ri-arrow-right-s-line text-slate-400 text-lg"></i>
                        <span class="ml-4 text-sm font-bold text-slate-900">Candidate Profile</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Profile Header -->
        <div class="relative mb-8">
            <div class="h-48 w-full bg-gradient-to-r from-indigo-600 to-blue-700 rounded-3xl shadow-lg"></div>
            <div class="absolute -bottom-6 left-8 flex items-end space-x-6">
                <div class="p-1.5 bg-white rounded-3xl shadow-xl">
                    <div class="w-32 h-32 rounded-2xl bg-slate-100 overflow-hidden border-2 border-slate-50">
                        @if($candidate->photo)
                            <img src="{{ asset('storage/' . $candidate->photo) }}" alt="{{ $candidate->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-slate-50">
                                <i class="ri-user-line text-5xl text-slate-300"></i>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="pb-8">
                    <h1 class="text-3xl font-bold text-white mb-1">{{ $candidate->name }}</h1>
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 bg-white/20 backdrop-blur-md border border-white/30 rounded-full text-xs font-bold text-white uppercase tracking-wider">
                            {{ $candidate->position ? $candidate->position->title : 'No Position' }}
                        </span>
                        @if($candidate->partylist)
                            <span class="px-3 py-1 bg-emerald-500/20 backdrop-blur-md border border-emerald-500/30 rounded-full text-xs font-bold text-emerald-100 uppercase tracking-wider">
                                {{ $candidate->partylist->name }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="absolute bottom-4 right-8 flex gap-3">
                <a href="{{ route('admin.candidates.edit', $candidate->id) }}" class="flex items-center gap-2 px-6 py-2.5 bg-white text-slate-900 rounded-xl font-bold text-sm shadow-lg hover:bg-slate-50 transition-all">
                    <i class="ri-edit-line"></i>
                    Edit Profile
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-12">
            <!-- Left Column: Details -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Platform Section -->
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600">
                            <i class="ri-file-list-3-line text-xl"></i>
                        </div>
                        <h2 class="text-xl font-bold text-slate-900">Platform & Vision</h2>
                    </div>
                    <div class="prose prose-slate max-w-none text-slate-600 leading-relaxed">
                        @if($candidate->platform)
                            {!! nl2br(e($candidate->platform)) !!}
                        @else
                            <p class="italic text-slate-400">No platform information provided.</p>
                        @endif
                    </div>
                </div>

                <!-- Election Info -->
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600">
                            <i class="ri-government-line text-xl"></i>
                        </div>
                        <h2 class="text-xl font-bold text-slate-900">Election Details</h2>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Current Election</p>
                            <p class="text-sm font-bold text-slate-900">{{ $candidate->election ? $candidate->election->title : 'Not Assigned' }}</p>
                        </div>
                        <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Organization</p>
                            <p class="text-sm font-bold text-slate-900">{{ $candidate->organization ? $candidate->organization->name : 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Sidebar Stats -->
            <div class="space-y-8">
                <!-- Status Card -->
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 p-8">
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-[0.2em] mb-6">Account Status</h3>
                    <div class="flex items-center justify-between p-4 rounded-2xl {{ $candidate->status === 'active' ? 'bg-emerald-50 border border-emerald-100' : 'bg-slate-50 border border-slate-100' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full {{ $candidate->status === 'active' ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></div>
                            <span class="text-sm font-bold {{ $candidate->status === 'active' ? 'text-emerald-700' : 'text-slate-600' }} uppercase">
                                {{ $candidate->status ?? 'Inactive' }}
                            </span>
                        </div>
                        <i class="ri-checkbox-circle-line text-xl {{ $candidate->status === 'active' ? 'text-emerald-500' : 'text-slate-300' }}"></i>
                    </div>
                </div>

                <!-- Quick Info -->
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 p-8">
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-[0.2em] mb-6">User Information</h3>
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-slate-500 flex-shrink-0">
                                <i class="ri-mail-line text-lg"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Email Address</p>
                                <p class="text-sm font-bold text-slate-700 truncate w-full">{{ $candidate->user ? $candidate->user->email : 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-slate-500 flex-shrink-0">
                                <i class="ri-calendar-event-line text-lg"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Joined At</p>
                                <p class="text-sm font-bold text-slate-700">{{ $candidate->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
