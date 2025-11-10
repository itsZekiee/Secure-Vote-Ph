@extends('layouts.app-main-admin')

@section('content')
    <div x-data="{
            formData: {
                name: '',
                acronym: '',
                description: '',
                platform: '',
                logo: null,
                color: '#6366f1',
                organization_id: '',
                status: 'active'
            },
            errors: {},
            loading: false,
            showSuccessModal: false,
            logoPreview: null,

            progressPercent() {
                const fields = ['name','acronym','description','platform','organization_id','logo'];
                const filled = fields.reduce((acc, key) => acc + (this.formData[key] ? 1 : 0), 0);
                return Math.round((filled / fields.length) * 100);
            },

            submitForm() {
                this.loading = true;
                this.errors = {};

                const formData = new FormData();
                formData.append('name', this.formData.name);
                formData.append('acronym', this.formData.acronym);
                formData.append('description', this.formData.description);
                formData.append('platform', this.formData.platform);
                formData.append('color', this.formData.color);
                formData.append('organization_id', this.formData.organization_id);
                formData.append('status', this.formData.status);

                if (this.formData.logo) {
                    formData.append('logo', this.formData.logo);
                }

                formData.append('_token', document.querySelector('input[name=_token]').value);

                fetch('{{ route('admin.partylists.store') }}', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.showSuccessModal = true;
                        this.resetFormData();
                    } else {
                        this.errors = data.errors || {};
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                })
                .finally(() => {
                    this.loading = false;
                });
            },

            resetFormData() {
                this.formData = {
                    name: '',
                    acronym: '',
                    description: '',
                    platform: '',
                    logo: null,
                    color: '#6366f1',
                    organization_id: '',
                    status: 'active'
                };
                this.logoPreview = null;
                this.errors = {};
            },

            handleLogoUpload(event) {
                const file = event.target.files[0];
                if (file) {
                    this.formData.logo = file;
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.logoPreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            }
         }"
         class="min-h-screen bg-gradient-to-b from-slate-50 to-white">

        <x-admin-sidebar />

        <main class="flex-1">
            <!-- Page header -->
            <div class="px-8 pt-8 pb-6 bg-gradient-to-r from-white via-slate-50 to-white border-b border-gray-100">
                <div class="max-w-7xl mx-auto">
                    <div class="flex items-start justify-between space-x-6">
                        <div>
                            <nav class="text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
                                <a href="{{ route('admin.dashboard') }}" class="hover:underline">Admin</a>
                                <span class="mx-2">›</span>
                                <a href="{{ route('admin.partylists.index') }}" class="hover:underline">Party Lists</a>
                                <span class="mx-2">›</span>
                                <span class="text-gray-700">Create</span>
                            </nav>

                            <div class="flex items-center space-x-4">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-600 to-indigo-600 flex items-center justify-center shadow-lg">
                                    <i class="ri-team-line text-white text-2xl"></i>
                                </div>
                                <div>
                                    <h1 class="text-3xl font-extrabold text-gray-900">Create Party List</h1>
                                    <p class="text-sm text-gray-600 mt-1">Add list details, upload logo, and assign to an organization.</p>
                                </div>
                            </div>

                        </div>

                        <div class="flex items-center space-x-3">
                            <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-200 rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-100">
                                <i class="ri-save-line mr-2"></i> Save Draft
                            </button>
                            <button type="button" @click="resetFormData()" class="inline-flex items-center px-4 py-2 border border-gray-200 rounded-lg bg-white text-gray-700 hover:bg-gray-50">
                                <i class="ri-refresh-line mr-2"></i> Reset
                            </button>
                        </div>
                    </div>

                    <!-- Stepper -->
                    <div class="mt-6">
                        <div class="flex items-center space-x-6 text-sm">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold">1</div>
                                <div class="text-gray-700 font-medium">Basic Information</div>
                            </div>
                            <div class="flex-1 border-t border-gray-200"></div>
                            <div class="flex items-center space-x-3 text-gray-400">
                                <div class="w-8 h-8 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center">2</div>
                                <div>Review & Submit</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content area -->
            <div class="max-w-7xl mx-auto px-8 py-10 grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Left: Form -->
                <section class="lg:col-span-8">
                    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
                        <div class="px-8 py-6 border-b border-gray-100 bg-gradient-to-r from-indigo-50 via-white to-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900">Partylist Details</h2>
                                    <p class="text-sm text-gray-600 mt-1">Provide basic information about the party list.</p>
                                </div>
                                <div class="text-sm text-gray-500">
                                    Required fields are marked with <span class="text-red-500">*</span>
                                </div>
                            </div>
                        </div>

                        <form @submit.prevent="submitForm()" class="p-8 space-y-6">
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Party Name -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Party Name <span class="text-red-500">*</span></label>
                                    <input type="text"
                                           x-model="formData.name"
                                           placeholder="Enter party name"
                                           class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                                    <div x-show="errors.name" class="text-red-500 text-sm mt-1" x-text="errors.name?.[0]"></div>
                                </div>

                                <!-- Acronym -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Acronym</label>
                                    <input type="text"
                                           x-model="formData.acronym"
                                           placeholder="e.g., ABC"
                                           class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                                    <div x-show="errors.acronym" class="text-red-500 text-sm mt-1" x-text="errors.acronym?.[0]"></div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Description <span class="text-sm text-gray-400">(Optional)</span></label>
                                <textarea x-model="formData.description"
                                          placeholder="Describe mission, goals and activities"
                                          rows="5"
                                          class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white resize-none"></textarea>
                                <div class="flex items-center justify-between mt-2 text-xs text-gray-400">
                                    <div>
                                        <i class="ri-lightbulb-line text-amber-400 mr-1"></i>
                                        Help users understand the party's purpose.
                                    </div>
                                    <div x-text="(formData.description || '').length + '/500'"></div>
                                </div>
                                <div x-show="errors.description" class="text-red-500 text-sm mt-1" x-text="errors.description?.[0]"></div>
                            </div>

                            <!-- Platform -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Platform</label>
                                <textarea x-model="formData.platform"
                                          placeholder="Key positions or platform highlights"
                                          rows="4"
                                          class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white"></textarea>
                                <div x-show="errors.platform" class="text-red-500 text-sm mt-1" x-text="errors.platform?.[0]"></div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Logo Upload -->
                                <div class="md:col-span-1">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Logo</label>
                                    <div class="border-2 border-dashed border-gray-200 rounded-lg p-4 text-center">
                                        <div x-show="!logoPreview" class="space-y-3">
                                            <div class="w-20 h-20 rounded-xl bg-gray-50 mx-auto flex items-center justify-center">
                                                <i class="ri-image-line text-gray-400 text-2xl"></i>
                                            </div>
                                            <div class="text-sm text-gray-600">PNG, JPG up to 2MB</div>
                                            <input type="file" @change="handleLogoUpload($event)" accept="image/*" class="hidden" id="logo-upload">
                                            <label for="logo-upload" class="mt-2 inline-flex items-center px-3 py-2 bg-indigo-600 text-white text-sm rounded-lg cursor-pointer hover:bg-indigo-700">Upload</label>
                                        </div>

                                        <div x-show="logoPreview" class="relative">
                                            <img :src="logoPreview" alt="Logo preview" class="mx-auto w-28 h-28 object-cover rounded-lg">
                                            <button type="button" @click="logoPreview = null; formData.logo = null" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center">×</button>
                                        </div>
                                    </div>
                                    <div x-show="errors.logo" class="text-red-500 text-sm mt-2" x-text="errors.logo?.[0]"></div>
                                </div>

                                <!-- Color -->
                                <div class="md:col-span-1">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Party Color</label>
                                    <div class="flex items-center space-x-3">
                                        <input type="color" x-model="formData.color" class="w-14 h-10 p-0 border-0 rounded-md" />
                                        <div class="text-sm text-gray-600">Selected: <span class="font-medium" x-text="formData.color"></span></div>
                                    </div>
                                    <div x-show="errors.color" class="text-red-500 text-sm mt-2" x-text="errors.color?.[0]"></div>
                                </div>

                                <!-- Organization -->
                                <div class="md:col-span-1">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Organization <span class="text-red-500">*</span></label>
                                    <select x-model="formData.organization_id" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white">
                                        <option value="">Select Organization</option>
                                        @foreach($organizations as $organization)
                                            <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                                        @endforeach
                                    </select>
                                    <div x-show="errors.organization_id" class="text-red-500 text-sm mt-2" x-text="errors.organization_id?.[0]"></div>
                                </div>
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                                <select x-model="formData.status" class="w-full md:w-56 px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white">
                                    <option value="active">Active</option>
                                    <option value="pending">Pending</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <div x-show="errors.status" class="text-red-500 text-sm mt-1" x-text="errors.status?.[0]"></div>
                            </div>

                            <!-- Action Row -->
                            <div class="flex items-center justify-between pt-6 border-t border-gray-100">
                                <a href="{{ route('admin.partylists.index') }}" class="px-6 py-3 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50">Cancel</a>
                                <div class="flex items-center space-x-3">
                                    <button type="button" @click="resetFormData()" class="px-6 py-3 border border-gray-200 bg-white text-gray-700 rounded-lg hover:bg-gray-50">Reset</button>
                                    <button type="submit" :disabled="loading" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-60">
                                        <span x-show="!loading">Create Party List</span>
                                        <span x-show="loading">Creating...</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </section>

                <!-- Right: Sidebar -->
                <aside class="lg:col-span-4 space-y-6">
                    <!-- Progress -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Progress</h3>
                        <div class="text-sm text-gray-500 mb-4">Form completion</div>
                        <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                            <div class="h-3 bg-gradient-to-r from-indigo-500 to-purple-500" :style="'width: ' + progressPercent() + '%'"></div>
                        </div>
                        <div class="flex items-center justify-between mt-3 text-xs text-gray-500">
                            <span>Completed</span>
                            <span x-text="progressPercent() + '%'"></span>
                        </div>
                    </div>

                    <!-- Pro Tips -->
                    <div class="bg-gradient-to-br from-indigo-50/60 to-white rounded-2xl border border-gray-100 p-6">
                        <h4 class="text-md font-semibold text-gray-900 mb-3">Pro Tips</h4>
                        <ul class="space-y-3 text-sm text-gray-600">
                            <li class="flex items-start space-x-3">
                                <i class="ri-check-line text-green-500 mt-1"></i>
                                <span>Use a clear, descriptive name for the party.</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <i class="ri-check-line text-green-500 mt-1"></i>
                                <span>Attach a high-quality logo for recognition.</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <i class="ri-check-line text-green-500 mt-1"></i>
                                <span>Assign the correct organization for accurate grouping.</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h4 class="text-md font-semibold text-gray-900 mb-4">Quick Actions</h4>
                        <div class="space-y-3">
                            <button type="button" class="w-full inline-flex items-center justify-center px-4 py-3 bg-amber-50 border border-amber-200 rounded-lg text-amber-700 hover:bg-amber-100">
                                <i class="ri-save-line mr-2"></i> Save as Draft
                            </button>
                            <a href="{{ route('admin.partylists.index') }}" class="w-full inline-flex items-center justify-center px-4 py-3 bg-white border border-gray-200 rounded-lg text-gray-700 hover:bg-gray-50">
                                <i class="ri-arrow-left-line mr-2"></i> Back to List
                            </a>
                        </div>
                    </div>
                </aside>
            </div>

            <!-- Success Modal -->
            <div x-show="showSuccessModal"
                 x-transition
                 class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50"
                 @click.self="showSuccessModal = false">
                <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="ri-check-line text-green-600 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Party List Created</h3>
                        <p class="text-sm text-gray-600 mb-6">The new party list has been successfully created.</p>
                        <div class="flex items-center gap-3">
                            <button @click="showSuccessModal = false; resetFormData()" class="flex-1 px-4 py-2 bg-white border border-gray-200 rounded-lg text-gray-700 hover:bg-gray-50">Create Another</button>
                            <a href="{{ route('admin.partylists.index') }}" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-center">View List</a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection
