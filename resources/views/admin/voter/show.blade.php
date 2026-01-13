@extends('layouts.app-main-admin')

@section('content')
    <div class="flex-1 flex flex-col">
        <!-- Top bar -->
        <div class="bg-white/80 backdrop-blur-sm border-b sticky top-0 z-40 hidden md:block">
            <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-600 to-blue-500 flex items-center justify-center shadow flex-shrink-0">
                        <i class="ri-file-list-3-line text-white text-xl"></i>
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-2xl font-semibold leading-tight truncate">Import Preview</h1>
                        <p class="text-sm text-gray-500 hidden sm:block">Review the fetched data before saving to the database</p>
                    </div>
                </div>
                <div class="hidden lg:flex items-center gap-4 text-sm text-gray-600">
                    <div>
                        <span class="font-medium text-gray-900">Admin</span>
                        <i class="ri-arrow-right-s-line mx-2"></i>
                        <a href="{{ route('admin.voters.index') }}" class="hover:text-indigo-600">Voters</a>
                        <i class="ri-arrow-right-s-line mx-2"></i>
                        <span>Import Preview</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-6">
            <!-- Import Configuration Card -->
            <div class="bg-white rounded-xl border border-gray-200/60 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b bg-gradient-to-r from-white via-indigo-50/30 to-white">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm border border-slate-200">
                            <i class="ri-settings-4-line text-indigo-600"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-gray-900">Import Configuration</h2>
                            <p class="text-[11px] text-gray-500 font-medium">Assign election and set default values</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <form method="POST" action="{{ route('admin.voters.import.store') }}" id="finalImportForm">
                        @csrf
                        <input type="hidden" name="import_path" value="{{ $importPath }}" />

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Election Selection -->
                            <div class="space-y-2">
                                <label for="election_id" class="text-sm font-bold text-slate-700 uppercase tracking-wider">Target Election Form</label>
                                <div class="relative">
                                    <select name="election_id" id="election_id" required
                                            class="w-full border rounded-lg px-4 py-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-200 appearance-none">
                                        <option value="">-- Select Election Form --</option>
                                        @foreach($forms ?? [] as $form)
                                            <option value="{{ $form->id }}">{{ $form->title }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                                        <i class="ri-arrow-down-s-line text-slate-400"></i>
                                    </div>
                                </div>
                                <p class="text-[10px] text-slate-500 italic">Select the election where these voters will be assigned.</p>
                            </div>

                            <!-- Default Registration Status -->
                            <div class="space-y-2">
                                <label for="registration_status" class="text-sm font-bold text-slate-700 uppercase tracking-wider">Default Status</label>
                                <div class="relative">
                                    <select name="registration_status" id="registration_status" required
                                            class="w-full border rounded-lg px-4 py-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-200 appearance-none">
                                        <option value="approved">Approved</option>
                                        <option value="pending">Pending</option>
                                        <option value="declined">Declined</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                                        <i class="ri-arrow-down-s-line text-slate-400"></i>
                                    </div>
                                </div>
                                <p class="text-[10px] text-slate-500 italic">Set the initial registration status for these voters.</p>
                            </div>

                            <!-- Temporary Password -->
                            <div class="space-y-2">
                                <label for="temp_password" class="text-sm font-bold text-slate-700 uppercase tracking-wider">Temporary Password</label>
                                <div class="relative">
                                    <input type="text" name="temp_password" id="temp_password" required
                                           placeholder="e.g. Welcome2024!"
                                           class="w-full border rounded-lg px-4 py-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-200" />
                                    <div class="absolute inset-y-0 right-3 flex items-center">
                                        <i class="ri-lock-line text-slate-400"></i>
                                    </div>
                                </div>
                                <p class="text-[10px] text-slate-500 italic">This password will be assigned to ALL voters in this batch.</p>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-end gap-3">
                                <button type="submit"
                                        class="flex-1 bg-emerald-600 text-white rounded-lg px-6 py-3 font-bold text-xs tracking-widest hover:bg-emerald-700 transition-all shadow-md flex items-center justify-center gap-2">
                                    <i class="ri-check-double-line text-lg"></i>
                                    CONFIRM IMPORT
                                </button>
                                <a href="{{ route('admin.voters.index') }}"
                                   class="px-6 py-3 border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all uppercase tracking-widest">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Data Table Card -->
            <div class="bg-white rounded-xl border border-gray-200/60 shadow-sm overflow-hidden mb-12">
                <div class="px-6 py-4 border-b bg-gradient-to-r from-white via-indigo-50/30 to-white flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Fetched Data Preview</h3>
                        <p class="text-[11px] text-gray-500 font-medium">Reviewing {{ count($voters) }} detected entries</p>
                    </div>
                    <div class="text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-lg">
                        {{ count($voters) }} Voters Detected
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">Full Name</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">Email Address</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">Student ID</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">Phone</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($voters as $voter)
                                <tr class="hover:bg-slate-50/80 transition-all group">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-slate-100 rounded flex items-center justify-center text-slate-500 font-bold text-xs">
                                                {{ substr($voter->name ?? '?', 0, 1) }}
                                            </div>
                                            <span class="font-bold text-slate-900 text-sm">{{ $voter->name ?? '—' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2 text-xs font-bold text-slate-600">
                                            <i class="ri-mail-line text-slate-400"></i>
                                            {{ $voter->email ?? '—' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-[10px] font-bold text-indigo-500 uppercase tracking-wider bg-indigo-50 px-2 py-1 rounded">
                                            {{ $voter->student_id ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-xs text-slate-500">{{ $voter->phone ?? '—' }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
