@extends('layouts.app-main-admin')

@section('content')
    <div x-data="candidateData()" class="min-h-screen bg-gray-50 flex">
        <x-admin-sidebar />

        <main class="flex-1 ">
            <!-- Top Navigation Bar -->
            <div class="bg-white border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3 text-sm text-gray-600">
                            <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-600 transition-colors">Dashboard</a>
                            <i class="ri-arrow-right-s-line text-xs"></i>
                            <a href="{{ route('admin.candidates.index') }}" class="hover:text-indigo-600 transition-colors">Candidates</a>
                            <i class="ri-arrow-right-s-line text-xs"></i>
                            <span class="font-medium text-gray-900">Create New</span>
                        </div>
                        <button type="button" @click="resetForm()" class="text-sm text-gray-600 hover:text-indigo-600 flex items-center gap-2 transition-colors">
                            <i class="ri-refresh-line"></i>
                            Reset
                        </button>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-6 py-4">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Header -->
                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-indigo-600 flex items-center justify-center flex-shrink-0">
                                <i class="ri-user-add-line text-white text-2xl"></i>
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900">Create Candidate</h1>
                                <p class="text-gray-600 mt-1">Set up a new candidate to participate in elections</p>
                            </div>
                        </div>

                        <!-- Step Indicator -->
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-sm font-semibold">1</div>
                                <span class="text-sm font-medium text-gray-900">Basic Information</span>
                            </div>
                            <div class="flex items-center gap-3 opacity-40">
                                <div class="w-8 h-8 rounded-lg bg-gray-200 text-gray-600 flex items-center justify-center text-sm font-semibold">2</div>
                                <span class="text-sm font-medium text-gray-600">Review & Submit</span>
                            </div>
                        </div>

                        <form @submit.prevent="submitForm()" class="space-y-6">
                            <!-- Candidate Details Card -->
                            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                                <div class="p-6 bg-indigo-50 border-b border-indigo-100">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center">
                                            <i class="ri-user-settings-line text-white text-lg"></i>
                                        </div>
                                        <div>
                                            <h2 class="text-lg font-bold text-gray-900">Candidate Details</h2>
                                            <p class="text-sm text-gray-600">Basic information about the candidate</p>
                                        </div>
                                    </div>
                                </div>

                                @csrf
                                <div class="p-6 space-y-6">
                                    <!-- User Inputs (replaces Select User) -->
                                    <div>
                                        <label class="flex items-center gap-2 text-sm font-semibold text-gray-900 mb-2">
                                            <i class="ri-user-line text-indigo-600"></i>
                                            Candidate Name
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text"
                                               x-model="formData.user_name"
                                               placeholder="Enter full name..."
                                               class="w-full pl-4 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                                        <div x-show="errors.user_name" class="mt-2 text-sm text-red-600 flex items-center gap-2">
                                            <i class="ri-error-warning-line"></i>
                                            <span x-text="errors.user_name?.[0]"></span>
                                        </div>

                                        <label class="flex items-center gap-2 text-sm font-semibold text-gray-900 mt-4 mb-2">
                                            <i class="ri-mail-line text-indigo-600"></i>
                                            Candidate Email
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <input type="email"
                                               x-model="formData.user_email"
                                               placeholder="Enter email address..."
                                               class="w-full pl-4 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                                        <div x-show="errors.user_email" class="mt-2 text-sm text-red-600 flex items-center gap-2">
                                            <i class="ri-error-warning-line"></i>
                                            <span x-text="errors.user_email?.[0]"></span>
                                        </div>
                                    </div>

                                    <!-- Organization -->
                                    <div>
                                        <label class="flex items-center gap-2 text-sm font-semibold text-gray-900 mb-2">
                                            <i class="ri-building-line text-indigo-600"></i>
                                            Organization
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <select x-model="formData.organization_id"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all appearance-none bg-white">
                                            <option value="">Select organization</option>
                                            <template x-for="org in organizations" :key="org.id">
                                                <option :value="org.id" x-text="org.name"></option>
                                            </template>
                                        </select>
                                        <div x-show="errors.organization_id" class="mt-2 text-sm text-red-600 flex items-center gap-2">
                                            <i class="ri-error-warning-line"></i>
                                            <span x-text="errors.organization_id?.[0]"></span>
                                        </div>
                                    </div>

                                    <!-- Election & Partylist Grid -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Election (optional, enhanced UI) -->
                                        <div>
                                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-900 mb-2">
                                                <i class="ri-calendar-event-line text-indigo-600"></i>
                                                Election
                                                <span class="text-xs text-gray-500 font-normal">(Optional)</span>
                                            </label>
                                            <div class="relative">
                                                <select x-model="formData.election_id"
                                                        class="w-full pl-10 pr-10 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all appearance-none bg-white">
                                                    <option value="">No election selected</option>
                                                    <template x-for="e in elections" :key="e.id">
                                                        <option :value="e.id" x-text="e.title"></option>
                                                    </template>
                                                </select>
                                                <i class="ri-calendar-event-line absolute left-3 top-1/2 -translate-y-1/2 text-indigo-600"></i>
                                                <i class="ri-arrow-down-s-line absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                            </div>
                                            <div x-show="errors.election_id" class="mt-2 text-sm text-red-600 flex items-center gap-2">
                                                <i class="ri-error-warning-line"></i>
                                                <span x-text="errors.election_id?.[0]"></span>
                                            </div>
                                        </div>

                                        <!-- Partylist -->
                                        <div>
                                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-900 mb-2">
                                                <i class="ri-team-line text-indigo-600"></i>
                                                Partylist
                                                <span class="text-xs text-gray-500 font-normal">(Optional)</span>
                                            </label>
                                            <select x-model="formData.partylist_id"
                                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all appearance-none bg-white">
                                                <option value="">Independent</option>
                                                <template x-for="p in filteredPartylists" :key="p.id">
                                                    <option :value="p.id" x-text="p.name"></option>
                                                </template>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Position -->
                                    <div>
                                        <label class="flex items-center gap-2 text-sm font-semibold text-gray-900 mb-2">
                                            <i class="ri-briefcase-line text-indigo-600"></i>
                                            Position
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <select x-model="formData.position_id"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all appearance-none bg-white">
                                            <option value="">Select position</option>
                                            <optgroup label="Existing Positions">
                                                <template x-for="p in existingPositions" :key="p.id">
                                                    <option :value="p.id" x-text="p.name"></option>
                                                </template>
                                            </optgroup>
                                            <option value="new">✨ Create New Position</option>
                                            <optgroup label="Common Positions">
                                                <template x-for="cp in commonPositions" :key="cp">
                                                    <option :value="'preset:' + cp" x-text="cp"></option>
                                                </template>
                                            </optgroup>
                                        </select>

                                        <div x-show="formData.position_id === 'new' || String(formData.position_id).startsWith('preset:')"
                                             x-transition
                                             class="mt-3">
                                            <input type="text"
                                                   x-model="formData.new_position_name"
                                                   placeholder="Enter position name..."
                                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                                            <div x-show="errors.new_position_name" class="mt-2 text-sm text-red-600 flex items-center gap-2">
                                                <i class="ri-error-warning-line"></i>
                                                <span x-text="errors.new_position_name?.[0]"></span>
                                            </div>
                                        </div>

                                        <div x-show="errors.position_id" class="mt-2 text-sm text-red-600 flex items-center gap-2">
                                            <i class="ri-error-warning-line"></i>
                                            <span x-text="errors.position_id?.[0]"></span>
                                        </div>
                                    </div>

                                    <!-- Platform -->
                                    <div>
                                        <label class="flex items-center gap-2 text-sm font-semibold text-gray-900 mb-2">
                                            <i class="ri-file-text-line text-indigo-600"></i>
                                            Description
                                            <span class="text-xs text-gray-500 font-normal">(Optional)</span>
                                        </label>
                                        <textarea x-model="formData.platform"
                                                  rows="4"
                                                  maxlength="500"
                                                  placeholder="Describe the candidate's mission, goals, and activities..."
                                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all resize-none"></textarea>
                                        <div class="flex items-center justify-between mt-2">
                                            <p class="text-xs text-amber-600 flex items-center gap-1">
                                                <i class="ri-lightbulb-line"></i>
                                                Help voters understand the candidate's vision and core values
                                            </p>
                                            <span class="text-xs text-gray-500" x-text="(formData.platform?.length || 0) + '/500'"></span>
                                        </div>
                                    </div>

                                    <!-- Status -->
                                    <div>
                                        <label class="flex items-center gap-2 text-sm font-semibold text-gray-900 mb-2">
                                            <i class="ri-toggle-line text-indigo-600"></i>
                                            Candidate Status
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <select x-model="formData.status"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all appearance-none bg-white">
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information Card -->
                            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                                <div class="p-6 bg-emerald-50 border-b border-emerald-100">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center">
                                            <i class="ri-image-line text-white text-lg"></i>
                                        </div>
                                        <div>
                                            <h2 class="text-lg font-bold text-gray-900">Candidate Photo</h2>
                                            <p class="text-sm text-gray-600">Upload a professional photo of the candidate</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-6">
                                    <div class="relative">
                                        <label class="block w-full h-48 border-2 border-dashed border-gray-300 rounded-xl hover:border-indigo-400 transition-all cursor-pointer overflow-hidden">
                                            <div x-show="!photoPreview" class="h-full flex flex-col items-center justify-center text-gray-500">
                                                <i class="ri-upload-cloud-2-line text-4xl mb-2"></i>
                                                <span class="text-sm font-medium">Click to upload photo</span>
                                                <span class="text-xs text-gray-400 mt-1">PNG, JPG up to 3MB</span>
                                            </div>
                                            <img x-show="photoPreview" :src="photoPreview" class="w-full h-full object-cover">
                                            <input type="file" @change="handlePhotoUpload($event)" accept="image/*" class="hidden">
                                        </label>
                                        <button x-show="photoPreview" @click="photoPreview=null; formData.photo=null;" type="button"
                                                class="absolute top-2 right-2 w-8 h-8 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                                            <i class="ri-close-line"></i>
                                        </button>
                                    </div>
                                    <p class="mt-3 text-xs text-gray-500 flex items-center gap-1">
                                        <i class="ri-information-line"></i>
                                        This photo will be used for official communications and notifications
                                    </p>
                                    <div x-show="errors.photo" class="mt-2 text-sm text-red-600 flex items-center gap-2">
                                        <i class="ri-error-warning-line"></i>
                                        <span x-text="errors.photo?.[0]"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Bottom Action Bar -->
                            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                        <i class="ri-arrow-right-line text-indigo-600 text-lg"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-base font-bold text-gray-900">Continue to Review</h3>
                                        <p class="text-sm text-gray-600 mt-1">Proceed to review your information before creating</p>
                                    </div>
                                    <div class="flex gap-3">
                                        <a href="{{ route('admin.candidates.index') }}"
                                           class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-all font-medium flex items-center gap-2">
                                            <i class="ri-arrow-left-line"></i>
                                            Cancel
                                        </a>
                                        <button type="submit"
                                                :disabled="loading"
                                                class="px-8 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 disabled:opacity-50 transition-all font-medium flex items-center gap-2">
                                            <template x-if="loading">
                                                <i class="ri-loader-4-line animate-spin"></i>
                                            </template>
                                            <template x-if="!loading">
                                                <i class="ri-arrow-right-line"></i>
                                            </template>
                                            <span x-text="loading ? 'Creating...' : 'Review & Submit'"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Right Sidebar -->
                    <div class="lg:col-span-1 space-y-6">
                        <!-- Progress Card -->
                        <div class="bg-white rounded-2xl border border-gray-200 p-6">
                            <h3 class="text-base font-bold text-gray-900 mb-4">Progress</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Form Completion</span>
                                    <span class="font-semibold text-gray-900" x-text="completionPercentage + '%'"></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300"
                                         :style="'width: ' + completionPercentage + '%'"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Pro Tips Card -->
                        <div class="bg-blue-50 rounded-2xl border border-blue-200 p-6">
                            <div class="flex items-center gap-2 mb-4">
                                <i class="ri-lightbulb-flash-line text-blue-600 text-xl"></i>
                                <h3 class="text-base font-bold text-gray-900">Pro Tips</h3>
                            </div>
                            <ul class="space-y-3 text-sm text-gray-700">
                                <li class="flex items-start gap-2">
                                    <i class="ri-checkbox-circle-line text-blue-600 mt-0.5 flex-shrink-0"></i>
                                    <span>Use a clear, descriptive name for your organization</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="ri-checkbox-circle-line text-blue-600 mt-0.5 flex-shrink-0"></i>
                                    <span>Provide a professional email address for communications</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="ri-checkbox-circle-line text-blue-600 mt-0.5 flex-shrink-0"></i>
                                    <span>Write a compelling description to attract members</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Quick Actions Card -->
                        <div class="bg-white rounded-2xl border border-gray-200 p-6">
                            <h3 class="text-base font-bold text-gray-900 mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                <button type="button" @click="saveDraft()"
                                        class="w-full px-4 py-3 bg-amber-50 border border-amber-200 text-amber-700 rounded-xl hover:bg-amber-100 transition-all font-medium text-sm flex items-center justify-center gap-2">
                                    <i class="ri-save-line"></i>
                                    Save as Draft
                                </button>
                                <button type="button" @click="loadDraft()"
                                        class="w-full px-4 py-3 bg-blue-50 border border-blue-200 text-blue-700 rounded-xl hover:bg-blue-100 transition-all font-medium text-sm flex items-center justify-center gap-2">
                                    <i class="ri-download-line"></i>
                                    Load Draft
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success Modal -->
            <div x-show="showSuccess" x-cloak
                 class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50"
                 @click.self="showSuccess = false">
                <div @click.away="showSuccess = false"
                     class="bg-white rounded-2xl max-w-md w-full p-8 shadow-xl">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="ri-checkbox-circle-fill text-emerald-600 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2" x-text="successTitle"></h3>
                        <p class="text-gray-600 mb-6" x-text="successMessage"></p>
                        <div class="flex gap-3">
                            <button @click="window.location.href='{{ route('admin.candidates.index') }}'"
                                    class="flex-1 px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all font-medium">
                                View Candidates
                            </button>
                            <button @click="resetFormAfterSuccess()"
                                    class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all font-medium">
                                Create Another
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Error Modal -->
            <div x-show="showError" x-cloak
                 class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50"
                 @click.self="showError = false">
                <div @click.away="showError = false"
                     class="bg-white rounded-2xl max-w-md w-full p-8 shadow-xl">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="ri-close-circle-fill text-red-600 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Unable to Create</h3>
                        <p class="text-gray-600 mb-6" x-text="errorMessage"></p>
                        <button @click="showError = false"
                                class="w-full px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all font-medium">
                            Close & Retry
                        </button>
                    </div>
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
                } else if (this.formData.position_id === 'new' && !this.formData.new_position_name.trim()) {
                    this.errors.new_position_name = ['Enter position name'];
                } else if (String(this.formData.position_id).startsWith('preset:') && !this.formData.new_position_name.trim()) {
                    this.errors.new_position_name = ['Position name required'];
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
                this.loading = true;
                this.errors = {};

                const formData = new FormData();
                formData.append('_token', document.querySelector('input[name=_token]').value);

                // send user name/email so backend can find-or-create the user
                formData.append('user_name', this.formData.user_name.trim());
                formData.append('user_email', this.formData.user_email.trim());

                formData.append('organization_id', this.formData.organization_id);
                formData.append('election_id', this.formData.election_id || '');

                if (String(this.formData.position_id).startsWith('preset:') || this.formData.position_id === 'new') {
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
