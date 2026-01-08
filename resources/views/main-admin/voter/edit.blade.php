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

        <main class="flex-1 p-4 lg:p-8">
            <div class="max-w-5xl mx-auto space-y-8">

                <!-- Profile Header Preview -->
                <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-200/40 overflow-hidden">
                    <div class="h-20 bg-gradient-to-r from-indigo-600 via-blue-500 to-indigo-600"></div>
                    <div class="px-6 pb-6">
                        <div class="relative flex items-end gap-5 -mt-8">
                            <div class="w-20 h-20 bg-white rounded-2xl p-1 shadow-xl">
                                <div class="w-full h-full bg-slate-100 rounded-xl flex items-center justify-center text-slate-400">
                                    <i class="ri-user-edit-line text-3xl"></i>
                                </div>
                            </div>
                            <div class="flex-1 mb-1.5">
                                <h1 class="text-2xl font-black text-slate-900 uppercase tracking-tight">{{ $voter->name }}</h1>
                                <p class="text-[10px] text-slate-500 font-bold tracking-widest uppercase mt-0.5">Ref ID: {{ $voter->id }}</p>
                            </div>
                            <div class="flex items-center gap-3 mb-1.5">
                                <a href="{{ route('admin.voters.index') }}"
                                   class="px-6 py-3 bg-white text-slate-600 rounded-xl font-black text-[10px] tracking-widest hover:bg-slate-50 transition-all border border-slate-200 uppercase">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.voters.update', $voter->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="bg-white rounded-3xl border border-slate-200/60 shadow-2xl shadow-slate-200/50 overflow-hidden">
                        <div class="px-8 py-5 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center text-indigo-600 shadow-sm">
                                    <i class="ri-information-line text-lg"></i>
                                </div>
                                <h3 class="text-base font-black text-slate-800 uppercase tracking-tight">Voter Information</h3>
                            </div>
                            <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full text-[9px] font-black uppercase tracking-widest border border-indigo-100">Profile Details</span>
                        </div>

                        <div class="p-8 space-y-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Full Name</label>
                                    <div class="relative group">
                                        <i class="ri-user-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors"></i>
                                        <input type="text" name="name" value="{{ old('name', $voter->name) }}"
                                               class="w-full bg-slate-50 border-none rounded-xl px-12 py-3 text-xs font-bold text-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all shadow-sm @error('name') ring-2 ring-rose-500 @enderror">
                                    </div>
                                    @error('name') <p class="text-rose-500 text-[9px] font-bold uppercase tracking-wider mt-1 ml-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Email Address</label>
                                    <div class="relative group">
                                        <i class="ri-mail-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors"></i>
                                        <input type="email" name="email" value="{{ old('email', $voter->email) }}"
                                               class="w-full bg-slate-50 border-none rounded-xl px-12 py-3 text-xs font-bold text-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all shadow-sm @error('email') ring-2 ring-rose-500 @enderror">
                                    </div>
                                    @error('email') <p class="text-rose-500 text-[9px] font-bold uppercase tracking-wider mt-1 ml-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Student/Employee ID</label>
                                    <div class="relative group">
                                        <i class="ri-id-card-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors"></i>
                                        <input type="text" name="student_id" value="{{ old('student_id', $voter->student_id) }}"
                                               class="w-full bg-slate-50 border-none rounded-xl px-12 py-3 text-xs font-bold text-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all shadow-sm @error('student_id') ring-2 ring-rose-500 @enderror">
                                    </div>
                                    @error('student_id') <p class="text-rose-500 text-[9px] font-bold uppercase tracking-wider mt-1 ml-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Phone Number</label>
                                    <div class="relative group">
                                        <i class="ri-phone-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors"></i>
                                        <input type="text" name="phone" value="{{ old('phone', $voter->phone) }}"
                                               class="w-full bg-slate-50 border-none rounded-xl px-12 py-3 text-xs font-bold text-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all shadow-sm @error('phone') ring-2 ring-rose-500 @enderror">
                                    </div>
                                    @error('phone') <p class="text-rose-500 text-[9px] font-bold uppercase tracking-wider mt-1 ml-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Assigned Election</label>
                                    <div class="relative group">
                                        <i class="ri-qr-code-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors z-10"></i>
                                        <select name="election_id" class="w-full bg-slate-50 border-none rounded-xl px-12 py-3 text-xs font-bold text-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all shadow-sm appearance-none @error('election_id') ring-2 ring-rose-500 @enderror">
                                            @foreach($forms as $form)
                                                <option value="{{ $form->id }}" {{ old('election_id', $voter->election_id) == $form->id ? 'selected' : '' }}>
                                                    {{ $form->title ?? $form->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <i class="ri-arrow-down-s-line absolute right-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                    </div>
                                    @error('election_id') <p class="text-rose-500 text-[9px] font-bold uppercase tracking-wider mt-1 ml-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Registration Status</label>
                                    <div class="relative group">
                                        <i class="ri-checkbox-circle-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors z-10"></i>
                                        <select name="registration_status" class="w-full bg-slate-50 border-none rounded-xl px-12 py-3 text-xs font-bold text-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all shadow-sm appearance-none @error('registration_status') ring-2 ring-rose-500 @enderror">
                                            <option value="approved" {{ old('registration_status', $voter->registration_status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                            <option value="pending" {{ old('registration_status', $voter->registration_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="declined" {{ old('registration_status', $voter->registration_status) == 'declined' ? 'selected' : '' }}>Declined</option>
                                        </select>
                                        <i class="ri-arrow-down-s-line absolute right-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                    </div>
                                    @error('registration_status') <p class="text-rose-500 text-[9px] font-bold uppercase tracking-wider mt-1 ml-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between">
                            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Ensure all data is accurate before saving.</p>
                            <button type="submit" class="px-10 py-3 bg-slate-900 text-white rounded-xl font-black text-[10px] tracking-widest hover:bg-indigo-600 hover:shadow-2xl hover:shadow-indigo-200 transition-all uppercase">
                                Update Profile
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>
@endsection
