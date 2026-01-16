@extends('layouts.app-main-admin')

@section('content')
    <div x-data="candidateData()" class="min-h-screen bg-slate-50 flex">
        <x-admin-sidebar />

        <main class="flex-1 min-w-0">
            <!-- Top Navigation Bar -->
            <div class="bg-white border-b border-slate-200 sticky top-0 z-40">
                <div class="max-w-7xl mx-auto px-4 sm:px-8 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] overflow-x-auto no-scrollbar whitespace-nowrap mr-4">
                            <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-600 transition-colors">Dashboard</a>
                            <i class="ri-arrow-right-s-line text-sm"></i>
                            <a href="{{ route('admin.candidates.index') }}" class="hover:text-indigo-600 transition-colors">Candidates</a>
                            <i class="ri-arrow-right-s-line text-sm text-slate-300"></i>
                            <span class="text-slate-900">Registration</span>
                        </div>
                        <button type="button" @click="resetForm()" class="text-[9px] font-black text-slate-500 hover:text-red-500 flex items-center gap-2 transition-all uppercase tracking-widest bg-slate-100 px-3 sm:px-4 py-2 rounded-lg flex-shrink-0">
                            <i class="ri-refresh-line"></i>
                            <span class="hidden xs:inline">Reset Form</span>
                            <span class="xs:hidden">Reset</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-8 py-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Header -->
                        <div class="flex items-start gap-4 sm:gap-5 mb-8">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-slate-900 flex items-center justify-center flex-shrink-0 shadow-xl shadow-slate-200">
                                <i class="ri-user-add-fill text-white text-xl sm:text-2xl"></i>
                            </div>
                            <div class="min-w-0">
                                <h1 class="text-xl sm:text-3xl font-black text-slate-900 uppercase tracking-tight truncate sm:whitespace-normal">Candidate Registration</h1>
                                <p class="text-slate-500 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1">Onboarding new candidates to the platform</p>
                            </div>
                        </div>

                        <!-- Step Indicator -->
                        <div class="flex items-center gap-4 bg-white p-3 rounded-2xl border border-slate-200 shadow-sm w-fit">
                            <div class="flex items-center gap-2.5 px-5 py-2.5 bg-slate-900 text-white rounded-xl shadow-lg">
                                <div class="w-5 h-5 rounded-lg bg-white/20 flex items-center justify-center text-[10px] font-black">1</div>
                                <span class="text-[10px] font-black uppercase tracking-widest">General Info</span>
                            </div>
                            <div class="flex items-center gap-2.5 px-5 py-2.5 text-slate-400 grayscale opacity-60">
                                <div class="w-5 h-5 rounded-lg bg-slate-100 flex items-center justify-center text-[10px] font-black">2</div>
                                <span class="text-[10px] font-black uppercase tracking-widest">Verification</span>
                            </div>
                        </div>

                        <form @submit.prevent="submitForm()" class="space-y-6">
                            <!-- Candidate Details Card -->
                            <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-200/40 overflow-hidden transition-all hover:shadow-2xl hover:shadow-slate-200/50">
                                <div class="p-6 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-100">
                                            <i class="ri-id-card-line text-white text-xl"></i>
                                        </div>
                                        <div>
                                            <h2 class="text-base font-black text-slate-800 uppercase tracking-tight">Identity Details</h2>
                                            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Official credentials</p>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full text-[9px] font-black uppercase tracking-widest border border-indigo-100">Profile</span>
                                </div>

                                @csrf
                                <div class="p-8 space-y-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="space-y-2">
                                            <label class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">
                                                Full Legal Name
                                                <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative group">
                                                <i class="ri-user-3-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-indigo-600 transition-colors text-lg"></i>
                                                <input type="text" x-model="formData.user_name"
                                                       placeholder="Enter full name..."
                                                       class="w-full pl-12 pr-6 py-3.5 bg-slate-50 border-none rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all font-bold text-slate-700 text-sm shadow-sm">
                                            </div>
                                            <div x-show="errors.user_name" class="mt-1 text-[9px] text-red-500 font-black uppercase tracking-wider flex items-center gap-1 ml-1">
                                                <i class="ri-error-warning-fill"></i>
                                                <span x-text="errors.user_name?.[0]"></span>
                                            </div>
                                        </div>

                                        <div class="space-y-2">
                                            <label class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">
                                                Official Email
                                                <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative group">
                                                <i class="ri-mail-send-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-indigo-600 transition-colors text-lg"></i>
                                                <input type="email" x-model="formData.user_email"
                                                       placeholder="Enter email address..."
                                                       class="w-full pl-12 pr-6 py-3.5 bg-slate-50 border-none rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all font-bold text-slate-700 text-sm shadow-sm">
                                            </div>
                                            <div x-show="errors.user_email" class="mt-1 text-[9px] text-red-500 font-black uppercase tracking-wider flex items-center gap-1 ml-1">
                                                <i class="ri-error-warning-fill"></i>
                                                <span x-text="errors.user_email?.[0]"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">
                                            Affiliated Organization
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative group">
                                            <i class="ri-community-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-indigo-600 transition-colors text-lg pointer-events-none"></i>
                                            <select x-model="formData.organization_id"
                                                    class="w-full pl-12 pr-10 py-3.5 bg-slate-50 border-none rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all font-bold text-slate-700 text-sm appearance-none shadow-sm">
                                                <option value="">Select organization</option>
                                                <template x-for="org in organizations" :key="org.id">
                                                    <option :value="org.id" x-text="org.name"></option>
                                                </template>
                                            </select>
                                            <i class="ri-arrow-down-s-line absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                                        </div>
                                        <div x-show="errors.organization_id" class="mt-1 text-[9px] text-red-500 font-black uppercase tracking-wider flex items-center gap-1 ml-1">
                                            <i class="ri-error-warning-fill"></i>
                                            <span x-text="errors.organization_id?.[0]"></span>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="space-y-2">
                                            <label class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">
                                                Election Session
                                            </label>
                                            <div class="relative group">
                                                <i class="ri-hand-coin-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-indigo-600 transition-colors text-lg pointer-events-none"></i>
                                                <select x-model="formData.election_id"
                                                        class="w-full pl-12 pr-10 py-3.5 bg-slate-50 border-none rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all font-bold text-slate-700 text-sm appearance-none shadow-sm">
                                                    <option value="">Select election</option>
                                                    <template x-for="e in elections" :key="e.id">
                                                        <option :value="e.id" x-text="e.title"></option>
                                                    </template>
                                                </select>
                                                <i class="ri-arrow-down-s-line absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                                            </div>
                                        </div>

                                        <div class="space-y-2">
                                            <label class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">
                                                Political Affiliation
                                            </label>
                                            <div class="relative group">
                                                <i class="ri-team-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-indigo-600 transition-colors text-lg pointer-events-none"></i>
                                                <select x-model="formData.partylist_id"
                                                        class="w-full pl-12 pr-10 py-3.5 bg-slate-50 border-none rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all font-bold text-slate-700 text-sm appearance-none shadow-sm">
                                                    <option value="">Independent</option>
                                                    <template x-for="p in filteredPartylists" :key="p.id">
                                                        <option :value="p.id" x-text="p.name"></option>
                                                    </template>
                                                </select>
                                                <i class="ri-arrow-down-s-line absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">
                                            Designated Position
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative group">
                                            <i class="ri-briefcase-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-indigo-600 transition-colors text-lg pointer-events-none"></i>
                                            <select x-model="formData.position_id"
                                                    class="w-full pl-12 pr-10 py-3.5 bg-slate-50 border-none rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all font-bold text-slate-700 text-sm appearance-none shadow-sm">
                                                <option value="">Select position</option>
                                                <optgroup label="Standard Titles" class="font-bold text-indigo-600 bg-indigo-50">
                                                    <template x-for="title in commonPositions" :key="title">
                                                        <option :value="'preset:' + title" x-text="title" class="font-bold text-slate-700 bg-white"></option>
                                                    </template>
                                                </optgroup>
                                                <optgroup label="Active Definitions" class="font-bold text-slate-900">
                                                    <template x-for="p in existingPositions" :key="p.id">
                                                        <option :value="p.id" x-text="p.name"></option>
                                                    </template>
                                                </optgroup>
                                                <option value="other" class="font-black text-indigo-600">+ CUSTOM POSITION</option>
                                            </select>
                                            <i class="ri-arrow-down-s-line absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                                        </div>

                                        <div x-show="formData.position_id === 'other' || formData.position_id === 'preset:Custom Position'"
                                             x-transition
                                             class="mt-3">
                                            <input type="text"
                                                   x-model="formData.new_position_name"
                                                   placeholder="Define new position title..."
                                                   class="w-full px-6 py-3.5 bg-indigo-50 border border-indigo-100 rounded-xl focus:ring-4 focus:ring-indigo-500/10 transition-all font-bold text-indigo-900 placeholder-indigo-300 text-sm">
                                            <div x-show="errors.new_position_name" class="mt-1 text-[9px] text-red-500 font-black uppercase tracking-wider flex items-center gap-1 ml-1">
                                                <i class="ri-error-warning-fill"></i>
                                                <span x-text="errors.new_position_name?.[0]"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">
                                            Platform Statement
                                        </label>
                                        <textarea x-model="formData.platform"
                                                  rows="5"
                                                  maxlength="500"
                                                  placeholder="Outline the candidate's core objectives and vision..."
                                                  class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all font-bold text-slate-700 text-sm resize-none shadow-sm"></textarea>
                                        <div class="flex items-center justify-between mt-2 px-1">
                                            <p class="text-[9px] text-indigo-600 font-black uppercase tracking-widest flex items-center gap-2">
                                                <i class="ri-lightbulb-flash-fill text-sm"></i>
                                                Detailed platforms increase engagement
                                            </p>
                                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest" :class="(formData.platform?.length || 0) > 450 ? 'text-orange-500' : ''" x-text="(formData.platform?.length || 0) + ' / 500'"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Photo Upload Card -->
                            <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-200/40 overflow-hidden">
                                <div class="p-6 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-slate-900 flex items-center justify-center shadow-lg">
                                            <i class="ri-camera-lens-line text-white text-xl"></i>
                                        </div>
                                        <div>
                                            <h2 class="text-base font-black text-slate-800 uppercase tracking-tight">Biometric Photo</h2>
                                            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Ballot portrait</p>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-[9px] font-black uppercase tracking-widest border border-slate-200">Asset</span>
                                </div>

                                <div class="p-8">
                                    <div class="relative group">
                                        <label class="block w-full h-56 border-2 border-dashed border-slate-200 rounded-3xl hover:border-indigo-400 hover:bg-indigo-50/30 transition-all cursor-pointer overflow-hidden relative group">
                                            <div x-show="!photoPreview" class="h-full flex flex-col items-center justify-center text-slate-400">
                                                <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform shadow-sm">
                                                    <i class="ri-upload-cloud-2-line text-3xl text-slate-300 group-hover:text-indigo-500 transition-colors"></i>
                                                </div>
                                                <span class="text-[10px] font-black uppercase tracking-widest">Select Portrait File</span>
                                                <span class="text-[9px] font-bold text-slate-300 uppercase tracking-[0.2em] mt-1.5">Max: 3000 x 3000 PX</span>
                                            </div>
                                            <img x-show="photoPreview" :src="photoPreview" class="w-full h-full object-cover">
                                            <input type="file" @change="handlePhotoUpload($event)" accept="image/*" class="hidden">

                                            <div x-show="photoPreview" class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-sm">
                                                <span class="text-white text-[10px] font-black uppercase tracking-[0.2em] bg-white/20 px-5 py-2.5 rounded-xl border border-white/30">Replace Image</span>
                                            </div>
                                        </label>
                                        <button x-show="photoPreview" @click="photoPreview=null; formData.photo=null;" type="button"
                                                class="absolute -top-2 -right-2 w-8 h-8 bg-red-500 text-white rounded-xl hover:bg-red-600 transition-all shadow-xl shadow-red-200 flex items-center justify-center z-10">
                                            <i class="ri-close-line text-xl font-black"></i>
                                        </button>
                                    </div>
                                    <div x-show="errors.photo" class="mt-3 text-[9px] text-red-500 font-black uppercase tracking-wider flex items-center gap-1 ml-1">
                                        <i class="ri-error-warning-fill"></i>
                                        <span x-text="errors.photo?.[0]"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Final Action -->
                            <div class="bg-white rounded-3xl border border-slate-200 p-6 flex items-center justify-between shadow-sm">
                                <a href="{{ route('admin.candidates.index') }}"
                                   class="px-6 py-3 text-slate-400 hover:text-slate-900 font-black text-[10px] uppercase tracking-widest transition-all flex items-center gap-2">
                                    <i class="ri-arrow-left-line"></i>
                                    Discard
                                </a>
                                <button type="submit"
                                        :disabled="loading"
                                        class="px-10 py-4 bg-slate-900 text-white rounded-xl hover:bg-indigo-600 hover:shadow-2xl hover:shadow-indigo-200 disabled:opacity-50 transition-all font-black text-[10px] uppercase tracking-[0.2em] flex items-center gap-3 group">
                                    <template x-if="loading">
                                        <i class="ri-loader-4-line animate-spin text-lg"></i>
                                    </template>
                                    <template x-if="!loading">
                                        <i class="ri-send-plane-fill text-lg group-hover:translate-x-1 transition-transform"></i>
                                    </template>
                                    <span x-text="loading ? 'Submitting...' : 'Register Candidate'"></span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-1 space-y-6">
                        <div class="bg-white rounded-3xl border border-slate-200/60 p-8 shadow-xl shadow-slate-200/40 relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-50 rounded-full -mr-12 -mt-12"></div>
                            <h3 class="text-[10px] font-black text-slate-900 uppercase tracking-widest mb-6 relative z-10">Integrity Check</h3>
                            <div class="space-y-5 relative z-10">
                                <div class="flex justify-between items-end mb-1">
                                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Completion</span>
                                    <span class="text-xl font-black text-slate-900" x-text="completionPercentage + '%'"></span>
                                </div>
                                <div class="w-full bg-slate-50 h-1.5 rounded-full overflow-hidden p-0 border border-slate-100">
                                    <div class="bg-slate-900 h-full rounded-full transition-all duration-1000 shadow-sm"
                                         :style="'width: ' + completionPercentage + '%'"></div>
                                </div>
                                <p class="text-[9px] text-slate-400 font-bold uppercase leading-relaxed tracking-wider">Ensure all required fields are populated with verified data.</p>
                            </div>
                        </div>

                        <div class="bg-indigo-600 rounded-3xl p-8 text-white shadow-2xl shadow-indigo-200 relative overflow-hidden">
                            <div class="absolute bottom-0 right-0 w-24 h-24 bg-white/10 rounded-full -mr-12 -mb-12 blur-2xl"></div>
                            <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mb-6 border border-white/20">
                                <i class="ri-lightbulb-fill text-2xl text-indigo-100"></i>
                            </div>
                            <h3 class="text-lg font-black uppercase tracking-tight mb-3">Ballot Excellence</h3>
                            <ul class="space-y-3">
                                <li class="flex items-start gap-2.5">
                                    <i class="ri-checkbox-circle-fill text-indigo-200 mt-0.5"></i>
                                    <span class="text-[10px] font-bold text-indigo-50 leading-relaxed uppercase tracking-wide">Use professional portraits</span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <i class="ri-checkbox-circle-fill text-indigo-200 mt-0.5"></i>
                                    <span class="text-[10px] font-bold text-indigo-50 leading-relaxed uppercase tracking-wide">Verify legal email addresses</span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <i class="ri-checkbox-circle-fill text-indigo-200 mt-0.5"></i>
                                    <span class="text-[10px] font-bold text-indigo-50 leading-relaxed uppercase tracking-wide">Be concise with platforms</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Success Modal -->
            <div x-show="showSuccess" x-cloak
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
                <div @click.away="showSuccess = false"
                     class="bg-white rounded-3xl max-w-sm w-full p-10 shadow-2xl text-center">
                    <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-inner">
                        <i class="ri-checkbox-circle-fill text-5xl"></i>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 mb-2 uppercase tracking-tight" x-text="successTitle"></h3>
                    <p class="text-slate-500 font-bold text-[10px] mb-8 leading-relaxed uppercase tracking-widest" x-text="successMessage"></p>
                    <div class="flex flex-col gap-3">
                        <button @click="window.location.href='{{ route('admin.candidates.index') }}'"
                                class="w-full py-4 bg-slate-900 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-xl shadow-slate-200">
                            View Candidate Registry
                        </button>
                        <button @click="resetFormAfterSuccess()"
                                class="w-full py-4 bg-slate-100 text-slate-600 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-200 transition-all">
                            Add Another Entry
                        </button>
                    </div>
                </div>
            </div>

            <!-- Error Modal -->
            <div x-show="showError" x-cloak
                 class="fixed inset-0 bg-red-900/20 backdrop-blur-sm flex items-center justify-center p-4 z-50">
                <div @click.away="showError = false"
                     class="bg-white rounded-3xl max-w-sm w-full p-10 shadow-2xl text-center">
                    <div class="w-20 h-20 bg-red-100 text-red-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="ri-error-warning-fill text-5xl"></i>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 mb-2 uppercase tracking-tight">Registry Error</h3>
                    <p class="text-slate-500 font-bold text-[10px] mb-8 leading-relaxed uppercase tracking-widest" x-text="errorMessage"></p>
                    <button @click="showError = false"
                            class="w-full py-4 bg-slate-900 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-red-600 transition-all">
                        Review & Correct
                    </button>
                </div>
            </div>
        </main>
    </div>
