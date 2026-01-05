@extends('layouts.app-main-admin')

@section('content')
    <div class="min-h-screen bg-gray-50 flex flex-col">
        <x-admin-header title="Edit Voter" />

        <main class="flex-1 p-6">
            <div class="max-w-4xl mx-auto">
                <div class="mb-6 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-200">
                            <i class="ri-user-edit-line text-white text-3xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-slate-900">Edit Voter</h1>
                            <p class="text-slate-500">Update information for {{ $voter->name }}</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.voters.index') }}"
                       class="px-6 py-3 bg-white border border-slate-200 text-slate-700 rounded-xl font-bold hover:bg-slate-50 transition-all flex items-center gap-2">
                        <i class="ri-arrow-left-line"></i>
                        Cancel
                    </a>
                </div>

                <form action="{{ route('admin.voters.update', $voter->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-8 py-6 bg-slate-50 border-b border-slate-200">
                            <h3 class="text-lg font-bold text-slate-800">Voter Information</h3>
                        </div>
                        <div class="p-8 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Full Name</label>
                                    <input type="text" name="name" value="{{ old('name', $voter->name) }}"
                                           class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 transition-all px-4 py-3 bg-slate-50 focus:bg-white @error('name') border-rose-500 @enderror">
                                    @error('name') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Email Address</label>
                                    <input type="email" name="email" value="{{ old('email', $voter->email) }}"
                                           class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 transition-all px-4 py-3 bg-slate-50 focus:bg-white @error('email') border-rose-500 @enderror">
                                    @error('email') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Student/Employee ID</label>
                                    <input type="text" name="student_id" value="{{ old('student_id', $voter->student_id) }}"
                                           class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 transition-all px-4 py-3 bg-slate-50 focus:bg-white @error('student_id') border-rose-500 @enderror">
                                    @error('student_id') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Phone Number</label>
                                    <input type="text" name="phone" value="{{ old('phone', $voter->phone) }}"
                                           class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 transition-all px-4 py-3 bg-slate-50 focus:bg-white @error('phone') border-rose-500 @enderror">
                                    @error('phone') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Election Form</label>
                                    <select name="election_id" class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 transition-all px-4 py-3 bg-slate-50 focus:bg-white @error('election_id') border-rose-500 @enderror">
                                        @foreach($forms as $form)
                                            <option value="{{ $form->id }}" {{ old('election_id', $voter->election_id) == $form->id ? 'selected' : '' }}>
                                                {{ $form->title ?? $form->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('election_id') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Registration Status</label>
                                    <select name="registration_status" class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 transition-all px-4 py-3 bg-slate-50 focus:bg-white @error('registration_status') border-rose-500 @enderror">
                                        <option value="approved" {{ old('registration_status', $voter->registration_status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="pending" {{ old('registration_status', $voter->registration_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="declined" {{ old('registration_status', $voter->registration_status) == 'declined' ? 'selected' : '' }}>Declined</option>
                                    </select>
                                    @error('registration_status') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="px-8 py-6 bg-slate-50 border-t border-slate-200 flex justify-end">
                            <button type="submit" class="px-10 py-3 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all">
                                Update Voter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>
@endsection
