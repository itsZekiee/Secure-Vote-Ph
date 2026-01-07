@extends('layouts.app-main-admin')

@section('content')
    <div x-data="candidateEditData()" class="min-h-screen bg-gray-50 flex">
        <x-admin-sidebar />

        <main class="flex-1">
            <!-- Top Navigation Bar -->
            <div class="bg-white border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3 text-sm text-gray-600">
                            <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-600 transition-colors">Dashboard</a>
                            <i class="ri-arrow-right-s-line text-xs"></i>
                            <a href="{{ route('admin.candidates.index') }}" class="hover:text-indigo-600 transition-colors">Candidates</a>
                            <i class="ri-arrow-right-s-line text-xs"></i>
                            <span class="font-medium text-gray-900">Edit Candidate</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-6 py-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Header -->
                        <div class="flex items-start gap-4 mb-8">
                            <div class="w-14 h-14 rounded-2xl bg-indigo-600 flex items-center justify-center flex-shrink-0 shadow-lg shadow-indigo-200">
                                <i class="ri-user-settings-line text-white text-2xl"></i>
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900">Edit Candidate</h1>
                                <p class="text-gray-600 mt-1">Update candidate profile and election assignments</p>
                            </div>
                        </div>

                        <form @submit.prevent="submitForm()" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <!-- Candidate Details Card -->
                            <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden transition-all hover:shadow-md">
                                <div class="p-6 bg-slate-50 border-b border-gray-100 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center">
                                            <i class="ri-profile-line text-white text-lg"></i>
                                        </div>
                                        <div>
                                            <h2 class="text-lg font-bold text-gray-900">Profile Information</h2>
                                            <p class="text-xs text-gray-500">Update personal details and platform</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-8 space-y-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-2">Full Name</label>
                                            <input type="text" x-model="formData.user_name"
                                                   class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none">
                                            <div x-show="errors.user_name" class="mt-2 text-xs text-red-500 font-bold" x-text="errors.user_name?.[0]"></div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-2">Email Address</label>
                                            <input type="email" x-model="formData.user_email"
                                                   class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none">
                                            <div x-show="errors.user_email" class="mt-2 text-xs text-red-500 font-bold" x-text="errors.user_email?.[0]"></div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Organization</label>
                                        <select x-model="formData.organization_id"
                                                class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none appearance-none">
                                            <option value="">Select organization</option>
                                            <template x-for="org in organizations" :key="org.id">
                                                <option :value="org.id" x-text="org.name"></option>
                                            </template>
                                        </select>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-2">Election</label>
                                            <select x-model="formData.election_id"
                                                    class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none appearance-none">
                                                <option value="">Select election</option>
                                                <template x-for="e in elections" :key="e.id">
                                                    <option :value="e.id" x-text="e.title"></option>
                                                </template>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-2">Partylist</label>
                                            <select x-model="formData.partylist_id"
                                                    class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none appearance-none">
                                                <option value="">Independent</option>
                                                <template x-for="p in filteredPartylists" :key="p.id">
                                                    <option :value="p.id" x-text="p.name"></option>
                                                </template>
                                            </select>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Position</label>
                                        <select x-model="formData.position_id"
                                                class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none appearance-none">
                                            <optgroup label="Existing Positions">
                                                <template x-for="p in existingPositions" :key="p.id">
                                                    <option :value="p.id" x-text="p.name"></option>
                                                </template>
                                            </optgroup>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Platform / Description</label>
                                        <textarea x-model="formData.platform" rows="5"
                                                  class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none resize-none"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Photo Card -->
                            <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden">
                                <div class="p-6 bg-emerald-50 border-b border-emerald-100">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center">
                                            <i class="ri-image-edit-line text-white text-lg"></i>
                                        </div>
                                        <h2 class="text-lg font-bold text-gray-900">Candidate Photo</h2>
                                    </div>
                                </div>
                                <div class="p-8 flex items-center gap-8">
                                    <div class="w-32 h-32 rounded-3xl bg-gray-100 overflow-hidden border-4 border-white shadow-xl flex-shrink-0">
                                        <img x-show="photoPreview" :src="photoPreview" class="w-full h-full object-cover">
                                        <div x-show="!photoPreview" class="w-full h-full flex items-center justify-center text-gray-300">
                                            <i class="ri-user-line text-4xl"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <label class="inline-flex items-center px-6 py-3 bg-white border-2 border-dashed border-gray-300 rounded-2xl text-sm font-bold text-gray-600 cursor-pointer hover:border-indigo-500 hover:text-indigo-600 transition-all">
                                            <i class="ri-upload-2-line mr-2 text-lg"></i>
                                            Change Photo
                                            <input type="file" @change="handlePhotoUpload($event)" accept="image/*" class="hidden">
                                        </label>
                                        <p class="mt-3 text-xs text-gray-500 font-medium">Recommended: Square JPG or PNG, max 3MB</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Bottom Actions -->
                            <div class="bg-white rounded-3xl border border-gray-200 p-8 flex items-center justify-between">
                                <a href="{{ route('admin.candidates.show', $candidate->id) }}" class="text-sm font-bold text-gray-500 hover:text-gray-700 transition-all flex items-center gap-2">
                                    <i class="ri-arrow-left-line"></i>
                                    Back to Profile
                                </a>
                                <div class="flex gap-4">
                                    <button type="submit" :disabled="loading"
                                            class="px-10 py-4 bg-indigo-600 text-white rounded-2xl font-bold text-sm hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all flex items-center gap-2 disabled:opacity-50">
                                        <i x-show="!loading" class="ri-save-line"></i>
                                        <i x-show="loading" class="ri-loader-4-line animate-spin"></i>
                                        <span x-text="loading ? 'SAVING CHANGES...' : 'SAVE CHANGES'"></span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Sidebar Stats -->
                    <div class="space-y-6">
                        <div class="bg-white rounded-3xl border border-gray-200 p-8 shadow-sm">
                            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-6">Status Management</h3>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-gray-100">
                                    <span class="text-sm font-bold text-gray-700">ACTIVE STATUS</span>
                                    <button @click="formData.status = (formData.status === 'active' ? 'inactive' : 'active')"
                                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
                                            :class="formData.status === 'active' ? 'bg-emerald-500' : 'bg-gray-300'">
                                        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                                              :class="formData.status === 'active' ? 'translate-x-6' : 'translate-x-1'"></span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="bg-indigo-900 rounded-3xl p-8 text-white shadow-xl shadow-indigo-200">
                            <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center mb-6">
                                <i class="ri-lightbulb-line text-2xl text-indigo-200"></i>
                            </div>
                            <h3 class="text-lg font-bold mb-2">Pro Tip</h3>
                            <p class="text-indigo-200 text-sm leading-relaxed">Updating a candidate's position or partylist will reflect immediately on the live election preview for voters.</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Success/Error Modals (simplified for brevity) -->
        <div x-show="showSuccess" x-cloak class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-[2.5rem] max-w-sm w-full p-10 text-center shadow-2xl">
                <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-3xl flex items-center justify-center mx-auto mb-6">
                    <i class="ri-checkbox-circle-fill text-5xl"></i>
                </div>
                <h3 class="text-2xl font-black text-gray-900 mb-2">Success!</h3>
                <p class="text-gray-500 font-medium mb-8">Candidate information has been updated successfully.</p>
                <button @click="window.location.href='{{ route('admin.candidates.index') }}'" class="w-full py-4 bg-gray-900 text-white rounded-2xl font-bold hover:bg-indigo-600 transition-all">
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