@endsection

<script>
    function candidateData() {
        return {
            existingPositions: @json($positions->map(fn($p) => ['id' => $p->id, 'name' => $p->title])->values()),
            commonPositions: @json($commonPositions ?? []),
            organizations: @json($organizations->map(fn($o) => ['id' => $o->id, 'name' => $o->name])->values()),
            partylists: @json($partylists->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'organization_id' => $p->organization_id ?? null])->values()),
            elections: @json(($elections ?? collect())->map(fn($e) => ['id' => $e->id, 'title' => $e->title])->values()),

            formData: {
                user_name: '',
                user_email: '',
                organization_id: '',
                election_id: '',
                position_id: '',
                new_position_name: '',
                partylist_id: '',
                platform: '',
                photo: null,
                status: 'active'
            },
            errors: {},
            loading: false,
            showSuccess: false,
            successMessage: '',
            successTitle: '',
            showError: false,
            errorMessage: '',
            photoPreview: null,

            get filteredPartylists() {
                if (!this.formData.organization_id) return [];
                return this.partylists.filter(p => String(p.organization_id) === String(this.formData.organization_id));
            },

            get completionPercentage() {
                let filled = 0;
                const total = 4; // email, organization, position, status
                if (this.formData.user_email) filled++;
                if (this.formData.organization_id) filled++;
                if (this.formData.position_id) filled++;
                if (this.formData.status) filled++;
                return Math.round((filled / total) * 100);
            },

            handlePhotoUpload(e) {
                const file = e.target.files[0];
                if (!file) { this.photoPreview = null; this.formData.photo = null; return; }
                if (!file.type.startsWith('image/')) {
                    this.errors.photo = ['File must be an image'];
                    return;
                }
                if (file.size > 3 * 1024 * 1024) {
                    this.errors.photo = ['File must be less than 3MB'];
                    return;
                }
                this.formData.photo = file;
                const reader = new FileReader();
                reader.onload = (ev) => this.photoPreview = ev.target.result;
                reader.readAsDataURL(file);
            },

            validate() {
                this.errors = {};
                if (!this.formData.user_name || !this.formData.user_name.trim()) this.errors.user_name = ['Name is required'];
                if (!this.formData.user_email || !this.formData.user_email.trim()) {
                    this.errors.user_email = ['Email is required'];
                } else {
                    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!re.test(this.formData.user_email.trim())) this.errors.user_email = ['Enter a valid email'];
                }
                if (!this.formData.organization_id) this.errors.organization_id = ['Organization is required'];
                if (!this.formData.position_id) {
                    this.errors.position_id = ['Position is required'];
                } else if ((this.formData.position_id === 'other' || this.formData.position_id === 'preset:Custom Position') && !this.formData.new_position_name.trim()) {
                    this.errors.new_position_name = ['Enter position name'];
                }
                return Object.keys(this.errors).length === 0;
            },

            resetForm() {
                this.formData = {
                    user_name: '', user_email: '', organization_id: '', election_id: '', position_id: '',
                    new_position_name: '', partylist_id: '', platform: '', photo: null, status: 'active'
                };
                this.photoPreview = null;
                this.errors = {};
            },

            saveDraft() {
                localStorage.setItem('candidateDraft', JSON.stringify(this.formData));
                alert('Draft saved successfully!');
            },

            loadDraft() {
                const draft = localStorage.getItem('candidateDraft');
                if (draft) {
                    const data = JSON.parse(draft);
                    Object.assign(this.formData, data);
                    alert('Draft loaded successfully!');
                }
            },

            submitForm() {
                if (!this.validate()) {
                    this.errorMessage = 'Please fix form errors.';
                    this.showError = true;
                    return;
                }
                if (!confirm('Are you sure you want to submit and create this candidate?')) {
                    return;
                }
                this.loading = true;
                this.errors = {};

                const formData = new FormData();
                formData.append('_token', document.querySelector('input[name=_token]').value);

                // send user name/email so backend can find-or-create the user
                formData.append('user_name', this.formData.user_name.trim());
                formData.append('user_email', this.formData.user_email.trim());

                formData.append('organization_id', this.formData.organization_id);
                formData.append('election_id', this.formData.election_id || '');

                if (String(this.formData.position_id).startsWith('preset:')) {
                    formData.append('position_id', '');
                    formData.append('new_position_name', this.formData.position_id.replace('preset:', ''));
                } else if (this.formData.position_id === 'other') {
                    formData.append('position_id', '');
                    formData.append('new_position_name', this.formData.new_position_name.trim());
                } else {
                    formData.append('position_id', this.formData.position_id || '');
                    formData.append('new_position_name', '');
                }

                formData.append('partylist_id', this.formData.partylist_id || '');
                formData.append('platform', this.formData.platform || '');
                formData.append('status', this.formData.status);
                if (this.formData.photo) formData.append('photo', this.formData.photo);

                fetch('{{ route('admin.candidates.store') }}', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(async response => {
                        const data = await response.json().catch(() => ({}));
                        if (response.ok && data.success) {
                            this.successTitle = data.title || 'Candidate Created';
                            this.successMessage = data.message || 'Successfully created';
                            this.showSuccess = true;
                            localStorage.removeItem('candidateDraft');
                            this.resetFormAfterSuccess = () => {
                                this.showSuccess = false;
                                this.resetForm();
                            };
                        } else {
                            this.errors = data.errors || {};
                            this.errorMessage = data.message || 'Server error';
                            this.showError = true;
                        }
                    })
                    .catch(error => {
                        this.errorMessage = 'Network error';
                        this.showError = true;
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            }
        };
    }
</script>

<style>
    [x-cloak] { display: none !important; }
</style>
