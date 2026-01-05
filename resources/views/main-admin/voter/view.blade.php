@extends('layouts.app-main-admin')

@section('content')
    <div class="min-h-screen bg-gray-50 flex flex-col">
        <x-admin-header title="Voter Details" />

        <main class="flex-1 p-6">
            <div class="max-w-4xl mx-auto">
                <div class="mb-6 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-200">
                            <i class="ri-user-3-line text-white text-3xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-slate-900">{{ $voter->name }}</h1>
                            <p class="text-slate-500">Voter ID: {{ $voter->id }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.voters.index') }}"
                           class="px-6 py-3 bg-white border border-slate-200 text-slate-700 rounded-xl font-bold hover:bg-slate-50 transition-all flex items-center gap-2">
                            <i class="ri-arrow-left-line"></i>
                            Back to Directory
                        </a>
                        <a href="{{ route('admin.voters.edit', $voter->id) }}"
                           class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all flex items-center gap-2">
                            <i class="ri-edit-line"></i>
                            Edit Voter
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2 space-y-6">
                        <!-- Basic Information -->
                        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                            <div class="px-8 py-6 bg-slate-50 border-b border-slate-200">
                                <h3 class="text-lg font-bold text-slate-800">Basic Information</h3>
                            </div>
                            <div class="p-8 grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Full Name</label>
                                    <p class="text-slate-900 font-medium">{{ $voter->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Email Address</label>
                                    <p class="text-slate-900 font-medium">{{ $voter->email }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Phone Number</label>
                                    <p class="text-slate-900 font-medium">{{ $voter->phone ?? '—' }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Student/Employee ID</label>
                                    <p class="text-slate-900 font-medium">{{ $voter->student_id ?? '—' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Registration Details -->
                        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                            <div class="px-8 py-6 bg-slate-50 border-b border-slate-200">
                                <h3 class="text-lg font-bold text-slate-800">Registration Details</h3>
                            </div>
                            <div class="p-8 grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Registered In</label>
                                    <p class="text-slate-900 font-medium">{{ optional($voter->election)->title ?? '—' }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Registration Date</label>
                                    <p class="text-slate-900 font-medium">{{ $voter->created_at->format('M d, Y h:i A') }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Current Status</label>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $voter->registration_status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($voter->registration_status === 'declined' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">
                                        {{ ucfirst($voter->registration_status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <!-- Quick Stats -->
                        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-8 text-center">
                            <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <i class="ri-ballot-line text-3xl"></i>
                            </div>
                            <h4 class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-1">Votes Cast</h4>
                            <p class="text-3xl font-black text-slate-900">{{ \App\Models\Vote::where('voter_id', $voter->id)->count() }}</p>
                        </div>

                        <!-- Account Status -->
                        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-8">
                            <h4 class="text-slate-800 font-bold mb-4">Actions</h4>
                            <div class="space-y-3">
                                @if($voter->registration_status !== 'approved')
                                    <form method="POST" action="{{ route('admin.voters.approve', $voter->id) }}">
                                        @csrf
                                        <button type="submit" class="w-full py-3 bg-emerald-600 text-white rounded-xl font-bold hover:bg-emerald-700 transition-all flex items-center justify-center gap-2">
                                            <i class="ri-check-line"></i> Approve Voter
                                        </button>
                                    </form>
                                @endif
                                @if($voter->registration_status !== 'declined')
                                    <form method="POST" action="{{ route('admin.voters.decline', $voter->id) }}">
                                        @csrf
                                        <button type="submit" class="w-full py-3 bg-white border border-rose-200 text-rose-600 rounded-xl font-bold hover:bg-rose-50 transition-all flex items-center justify-center gap-2">
                                            <i class="ri-close-line"></i> Decline Voter
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection
