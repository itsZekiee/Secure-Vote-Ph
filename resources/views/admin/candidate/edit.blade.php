@extends('layouts.app-main-admin')

@section('content')
    <div x-data="candidateEditData()" class="min-h-screen bg-slate-50 flex">
        <x-admin-sidebar />

        <main class="flex-1">
            <!-- Top Navigation Bar -->
            <div class="bg-white border-b border-slate-200 sticky top-0 z-40">
                <div class="max-w-7xl mx-auto px-4 sm:px-8 py-4">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] overflow-x-auto no-scrollbar whitespace-nowrap">
                            <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-600 transition-colors">Dashboard</a>
                            <i class="ri-arrow-right-s-line text-xs"></i>
                            <a href="{{ route('admin.candidates.index') }}" class="hover:text-indigo-600 transition-colors">Candidates</a>
                            <i class="ri-arrow-right-s-line text-xs text-slate-300"></i>
                            <span class="font-black text-slate-900">Edit Candidate</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-8 py-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Header -->
                        <div class="flex items-start gap-4 sm:gap-5 mb-8">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-indigo-600 flex items-center justify-center flex-shrink-0 shadow-lg shadow-indigo-100">
                                <i class="ri-user-settings-line text-white text-xl sm:text-2xl"></i>
                            </div>
                            <div class="min-w-0">
                                <h1 class="text-xl sm:text-3xl font-black text-slate-900 uppercase tracking-tight truncate sm:whitespace-normal">Edit Candidate</h1>
                                <p class="text-slate-500 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1">Update profile and assignments</p>
                            </div>
                        </div>

                        <form @submit.prevent="submitForm()" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <!-- Profile Preview Header -->
                            <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-200/40 overflow-hidden">
                                <div class="h-20 bg-gradient-to-r from-indigo-600 via-blue-500 to-indigo-600"></div>
                                <div class="px-6 pb-6">
                                    <div class="relative flex items-end gap-4 sm:gap-5 -mt-8">
                                        <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white rounded-2xl p-1 shadow-xl">
                                            <div class="w-full h-full bg-slate-100 rounded-xl flex items-center justify-center text-slate-400 overflow-hidden">
                                                @if($candidate->photo)
                                                    <img src="{{ asset('storage/' . $candidate->photo) }}" class="w-full h-full object-cover">
                                                @else
                                                    <i class="ri-user-edit-line text-2xl sm:text-3xl"></i>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex-1 mb-1.5 min-w-0">
                                            <h1 class="text-lg sm:text-xl font-black text-slate-900 uppercase tracking-tight truncate">{{ $candidate->name }}</h1>
                                            <p class="text-[9px] text-slate-500 font-bold tracking-widest uppercase mt-0.5 truncate">Reference ID: #{{ $candidate->id }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Candidate Details Card -->
                            <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-200/40 overflow-hidden transition-all hover:shadow-2xl hover:shadow-slate-200/50">
                                <div class="p-6 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center">
                                            <i class="ri-profile-line text-white text-xl"></i>
                                        </div>
                                        <div>
                                            <h2 class="text-base font-black text-slate-800 uppercase tracking-tight">Information Details</h2>
                                            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Profile & Assignments</p>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full text-[9px] font-black uppercase tracking-widest border border-indigo-100">Editor</span>
                                </div>

                                <div class="p-8 space-y-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="space-y-2">
                                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Full Name</label>
                                            <input type="text" x-model="formData.user_name"
                                                   class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all font-bold text-slate-700 text-sm shadow-sm">
                                            <div x-show="errors.user_name" class="mt-1 text-[9px] text-red-500 font-black uppercase tracking-wider ml-1" x-text="errors.user_name?.[0]"></div>
                                        </div>
                                        <div class="space-y-2">
                                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Email Address</label>
                                            <input type="email" x-model="formData.user_email"
                                                   class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all font-bold text-slate-700 text-sm shadow-sm">
                                            <div x-show="errors.user_email" class="mt-1 text-[9px] text-red-500 font-black uppercase tracking-wider ml-1" x-text="errors.user_email?.[0]"></div>
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Organization</label>
                                        <div class="relative">
                                            <select x-model="formData.organization_id"
                                                    class="w-full pl-5 pr-10 py-3.5 bg-slate-50 border-none rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all font-bold text-slate-700 text-sm shadow-sm appearance-none">
                                                <option value="">Select organization</option>
                                                <template x-for="org in organizations" :key="org.id">
                                                    <option :value="org.id" x-text="org.name"></option>
                                                </template>
                                            </select>
                                            <i class="ri-arrow-down-s-line absolute right-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="space-y-2">
                                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Election</label>
                                            <div class="relative">
                                                <select x-model="formData.election_id"
                                                        class="w-full pl-5 pr-10 py-3.5 bg-slate-50 border-none rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all font-bold text-slate-700 text-sm shadow-sm appearance-none">
                                                    <option value="">Select election</option>
                                                    <template x-for="e in elections" :key="e.id">
                                                        <option :value="e.id" x-text="e.title"></option>
                                                    </template>
                                                </select>
                                                <i class="ri-arrow-down-s-line absolute right-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                            </div>
                                        </div>
                                        <div class="space-y-2">
                                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Partylist</label>
                                            <div class="relative">
                                                <select x-model="formData.partylist_id"
                                                        class="w-full pl-5 pr-10 py-3.5 bg-slate-50 border-none rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all font-bold text-slate-700 text-sm shadow-sm appearance-none">
                                                    <option value="">Independent</option>
                                                    <template x-for="p in filteredPartylists" :key="p.id">
                                                        <option :value="p.id" x-text="p.name"></option>
                                                    </template>
                                                </select>
                                                <i class="ri-arrow-down-s-line absolute right-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Position</label>
                                        <div class="relative">
                                            <select x-model="formData.position_id"
                                                    class="w-full pl-5 pr-10 py-3.5 bg-slate-50 border-none rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all font-bold text-slate-700 text-sm shadow-sm appearance-none">
                                                <optgroup label="Existing Positions">
                                                    <template x-for="p in existingPositions" :key="p.id">
                                                        <option :value="p.id" x-text="p.name"></option>
                                                    </template>
                                                </optgroup>
                                            </select>
                                            <i class="ri-arrow-down-s-line absolute right-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Platform / Description</label>
                                        <textarea x-model="formData.platform" rows="5"
                                                  class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all font-bold text-slate-700 text-sm resize-none shadow-sm"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Photo Card -->
                            <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-200/40 overflow-hidden">
                                <div class="p-6 bg-emerald-50/50 border-b border-emerald-100 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center">
                                            <i class="ri-image-edit-line text-white text-xl"></i>
                                        </div>
                                        <div>
                                            <h2 class="text-base font-black text-slate-800 uppercase tracking-tight">Candidate Photo</h2>
                                            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Asset Update</p>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[9px] font-black uppercase tracking-widest border border-emerald-100">Portrait</span>
                                </div>
                                <div class="p-8 flex items-center gap-8">
                                    <div class="w-24 h-24 rounded-2xl bg-slate-100 overflow-hidden border-2 border-white shadow-xl flex-shrink-0">
                                        <img x-show="photoPreview" :src="photoPreview" class="w-full h-full object-cover">
                                        <div x-show="!photoPreview" class="w-full h-full flex items-center justify-center text-slate-300">
                                            <i class="ri-user-line text-3xl"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <label class="inline-flex items-center px-6 py-3 bg-white border-2 border-dashed border-slate-200 rounded-xl text-[10px] font-black text-slate-600 cursor-pointer hover:border-indigo-500 hover:text-indigo-600 transition-all uppercase tracking-widest">
                                            <i class="ri-upload-2-line mr-2 text-lg"></i>
                                            Change Photo
                                            <input type="file" @change="handlePhotoUpload($event)" accept="image/*" class="hidden">
                                        </label>
                                        <p class="mt-2 text-[9px] text-slate-400 font-black uppercase tracking-widest">Recommended: Square, Max 3MB</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Bottom Actions -->
                            <div class="bg-white rounded-3xl border border-slate-200 p-6 flex items-center justify-between shadow-sm">
                                <a href="{{ route('admin.candidates.show', $candidate->id) }}" class="text-[10px] font-black text-slate-400 hover:text-slate-900 transition-all flex items-center gap-2 uppercase tracking-widest">
                                    <i class="ri-arrow-left-line"></i>
                                    Back to Profile
                                </a>
                                <button type="submit" :disabled="loading"
                                        class="px-10 py-4 bg-slate-900 text-white rounded-xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-indigo-600 shadow-xl shadow-indigo-100 transition-all flex items-center gap-2 disabled:opacity-50 group">
                                    <i x-show="!loading" class="ri-save-line text-lg group-hover:scale-110 transition-transform"></i>
                                    <i x-show="loading" class="ri-loader-4-line animate-spin text-lg"></i>
                                    <span x-text="loading ? 'SAVING CHANGES...' : 'SAVE CHANGES'"></span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <div class="bg-white rounded-3xl border border-slate-200/60 p-8 shadow-xl shadow-slate-200/40 relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-50 rounded-full -mr-12 -mt-12"></div>
                            <h3 class="text-[10px] font-black text-slate-900 uppercase tracking-widest mb-6 relative z-10">Status Management</h3>
                            <div class="space-y-4 relative z-10">
                                <div class="flex items-center justify-between p-4 bg-slate-50/50 rounded-xl border border-slate-100">
                                    <span class="text-[10px] font-black text-slate-700 uppercase tracking-widest">ACTIVE STATUS</span>
                                    <button @click="formData.status = (formData.status === 'active' ? 'inactive' : 'active')"
                                            class="relative inline-flex h-5 w-10 items-center rounded-full transition-colors focus:outline-none"
                                            :class="formData.status === 'active' ? 'bg-emerald-500' : 'bg-slate-300'">
                                        <span class="inline-block h-3.5 w-3.5 transform rounded-full bg-white transition-transform"
                                              :class="formData.status === 'active' ? 'translate-x-6' : 'translate-x-0.5'"></span>
                                    </button>
                                </div>
                                <p class="text-[9px] text-slate-400 font-bold uppercase leading-relaxed tracking-wider">Deactivating a candidate will remove them from the active ballot.</p>
                            </div>
                        </div>

                        <div class="bg-indigo-900 rounded-3xl p-8 text-white shadow-2xl shadow-indigo-200 relative overflow-hidden">
                            <div class="absolute bottom-0 right-0 w-24 h-24 bg-white/10 rounded-full -mr-12 -mb-12 blur-2xl"></div>
                            <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mb-6 border border-white/20">
                                <i class="ri-lightbulb-line text-2xl text-indigo-200"></i>
                            </div>
                            <h3 class="text-base font-black uppercase tracking-tight mb-2">Pro Tip</h3>
                            <p class="text-indigo-200 text-[10px] font-bold uppercase leading-relaxed tracking-wider">Updates reflect immediately on the live election preview for voters.</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Success Modal -->
        <div x-show="showSuccess" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-sm w-full p-10 text-center shadow-2xl">
                <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-inner">
                    <i class="ri-checkbox-circle-fill text-5xl"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-900 mb-2 uppercase tracking-tight">Success!</h3>
                <p class="text-slate-500 font-bold text-[10px] mb-8 uppercase tracking-widest">Candidate information has been updated successfully.</p>
                <button @click="window.location.href='{{ route('admin.candidates.index') }}'" class="w-full py-4 bg-slate-900 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-600 transition-all">
                    VIEW CANDIDATES
                </button>
            </div>
        </div>
    </div>
@endsection

<script>
    function candidateEditData() {
        return {
            existingPositions: @json($positions->map(fn($p) => ['id' => $p->id, 'name' => $p->title])->values()),
            organizations: @json($organizations->map(fn($o) => ['id' => $o->id, 'name' => $o->name])->values()),
            partylists: @json($partylists->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'organization_id' => $p->organization_id])->values()),
            elections: @json($elections->map(fn($e) => ['id' => $e->id, 'title' => $e->title])->values()),

            formData: {
                user_name: @json($candidate->name),
                user_email: @json($candidate->user->email ?? ''),
                organization_id: @json($candidate->organization_id),
                election_id: @json($candidate->election_id),
                position_id: @json($candidate->position_id),
                partylist_id: @json($candidate->partylist_id),
                platform: @json($candidate->platform),
                status: @json($candidate->status),
                photo: null
            },
            photoPreview: @json($candidate->photo ? asset('storage/' . $candidate->photo) : null),
            errors: {},
            loading: false,
            showSuccess: false,

            get filteredPartylists() {
                if (!this.formData.organization_id) return [];
                return this.partylists.filter(p => String(p.organization_id) === String(this.formData.organization_id));
            },

            handlePhotoUpload(e) {
                const file = e.target.files[0];
                if (!file) return;
                this.formData.photo = file;
                const reader = new FileReader();
                reader.onload = (ev) => this.photoPreview = ev.target.result;
                reader.readAsDataURL(file);
            },

            async submitForm() {
                this.loading = true;
                this.errors = {};

                const formData = new FormData();
                formData.append('_method', 'PUT');
                formData.append('_token', '{{ csrf_token() }}');

                Object.keys(this.formData).forEach(key => {
                    if (this.formData[key] !== null) {
                        formData.append(key, this.formData[key]);
                    }
                });

                try {
                    const response = await fetch('{{ route('admin.candidates.update', $candidate->id) }}', {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });

                    const data = await response.json();
                    if (response.ok && data.success) {
                        this.showSuccess = true;
                    } else {
                        this.errors = data.errors || {};
                        alert(data.message || 'Validation failed');
                    }
                } catch (e) {
                    alert('Network error occurred');
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>

<style>
    [x-cloak] { display: none !important; }
</style>
