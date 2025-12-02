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
            showSuccessModal: false,
            showErrorModal: false,
            errorMessage: '',

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
                        this.showSuccessModal = true;
                        localStorage.removeItem('organization_draft');
                        setTimeout(() => {
                            window.location.href = '{{ route('admin.organizations.index') }}';
                        }, 2000);
                    } else {
                        this.errors = data.errors || {};
                        this.errorMessage = data.message || 'Failed to create organization. Please check your input and try again.';
                        this.showErrorModal = true;
                        this.currentStep = 1;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.errorMessage = 'An unexpected error occurred while creating the organization. Please try again later.';
                    this.showErrorModal = true;
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

            closeSuccessModal() {
                this.showSuccessModal = false;
                window.location.href = '{{ route('admin.organizations.index') }}';
            },

            closeErrorModal() {
                this.showErrorModal = false;
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
         class="flex min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/40 to-indigo-50/30">

        <x-admin-sidebar />

        <!-- Success Modal -->
        <div x-show="showSuccessModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div x-show="showSuccessModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 transform translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 transform translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="bg-white rounded-2xl shadow-2xl max-w-md mx-4 overflow-hidden">
                <div class="p-8 text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <i class="ri-check-line text-white text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Organization Created Successfully!</h3>
                    <p class="text-gray-600 mb-6">Your new organization has been created and is ready for use. You'll be redirected to the organizations list shortly.</p>
                    <div class="flex space-x-4">
                        <button @click="closeSuccessModal()"
                                class="flex-1 px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-200 font-semibold">
                            Continue
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error Modal -->
        <div x-show="showErrorModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div x-show="showErrorModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 transform translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 transform translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="bg-white rounded-2xl shadow-2xl max-w-md mx-4 overflow-hidden">
                <div class="p-8 text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-red-500 to-rose-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <i class="ri-error-warning-line text-white text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Creation Failed</h3>
                    <p class="text-gray-600 mb-6" x-text="errorMessage"></p>
                    <div class="flex space-x-4">
                        <button @click="closeErrorModal()"
                                class="flex-1 px-6 py-3 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-all duration-200 font-semibold">
                            Close
                        </button>
                        <button @click="closeErrorModal(); currentStep = 1;"
                                class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 font-semibold">
                            Try Again
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <main class="flex-1">
            <!-- Simplified Navigation Bar -->
            <div class="bg-gradient-to-r from-white/95 via-indigo-50/30 to-white/95 backdrop-blur-xl border-b border-gray-200/50 sticky top-0 z-40 shadow-sm">
                <div class="px-8 py-4">
                    <div class="flex items-center justify-between">
                        <!-- Left Section: Back Button & Breadcrumb -->
                        <div class="flex items-center space-x-4">
                            <!-- Simplified Back Button -->
                            <a href="{{ route('admin.organizations.index') }}"
                               class="flex items-center justify-center w-10 h-10 rounded-lg bg-gray-50 hover:bg-gray-100 transition-all duration-200 border border-gray-200 hover:border-gray-300">
                                <i class="ri-arrow-left-line text-gray-600"></i>
                            </a>

                            <!-- Simple Divider -->
                            <div class="h-6 w-px bg-gray-300"></div>

                            <!-- Simplified Breadcrumb -->
                            <nav class="flex items-center space-x-2" aria-label="Breadcrumb">
                                <ol class="flex items-center space-x-2">
                                    <li>
                                        <a href="{{ route('admin.dashboard') }}"
                                           class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition-colors duration-200">
                                            Dashboard
                                        </a>
                                    </li>
                                    <li>
                                        <i class="ri-arrow-right-s-line text-gray-400 text-xs"></i>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.organizations.index') }}"
                                           class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition-colors duration-200">
                                            Organizations
                                        </a>
                                    </li>
                                    <li>
                                        <i class="ri-arrow-right-s-line text-gray-400 text-xs"></i>
                                    </li>
                                    <li class="flex items-center">
                                        <span class="text-sm font-semibold text-indigo-700">Create New</span>
                                    </li>
                                </ol>
                            </nav>
                        </div>

                        <!-- Right Section: Reset Button Only -->
                        <div class="flex items-center">
                            <!-- Smaller Reset Button -->
                            <button @click="resetForm()"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 hover:border-gray-300 transition-all duration-200">
                                <i class="ri-refresh-line text-sm mr-1"></i>
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
                                        <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold"
                                             :class="currentStep === 1 ? 'bg-blue-100 text-blue-600' : 'bg-blue-600 text-white'">
                                            1
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">Basic Information</span>
                                    </div>
                                    <div class="w-12 h-px bg-gray-200"></div>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold"
                                             :class="currentStep === 2 ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-400'">
                                            2
                                        </div>
                                        <span class="text-sm" :class="currentStep === 2 ? 'text-gray-900 font-medium' : 'text-gray-500'">Review & Submit</span>
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
                                                           class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-gray-900 placeholder-gray-400"
                                                           :class="errors.name ? 'border-red-300 bg-red-50' : 'border-gray-200'"
                                                           placeholder="Enter organization name..."
                                                           required>
                                                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center">
                                                        <i class="ri-building-line text-gray-400" x-show="!formData.name"></i>
                                                        <i class="ri-check-line text-green-500" x-show="formData.name"></i>
                                                    </div>
                                                    <div x-show="errors.name" class="absolute -bottom-6 left-0">
                                                        <p class="text-red-500 text-sm" x-text="errors.name?.[0]"></p>
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
                                                        <span x-text="formData.description?.length || 0"></span>/500
                                                    </div>
                                                    <div x-show="errors.description" class="absolute -bottom-6 left-0">
                                                        <p class="text-red-500 text-sm" x-text="errors.description?.[0]"></p>
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
                                                            class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-gray-900 appearance-none cursor-pointer"
                                                            :class="errors.status ? 'border-red-300 bg-red-50' : 'border-gray-200'">
                                                        <option value="active">Active</option>
                                                        <option value="inactive">Inactive</option>
                                                    </select>
                                                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                                        <i class="ri-arrow-down-s-line text-gray-400"></i>
                                                    </div>
                                                    <div x-show="errors.status" class="absolute -bottom-6 left-0">
                                                        <p class="text-red-500 text-sm" x-text="errors.status?.[0]"></p>
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
                                                           class="w-full pl-12 pr-12 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 text-gray-900 placeholder-gray-400"
                                                           :class="errors.contact_email ? 'border-red-300 bg-red-50' : 'border-gray-200'"
                                                           placeholder="organization@example.com"
                                                           required>
                                                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center">
                                                        <i class="ri-mail-line text-gray-400" x-show="!formData.contact_email"></i>
                                                        <i class="ri-check-line text-green-500" x-show="formData.contact_email"></i>
                                                    </div>
                                                    <div x-show="errors.contact_email" class="absolute -bottom-6 left-0">
                                                        <p class="text-red-500 text-sm" x-text="errors.contact_email?.[0]"></p>
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
                                                           class="w-full pl-12 pr-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 text-gray-900 placeholder-gray-400"
                                                           :class="errors.contact_phone ? 'border-red-300 bg-red-50' : 'border-gray-200'"
                                                           placeholder="+1 (555) 123-4567">
                                                    <div x-show="errors.contact_phone" class="absolute -bottom-6 left-0">
                                                        <p class="text-red-500 text-sm" x-text="errors.contact_phone?.[0]"></p>
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

                                                <!-- Smaller Next Button -->
                                                <button type="submit"
                                                        :disabled="!validationPassed"
                                                        class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                                                    <i class="ri-arrow-right-line mr-2"></i>
                                                    Review & Submit
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
                                                    <p x-text="formData.status" class="text-gray-900 bg-gray-50 px-4 py-3 rounded-lg border capitalize"></p>
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
                                                    <span x-show="!loading" class="flex items-center">
                                                        <i class="ri-add-circle-line mr-2"></i>
                                                        Create Organization
                                                    </span>
                                                    <span x-show="loading" class="flex items-center">
                                                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                                                        Creating...
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
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
