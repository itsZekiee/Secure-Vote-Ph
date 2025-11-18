@extends('layouts.app-main-admin')

@section('content')
    <div x-data="{
            formData: {
                name: '',
                description: '',
                contact_email: '',
                contact_phone: '',
                status: 'active'
            },
            errors: {},
            loading: false,
            currentStep: 1,
            validationPassed: false,

            nextStep() {
                if (this.validateForm()) {
                    this.currentStep = 2;
                }
            },

            prevStep() {
                this.currentStep = 1;
            },

            submitForm() {
                this.loading = true;
                this.errors = {};

                const formData = new FormData();
                formData.append('name', this.formData.name);
                formData.append('description', this.formData.description);
                formData.append('contact_email', this.formData.contact_email);
                formData.append('contact_phone', this.formData.contact_phone);
                formData.append('status', this.formData.status);
                formData.append('_token', document.querySelector('input[name=_token]').value);

                fetch('{{ route('admin.organizations.store') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = '{{ route('admin.organizations.index') }}';
                    } else {
                        this.errors = data.errors || {};
                        this.currentStep = 1; // Go back to form if error
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.showToast('An error occurred while creating the organization.', 'error');
                    this.currentStep = 1;
                })
                .finally(() => {
                    this.loading = false;
                });
            },

            validateForm() {
                this.validationPassed = this.formData.name && this.formData.contact_email;
                return this.validationPassed;
            },

            saveDraft() {
                localStorage.setItem('organization_draft', JSON.stringify(this.formData));
                this.showToast('Draft saved successfully', 'success');
            },

            resetForm() {
                this.formData = {
                    name: '',
                    description: '',
                    contact_email: '',
                    contact_phone: '',
                    status: 'active'
                };
                this.errors = {};
                localStorage.removeItem('organization_draft');
                this.showToast('Form reset successfully', 'info');
            },

            loadDraft() {
                const draft = localStorage.getItem('organization_draft');
                if (draft) {
                    this.formData = JSON.parse(draft);
                    this.showToast('Draft loaded successfully', 'info');
                }
            },

            showToast(message, type = 'info') {
                const toast = document.createElement('div');
                const colors = {
                    success: 'bg-emerald-500 border-emerald-200',
                    error: 'bg-red-500 border-red-200',
                    info: 'bg-blue-500 border-blue-200',
                    warning: 'bg-amber-500 border-amber-200'
                };
                const icons = {
                    success: 'ri-check-circle-line',
                    error: 'ri-error-warning-line',
                    info: 'ri-information-line',
                    warning: 'ri-alert-line'
                };

                toast.className = `fixed top-6 right-6 ${colors[type]} text-white px-6 py-4 rounded-2xl shadow-xl border-2 backdrop-blur-sm z-50 transform transition-all duration-500 translate-x-full`;
                toast.innerHTML = `
                    <div class='flex items-center space-x-3'>
                        <i class='${icons[type]} text-xl'></i>
                        <span class='font-semibold'>${message}</span>
                    </div>
                `;
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.style.transform = 'translateX(0)';
                }, 100);

                setTimeout(() => {
                    toast.style.transform = 'translateX(100%)';
                    setTimeout(() => document.body.removeChild(toast), 500);
                }, 4000);
            }
         }"
         x-init="
            loadDraft();
            $watch('formData', () => validateForm(), { deep: true });
         "
         class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/40 to-indigo-50/30">

        <x-admin-sidebar />

        <!-- Main Content -->
        <main class="flex-1">
            <!-- Top Navigation Bar -->
            <div class="bg-white/80 backdrop-blur-xl border-b border-gray-200/50 sticky top-0 z-40">
                <div class="px-8 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('admin.organizations.index') }}"
                               class="group flex items-center justify-center w-10 h-10 rounded-xl bg-gray-100 hover:bg-blue-50 transition-all duration-200 border border-gray-200 hover:border-blue-200">
                                <i class="ri-arrow-left-line text-gray-600 group-hover:text-blue-600 transition-colors"></i>
                            </a>
                            <div class="h-8 w-px bg-gray-200"></div>
                            <nav class="flex items-center space-x-2 text-sm">
                                <span class="text-gray-500">Admin</span>
                                <i class="ri-arrow-right-s-line text-gray-400"></i>
                                <span class="text-gray-500">Organizations</span>
                                <i class="ri-arrow-right-s-line text-gray-400"></i>
                                <span class="text-gray-900 font-semibold">Create New</span>
                            </nav>
                        </div>

                        <div class="flex items-center space-x-3">
                            <button @click="saveDraft()"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-amber-700 bg-amber-50 border border-amber-200 rounded-lg hover:bg-amber-100 transition-all duration-200">
                                <i class="ri-save-3-line mr-2"></i>
                                Save Draft
                            </button>
                            <button @click="resetForm()"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-all duration-200">
                                <i class="ri-refresh-line mr-2"></i>
                                Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Header Section -->
            <div class="px-8 py-8">
                <div class="max-w-6xl mx-auto">
                    <div class="flex items-start justify-between mb-8">
                        <div class="flex items-start space-x-6">
                            <div class="flex-shrink-0">
                                <div class="w-16 h-16 bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600 rounded-3xl flex items-center justify-center shadow-lg shadow-blue-500/25">
                                    <i class="ri-building-add-line text-white text-2xl"></i>
                                </div>
                            </div>
                            <div>
                                <h1 class="text-4xl font-bold text-gray-900 mb-2">Create Organization</h1>
                                <p class="text-lg text-gray-600 mb-4">Set up a new organization to manage members, activities, and resources</p>

                                <!-- Progress Steps -->
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold">
                                            1
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">Basic Information</span>
                                    </div>
                                    <div class="w-12 h-px bg-gray-200"></div>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-8 h-8 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center text-sm font-bold">
                                            2
                                        </div>
                                        <span class="text-sm text-gray-500">Review & Submit</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Container -->
                    <div class="grid grid-cols-12 gap-8">
                        <!-- Main Form -->
                        <div class="col-span-8">
                            <!-- Step 1: Basic Information -->
                            <div x-show="currentStep === 1" class="space-y-8">
                                <form @submit.prevent="nextStep()" class="space-y-8">
                                @csrf

                                <!-- Organization Details Card -->
                                <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
                                    <div class="px-8 py-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200/60">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                                                <i class="ri-building-4-line text-white text-lg"></i>
                                            </div>
                                            <div>
                                                <h3 class="text-xl font-bold text-gray-900">Organization Details</h3>
                                                <p class="text-gray-600 text-sm mt-0.5">Basic information about your organization</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-8 space-y-8">
                                        <!-- Organization Name -->
                                        <div class="group">
                                            <label class="block text-sm font-bold text-gray-800 mb-3">
                                                <i class="ri-building-line text-blue-600 mr-2"></i>
                                                Organization Name
                                                <span class="text-red-500 ml-1">*</span>
                                            </label>
                                            <div class="relative">
                                                <input type="text"
                                                       x-model="formData.name"
                                                       class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-gray-900 placeholder-gray-400 text-lg"
                                                       :class="errors.name ? 'border-red-300 bg-red-50' : 'border-gray-200 focus:border-blue-500'"
                                                       placeholder="Enter organization name..."
                                                       required>
                                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center">
                                                    <div x-show="formData.name" class="text-green-500">
                                                        <i class="ri-check-line"></i>
                                                    </div>
                                                </div>
                                                <div x-show="errors.name" class="absolute -bottom-6 left-0">
                                                    <span class="text-red-500 text-sm font-medium" x-text="errors.name?.[0]"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Description -->
                                        <div class="group">
                                            <label class="block text-sm font-bold text-gray-800 mb-3">
                                                <i class="ri-file-text-line text-blue-600 mr-2"></i>
                                                Description
                                                <span class="text-gray-500 text-xs font-normal ml-2">(Optional)</span>
                                            </label>
                                            <div class="relative">
                                                <textarea x-model="formData.description"
                                                          rows="4"
                                                          class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-gray-900 placeholder-gray-400 resize-none"
                                                          :class="errors.description ? 'border-red-300 bg-red-50' : 'border-gray-200'"
                                                          placeholder="Describe the organization's mission, goals, and activities..."></textarea>
                                                <div class="absolute bottom-3 right-3 text-xs text-gray-400">
                                                    <span x-text="formData.description.length"></span>/500
                                                </div>
                                                <div x-show="errors.description" class="absolute -bottom-6 left-0">
                                                    <span class="text-red-500 text-sm font-medium" x-text="errors.description?.[0]"></span>
                                                </div>
                                            </div>
                                            <p class="mt-3 text-sm text-gray-500 flex items-center">
                                                <i class="ri-lightbulb-line mr-2 text-amber-500"></i>
                                                Help users understand what your organization does and its core values
                                            </p>
                                        </div>

                                        <!-- Status -->
                                        <div class="group">
                                            <label class="block text-sm font-bold text-gray-800 mb-3">
                                                <i class="ri-toggle-line text-blue-600 mr-2"></i>
                                                Organization Status
                                                <span class="text-red-500 ml-1">*</span>
                                            </label>
                                            <div class="relative">
                                                <select x-model="formData.status"
                                                        class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-gray-900 text-lg appearance-none cursor-pointer"
                                                        :class="errors.status ? 'border-red-300 bg-red-50' : 'border-gray-200'">
                                                    <option value="active" class="py-2">🟢 Active - Organization is operational</option>
                                                    <option value="inactive" class="py-2">🔴 Inactive - Temporarily disabled</option>
                                                    <option value="pending" class="py-2">🟡 Pending - Awaiting approval</option>
                                                </select>
                                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                                    <i class="ri-arrow-down-s-line text-gray-400"></i>
                                                </div>
                                                <div x-show="errors.status" class="absolute -bottom-6 left-0">
                                                    <span class="text-red-500 text-sm font-medium" x-text="errors.status?.[0]"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contact Information Card -->
                                <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
                                    <div class="px-8 py-6 bg-gradient-to-r from-emerald-50 to-teal-50 border-b border-gray-200/60">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-12 h-12 bg-gradient-to-br from-emerald-600 to-teal-600 rounded-xl flex items-center justify-center shadow-lg">
                                                <i class="ri-contacts-line text-white text-lg"></i>
                                            </div>
                                            <div>
                                                <h3 class="text-xl font-bold text-gray-900">Contact Information</h3>
                                                <p class="text-gray-600 text-sm mt-0.5">How to reach and communicate with the organization</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-8 space-y-8">
                                        <!-- Contact Email -->
                                        <div class="group">
                                            <label class="block text-sm font-bold text-gray-800 mb-3">
                                                <i class="ri-mail-line text-emerald-600 mr-2"></i>
                                                Primary Email Address
                                                <span class="text-red-500 ml-1">*</span>
                                            </label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                                    <i class="ri-at-line text-gray-400"></i>
                                                </div>
                                                <input type="email"
                                                       x-model="formData.contact_email"
                                                       class="w-full pl-12 pr-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 text-gray-900 placeholder-gray-400 text-lg"
                                                       :class="errors.contact_email ? 'border-red-300 bg-red-50' : 'border-gray-200'"
                                                       placeholder="contact@organization.com"
                                                       required>
                                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center">
                                                    <div x-show="formData.contact_email && formData.contact_email.includes('@')" class="text-green-500">
                                                        <i class="ri-check-line"></i>
                                                    </div>
                                                </div>
                                                <div x-show="errors.contact_email" class="absolute -bottom-6 left-0">
                                                    <span class="text-red-500 text-sm font-medium" x-text="errors.contact_email?.[0]"></span>
                                                </div>
                                            </div>
                                            <p class="mt-3 text-sm text-gray-500 flex items-center">
                                                <i class="ri-shield-check-line mr-2 text-emerald-500"></i>
                                                This email will be used for official communications and notifications
                                            </p>
                                        </div>

                                        <!-- Contact Phone -->
                                        <div class="group">
                                            <label class="block text-sm font-bold text-gray-800 mb-3">
                                                <i class="ri-phone-line text-emerald-600 mr-2"></i>
                                                Contact Phone Number
                                                <span class="text-gray-500 text-xs font-normal ml-2">(Optional)</span>
                                            </label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                                    <i class="ri-phone-line text-gray-400"></i>
                                                </div>
                                                <input type="tel"
                                                       x-model="formData.contact_phone"
                                                       class="w-full pl-12 pr-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 text-gray-900 placeholder-gray-400 text-lg"
                                                       :class="errors.contact_phone ? 'border-red-300 bg-red-50' : 'border-gray-200'"
                                                       placeholder="+1 (555) 123-4567">
                                                <div x-show="errors.contact_phone" class="absolute -bottom-6 left-0">
                                                    <span class="text-red-500 text-sm font-medium" x-text="errors.contact_phone?.[0]"></span>
                                                </div>
                                            </div>
                                            <p class="mt-3 text-sm text-gray-500 flex items-center">
                                                <i class="ri-information-line mr-2 text-blue-500"></i>
                                                Include country code for international numbers
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Next Step Actions -->
                                <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm p-8">
                                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-12 h-12 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-xl flex items-center justify-center">
                                                <i class="ri-arrow-right-line text-purple-600 text-lg"></i>
                                            </div>
                                            <div>
                                                <p class="text-lg font-bold text-gray-900">Continue to Review</p>
                                                <p class="text-gray-600">Proceed to review your information before creating</p>
                                            </div>
                                        </div>

                                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">
                                            <a href="{{ route('admin.organizations.index') }}"
                                               class="inline-flex items-center justify-center px-6 py-3 border-2 border-gray-200 rounded-xl text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-300 transition-all duration-200 font-semibold">
                                                <i class="ri-arrow-left-line mr-2"></i>
                                                Cancel
                                            </a>

                                            <button type="submit"
                                                    :disabled="!validationPassed"
                                                    class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 font-bold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                                                <i class="ri-arrow-right-line mr-2"></i>
                                                Next: Review & Submit
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                </form>
                            </div>

                            <!-- Step 2: Review & Submit -->
                            <div x-show="currentStep === 2" class="space-y-8">
                                <form @submit.prevent="submitForm()" class="space-y-8">
                                    <!-- Review Content -->
                                    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
                                        <div class="px-8 py-6 bg-gradient-to-r from-purple-50 to-indigo-50 border-b border-gray-200/60">
                                            <div class="flex items-center space-x-4">
                                                <div class="w-12 h-12 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                                                    <i class="ri-eye-line text-white text-lg"></i>
                                                </div>
                                                <div>
                                                    <h3 class="text-xl font-bold text-gray-900">Review Your Information</h3>
                                                    <p class="text-gray-600 text-sm mt-0.5">Please review all details before creating the organization</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="p-8 space-y-6">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                <div>
                                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Organization Name:</label>
                                                    <p x-text="formData.name" class="text-gray-900 bg-gray-50 px-4 py-3 rounded-lg border"></p>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status:</label>
                                                    <p x-text="formData.status" class="text-gray-900 bg-gray-50 px-4 py-3 rounded-lg border"></p>
                                                </div>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">Description:</label>
                                                <p x-text="formData.description || 'No description provided'" class="text-gray-900 bg-gray-50 px-4 py-3 rounded-lg border min-h-[60px]"></p>
                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                <div>
                                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Contact Email:</label>
                                                    <p x-text="formData.contact_email" class="text-gray-900 bg-gray-50 px-4 py-3 rounded-lg border"></p>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Contact Phone:</label>
                                                    <p x-text="formData.contact_phone || 'Not provided'" class="text-gray-900 bg-gray-50 px-4 py-3 rounded-lg border"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm p-8">
                                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                                            <div class="flex items-center space-x-4">
                                                <div class="w-12 h-12 bg-gradient-to-br from-green-100 to-emerald-100 rounded-xl flex items-center justify-center">
                                                    <i class="ri-check-double-line text-green-600 text-lg"></i>
                                                </div>
                                                <div>
                                                    <p class="text-lg font-bold text-gray-900">Ready to Create</p>
                                                    <p class="text-gray-600">Click create to add this organization to the system</p>
                                                </div>
                                            </div>

                                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">
                                                <button type="button" @click="prevStep()" class="inline-flex items-center justify-center px-6 py-3 border-2 border-gray-200 rounded-xl text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-300 transition-all duration-200 font-semibold">
                                                    <i class="ri-arrow-left-line mr-2"></i>
                                                    Back to Edit
                                                </button>

                                                <button type="submit" :disabled="loading" class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-200 font-bold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                                                    <span x-show="!loading">
                                                        <i class="ri-add-circle-line mr-2"></i>
                                                        Create Organization
                                                    </span>
                                                    <span x-show="loading">
                                                        <i class="ri-loader-4-line mr-2 animate-spin"></i>
                                                        Creating...
                                                    </span>
                                                </button>
                                            </div>
                                        </div>

                        <!-- Sidebar -->
                        <div class="col-span-4">
                            <div class="sticky top-32 space-y-6">
                                <!-- Progress Card -->
                                <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm p-6">
                                    <h4 class="text-lg font-bold text-gray-900 mb-4">Progress</h4>
                                    <div class="space-y-4">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-600">Form Completion</span>
                                            <span class="text-sm font-semibold text-gray-900" x-text="Math.round((Object.values(formData).filter(v => v !== '').length / Object.keys(formData).length) * 100) + '%'"></span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-gradient-to-r from-blue-500 to-indigo-500 h-2 rounded-full transition-all duration-300"
                                                 :style="`width: ${(Object.values(formData).filter(v => v !== '').length / Object.keys(formData).length) * 100}%`"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tips Card -->
                                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl border border-blue-200/50 p-6">
                                    <div class="flex items-center space-x-3 mb-4">
                                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <i class="ri-lightbulb-line text-blue-600"></i>
                                        </div>
                                        <h4 class="text-lg font-bold text-gray-900">Pro Tips</h4>
                                    </div>
                                    <ul class="space-y-3 text-sm text-gray-700">
                                        <li class="flex items-start space-x-2">
                                            <i class="ri-check-line text-green-500 mt-0.5 flex-shrink-0"></i>
                                            <span>Use a clear, descriptive name for your organization</span>
                                        </li>
                                        <li class="flex items-start space-x-2">
                                            <i class="ri-check-line text-green-500 mt-0.5 flex-shrink-0"></i>
                                            <span>Provide a professional email address for communications</span>
                                        </li>
                                        <li class="flex items-start space-x-2">
                                            <i class="ri-check-line text-green-500 mt-0.5 flex-shrink-0"></i>
                                            <span>Write a compelling description to attract members</span>
                                        </li>
                                    </ul>
                                </div>

                                <!-- Quick Actions -->
                                <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm p-6">
                                    <h4 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h4>
                                    <div class="space-y-3">
                                        <button @click="saveDraft()"
                                                class="w-full inline-flex items-center justify-center px-4 py-3 text-sm font-medium text-amber-700 bg-amber-50 border border-amber-200 rounded-xl hover:bg-amber-100 transition-all duration-200">
                                            <i class="ri-save-3-line mr-2"></i>
                                            Save as Draft
                                        </button>
                                        <button @click="loadDraft()"
                                                class="w-full inline-flex items-center justify-center px-4 py-3 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded-xl hover:bg-blue-100 transition-all duration-200">
                                            <i class="ri-download-line mr-2"></i>
                                            Load Draft
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </main>
    </div>
@endsection
