@extends('layouts.app-main-admin')

@section('content')
    <div class="min-h-screen bg-slate-50 flex flex-col">
        <x-admin-header title="Edit Voter" />

        <!-- Mobile Header -->
        <header class="lg:hidden bg-white shadow-sm border-b px-4 py-3 flex items-center justify-between">
            <button @click="collapsed = false"
                    class="p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                <i class="ri-menu-fold-line text-lg rotate-180"></i>
            </button>
            <h1 class="text-lg font-semibold text-slate-800">Edit Voter</h1>
            <div class="w-10"></div>
        </header>

        <main class="flex-1 p-8 lg:p-12">
            <div class="max-w-5xl mx-auto">
                <!-- Top Actions Bar -->
                <div class="mb-12 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-center gap-6">
                        <div class="w-16 h-16 bg-gradient-to-br from-indigo-600 to-blue-600 rounded-[2rem] flex items-center justify-center shadow-2xl shadow-indigo-200">
                            <i class="ri-user-edit-fill text-white text-3xl"></i>
                        </div>
                        <div>
                            <h1 class="text-4xl font-black text-slate-900 uppercase tracking-tight">Edit Voter Profile</h1>
                            <p class="text-sm text-slate-500 font-bold tracking-widest uppercase mt-1">Ref ID: {{ $voter->id }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('admin.voters.index') }}"
                           class="px-8 py-4 bg-white text-slate-600 rounded-2xl font-black text-xs tracking-widest hover:bg-slate-50 transition-all border border-slate-200 uppercase">
                            Cancel
                        </a>
                    </div>
                </div>

                <form action="{{ route('admin.voters.update', $voter->id) }}" method="POST" class="space-y-8">
                    @csrf
                    @method('PUT')

                    <div class="bg-white rounded-[3rem] border border-slate-200/60 shadow-2xl shadow-slate-200/50 overflow-hidden">
                        <div class="px-10 py-8 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-indigo-600 shadow-sm">
                                    <i class="ri-information-line text-xl"></i>
                                </div>
                                <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Voter Information</h3>
                            </div>
                            <span class="px-4 py-1.5 bg-indigo-50 text-indigo-600 rounded-full text-[10px] font-black uppercase tracking-widest">General Details</span>
                        </div>

                        <div class="p-10 space-y-10">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                <div class="space-y-2">
                                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">Full Name</label>
                                    <div class="relative group">
                                        <i class="ri-user-line absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors"></i>
                                        <input type="text" name="name" value="{{ old('name', $voter->name) }}"
                                               class="w-full bg-slate-50 border-none rounded-2xl px-14 py-4 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all shadow-sm @error('name') ring-2 ring-rose-500 @enderror">
                                    </div>
                                    @error('name') <p class="text-rose-500 text-[10px] font-bold uppercase tracking-wider mt-1 ml-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">Email Address</label>
                                    <div class="relative group">
                                        <i class="ri-mail-line absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors"></i>
                                        <input type="email" name="email" value="{{ old('email', $voter->email) }}"
                                               class="w-full bg-slate-50 border-none rounded-2xl px-14 py-4 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all shadow-sm @error('email') ring-2 ring-rose-500 @enderror">
                                    </div>
                                    @error('email') <p class="text-rose-500 text-[10px] font-bold uppercase tracking-wider mt-1 ml-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                <div class="space-y-2">
                                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">Student/Employee ID</label>
                                    <div class="relative group">
                                        <i class="ri-id-card-line absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors"></i>
                                        <input type="text" name="student_id" value="{{ old('student_id', $voter->student_id) }}"
                                               class="w-full bg-slate-50 border-none rounded-2xl px-14 py-4 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all shadow-sm @error('student_id') ring-2 ring-rose-500 @enderror">
                                    </div>
                                    @error('student_id') <p class="text-rose-500 text-[10px] font-bold uppercase tracking-wider mt-1 ml-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">Phone Number</label>
                                    <div class="relative group">
                                        <i class="ri-phone-line absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors"></i>
                                        <input type="text" name="phone" value="{{ old('phone', $voter->phone) }}"
                                               class="w-full bg-slate-50 border-none rounded-2xl px-14 py-4 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all shadow-sm @error('phone') ring-2 ring-rose-500 @enderror">
                                    </div>
                                    @error('phone') <p class="text-rose-500 text-[10px] font-bold uppercase tracking-wider mt-1 ml-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                <div class="space-y-2">
                                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">Assigned Election</label>
                                    <div class="relative group">
                                        <i class="ri-qr-code-line absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors z-10"></i>
                                        <select name="election_id" class="w-full bg-slate-50 border-none rounded-2xl px-14 py-4 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all shadow-sm appearance-none @error('election_id') ring-2 ring-rose-500 @enderror">
                                            @foreach($forms as $form)
                                                <option value="{{ $form->id }}" {{ old('election_id', $voter->election_id) == $form->id ? 'selected' : '' }}>
                                                    {{ $form->title ?? $form->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <i class="ri-arrow-down-s-line absolute right-5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                    </div>
                                    @error('election_id') <p class="text-rose-500 text-[10px] font-bold uppercase tracking-wider mt-1 ml-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">Registration Status</label>
                                    <div class="relative group">
                                        <i class="ri-checkbox-circle-line absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors z-10"></i>
                                        <select name="registration_status" class="w-full bg-slate-50 border-none rounded-2xl px-14 py-4 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all shadow-sm appearance-none @error('registration_status') ring-2 ring-rose-500 @enderror">
                                            <option value="approved" {{ old('registration_status', $voter->registration_status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                            <option value="pending" {{ old('registration_status', $voter->registration_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="declined" {{ old('registration_status', $voter->registration_status) == 'declined' ? 'selected' : '' }}>Declined</option>
                                        </select>
                                        <i class="ri-arrow-down-s-line absolute right-5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                    </div>
                                    @error('registration_status') <p class="text-rose-500 text-[10px] font-bold uppercase tracking-wider mt-1 ml-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="px-10 py-8 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between">
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Ensure all data is accurate before saving.</p>
                            <button type="submit" class="px-12 py-4 bg-slate-900 text-white rounded-2xl font-black text-xs tracking-widest hover:bg-indigo-600 hover:shadow-2xl hover:shadow-indigo-200 transition-all uppercase">
                                Update Voter Profile
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>
@endsection
