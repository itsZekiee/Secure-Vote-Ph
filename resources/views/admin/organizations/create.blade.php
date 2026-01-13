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
                if (!confirm('Are you sure you want to submit and create this organization?')) {
                    return;
                }
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
            <div class="bg-white border-b border-slate-200/60 sticky top-0 z-40 shadow-sm">
                <div class="px-8 py-4">
                    <div class="flex items-center justify-between">
                        <!-- Left Section: Back Button & Breadcrumb -->
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('admin.organizations.index') }}"
                               class="flex items-center justify-center w-10 h-10 rounded-xl bg-slate-50 hover:bg-slate-100 transition-all duration-200 border border-slate-200 hover:border-slate-300">
                                <i class="ri-arrow-left-line text-slate-600"></i>
                            </a>

                            <div class="h-6 w-px bg-slate-200"></div>

                            <nav class="flex items-center" aria-label="Breadcrumb">
                                <ol class="flex items-center space-x-2">
                                    <li>
                                        <a href="{{ route('admin.dashboard') }}" class="text-[9px] font-black text-slate-400 hover:text-indigo-600 transition-colors uppercase tracking-widest">Dashboard</a>
                                    </li>
                                    <li>
                                        <i class="ri-arrow-right-s-line text-slate-300"></i>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.organizations.index') }}" class="text-[9px] font-black text-slate-400 hover:text-indigo-600 transition-colors uppercase tracking-widest">Organizations</a>
                                    </li>
                                    <li>
                                        <i class="ri-arrow-right-s-line text-slate-300"></i>
                                    </li>
                                    <li class="flex items-center">
                                        <span class="text-[9px] font-black text-indigo-700 uppercase tracking-widest">Create New</span>
                                    </li>
                                </ol>
                            </nav>
                        </div>

                        <div class="flex items-center">
                            <button @click="resetForm()"
                                    class="inline-flex items-center px-4 py-2 text-[9px] font-black text-slate-600 bg-slate-50 border border-slate-200 rounded-xl hover:bg-slate-100 transition-all uppercase tracking-widest">
                                <i class="ri-refresh-line mr-1.5"></i>
                                Reset Form
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
                                <div class="w-16 h-16 bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600 rounded-2xl flex items-center justify-center shadow-xl shadow-blue-500/25 transform hover:rotate-3 transition-transform">
                                    <i class="ri-building-add-line text-white text-2xl"></i>
                                </div>
                            </div>
                            <div>
                                <h1 class="text-3xl font-black text-slate-900 mb-1 uppercase tracking-tight">Create Organization</h1>
                                <p class="text-sm text-slate-500 font-bold mb-4">Set up a new organization to manage members and activities</p>

                                <!-- Progress Steps -->
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-[10px] font-black transition-all duration-300 shadow-sm"
                                             :class="currentStep === 1 ? 'bg-indigo-600 text-white shadow-indigo-100' : 'bg-emerald-500 text-white shadow-emerald-100'">
                                            <i x-show="currentStep > 1" class="ri-check-line"></i>
                                            <span x-show="currentStep === 1">1</span>
                                        </div>
                                        <span class="text-[9px] font-black uppercase tracking-widest" :class="currentStep === 1 ? 'text-slate-900' : 'text-slate-400'">Basic Info</span>
                                    </div>
                                    <div class="w-8 h-px bg-slate-200"></div>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-[10px] font-black transition-all duration-300 shadow-sm"
                                             :class="currentStep === 2 ? 'bg-indigo-600 text-white shadow-indigo-100' : 'bg-slate-100 text-slate-400'">
                                            2
                                        </div>
                                        <span class="text-[9px] font-black uppercase tracking-widest" :class="currentStep === 2 ? 'text-slate-900' : 'text-slate-400'">Review & Submit</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Container -->
                    <div class="grid grid-cols-12 gap-8">
                        <!-- Main Form -->
                        <div class="col-span-12 lg:col-span-8">
                            <!-- Step 1: Basic Information -->
                            <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                                <form @submit.prevent="nextStep()" class="space-y-6">
                                    @csrf

                                    <!-- Organization Details Card -->
                                    <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-200/40 overflow-hidden">
                                        <div class="px-8 py-5 bg-slate-50/50 border-b border-slate-100">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-indigo-600 shadow-sm border border-slate-100">
                                                        <i class="ri-building-4-line text-xl"></i>
                                                    </div>
                                                    <div>
                                                        <h3 class="text-base font-black text-slate-800 uppercase tracking-tight">Organization Details</h3>
                                                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Primary Identity Info</p>
                                                    </div>
                                                </div>
                                                <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full text-[9px] font-black uppercase tracking-widest">Step 01</span>
                                            </div>
                                        </div>

                                        <div class="p-8 space-y-6">
                                            <!-- Organization Name -->
                                            <div class="space-y-2">
                                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">
                                                    Organization Name <span class="text-rose-500">*</span>
                                                </label>
                                                <div class="relative group">
                                                    <i class="ri-building-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors"></i>
                                                    <input type="text"
                                                           x-model="formData.name"
                                                           class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border-none rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all duration-300 text-sm font-bold text-slate-700 shadow-sm"
                                                           :class="errors.name ? 'ring-2 ring-rose-500' : ''"
                                                           placeholder="e.g. Supreme Student Council"
                                                           required>
                                                </div>
                                                <template x-if="errors.name">
                                                    <p class="text-rose-500 text-[9px] font-bold uppercase tracking-wider mt-1 ml-1" x-text="errors.name[0]"></p>
                                                </template>
                                            </div>

                                            <!-- Description -->
                                            <div class="space-y-2">
                                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">
                                                    Description <span class="text-slate-400 font-bold text-[8px] tracking-normal lowercase">(optional)</span>
                                                </label>
                                                <textarea x-model="formData.description"
                                                          rows="4"
                                                          class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all duration-300 text-sm font-bold text-slate-700 shadow-sm resize-none"
                                                          :class="errors.description ? 'ring-2 ring-rose-500' : ''"
                                                          placeholder="Describe the organization's mission and goals..."></textarea>
                                                <div class="flex justify-between items-center px-1">
                                                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Brief overview of the organization</p>
                                                    <span class="text-[9px] font-black text-slate-400 uppercase" x-text="`${formData.description?.length || 0}/500`"></span>
                                                </div>
                                            </div>

                                            <!-- Status -->
                                            <div class="space-y-2">
                                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">
                                                    Activity Status <span class="text-rose-500">*</span>
                                                </label>
                                                <div class="relative">
                                                    <i class="ri-toggle-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 z-10"></i>
                                                    <select x-model="formData.status"
                                                            class="w-full pl-12 pr-10 py-3.5 bg-slate-50 border-none rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all duration-300 text-sm font-bold text-slate-700 shadow-sm appearance-none cursor-pointer">
                                                        <option value="active">Active & Operational</option>
                                                        <option value="inactive">Inactive / Archived</option>
                                                    </select>
                                                    <i class="ri-arrow-down-s-line absolute right-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Contact Information Card -->
                                    <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-200/40 overflow-hidden">
                                        <div class="px-8 py-5 bg-slate-50/50 border-b border-slate-100">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-emerald-600 shadow-sm border border-slate-100">
                                                    <i class="ri-contacts-line text-xl"></i>
                                                </div>
                                                <div>
                                                    <h3 class="text-base font-black text-slate-800 uppercase tracking-tight">Contact Information</h3>
                                                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Communication Channels</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="p-8 space-y-6">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                <!-- Contact Email -->
                                                <div class="space-y-2">
                                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">
                                                        Official Email <span class="text-rose-500">*</span>
                                                    </label>
                                                    <div class="relative group">
                                                        <i class="ri-mail-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-emerald-600 transition-colors"></i>
                                                        <input type="email"
                                                               x-model="formData.contact_email"
                                                               class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border-none rounded-xl focus:ring-4 focus:ring-emerald-500/10 focus:bg-white transition-all duration-300 text-sm font-bold text-slate-700 shadow-sm"
                                                               :class="errors.contact_email ? 'ring-2 ring-rose-500' : ''"
                                                               placeholder="org@example.com"
                                                               required>
                                                    </div>
                                                </div>

                                                <!-- Contact Phone -->
                                                <div class="space-y-2">
                                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">
                                                        Phone Number <span class="text-slate-400 font-bold text-[8px] tracking-normal lowercase">(optional)</span>
                                                    </label>
                                                    <div class="relative group">
                                                        <i class="ri-phone-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-emerald-600 transition-colors"></i>
                                                        <input type="tel"
                                                               x-model="formData.contact_phone"
                                                               class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border-none rounded-xl focus:ring-4 focus:ring-emerald-500/10 focus:bg-white transition-all duration-300 text-sm font-bold text-slate-700 shadow-sm"
                                                               placeholder="+63 9XX XXX XXXX">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Next Step Action -->
                                    <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-200/40 p-6 flex flex-col md:flex-row items-center justify-between gap-4">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 shadow-inner">
                                                <i class="ri-arrow-right-line text-2xl"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-black text-slate-900 uppercase tracking-tight">Proceed to Review</p>
                                                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">Validate information before finalizing</p>
                                            </div>
                                        </div>

                                        <div class="flex items-center space-x-3 w-full md:w-auto">
                                            <a href="{{ route('admin.organizations.index') }}"
                                               class="flex-1 md:flex-none inline-flex items-center justify-center px-6 py-3.5 bg-slate-100 text-slate-600 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-200 transition-all">
                                                Cancel
                                            </a>
                                            <button type="submit"
                                                    :disabled="!validationPassed"
                                                    class="flex-1 md:flex-none inline-flex items-center justify-center px-8 py-3.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-indigo-100 hover:shadow-indigo-200 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                                Review & Submit
                                                <i class="ri-arrow-right-s-line ml-2 text-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Step 2: Review & Submit -->
                            <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                                <form @submit.prevent="submitForm()" class="space-y-6">
                                    <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-200/40 overflow-hidden">
                                        <div class="px-8 py-5 bg-slate-50/50 border-b border-slate-100">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-purple-600 shadow-sm border border-slate-100">
                                                    <i class="ri-eye-line text-xl"></i>
                                                </div>
                                                <div>
                                                    <h3 class="text-base font-black text-slate-800 uppercase tracking-tight">Review Details</h3>
                                                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Final Verification</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="p-8 space-y-6">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                                <div class="space-y-1.5">
                                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Organization Name</label>
                                                    <p x-text="formData.name" class="text-sm font-bold text-slate-900 bg-slate-50 px-4 py-3 rounded-xl border border-slate-100"></p>
                                                </div>
                                                <div class="space-y-1.5">
                                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Status</label>
                                                    <div class="flex">
                                                        <span x-text="formData.status" class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-indigo-100 capitalize"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="space-y-1.5">
                                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Description</label>
                                                <p x-text="formData.description || 'No description provided'" class="text-sm font-bold text-slate-700 bg-slate-50 px-4 py-3 rounded-xl border border-slate-100 min-h-[80px] leading-relaxed"></p>
                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                                <div class="space-y-1.5">
                                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Official Email</label>
                                                    <div class="flex items-center gap-2 text-slate-900 font-bold text-sm">
                                                        <i class="ri-mail-line text-indigo-500"></i>
                                                        <span x-text="formData.contact_email"></span>
                                                    </div>
                                                </div>
                                                <div class="space-y-1.5">
                                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Phone Number</label>
                                                    <div class="flex items-center gap-2 text-slate-900 font-bold text-sm">
                                                        <i class="ri-phone-line text-emerald-500"></i>
                                                        <span x-text="formData.contact_phone || 'Not provided'"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-200/40 p-6 flex flex-col md:flex-row items-center justify-between gap-4">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-inner">
                                                <i class="ri-check-double-line text-2xl"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-black text-slate-900 uppercase tracking-tight">Ready to Create</p>
                                                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">Everything looks correct</p>
                                            </div>
                                        </div>

                                        <div class="flex items-center space-x-3 w-full md:w-auto">
                                            <button type="button" @click="prevStep()"
                                                    class="flex-1 md:flex-none inline-flex items-center justify-center px-6 py-3.5 bg-slate-100 text-slate-600 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-200 transition-all">
                                                <i class="ri-arrow-left-s-line mr-2 text-lg"></i>
                                                Back to Edit
                                            </button>
                                            <button type="submit" :disabled="loading"
                                                    class="flex-1 md:flex-none inline-flex items-center justify-center px-10 py-3.5 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-emerald-100 hover:shadow-emerald-200 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                                <span x-show="!loading" class="flex items-center">
                                                    <i class="ri-add-circle-line mr-2 text-lg"></i>
                                                    Create Organization
                                                </span>
                                                <span x-show="loading" class="flex items-center">
                                                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                                                    Creating...
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Sidebar -->
                        <div class="col-span-12 lg:col-span-4">
                            <div class="sticky top-32 space-y-6">
                                <!-- Progress Card -->
                                <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-200/40 p-6">
                                    <h4 class="text-xs font-black text-slate-900 mb-6 uppercase tracking-widest">Setup Progress</h4>
                                    <div class="space-y-4">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Completion</span>
                                            <span class="text-sm font-black text-indigo-600" x-text="Math.round((Object.values(formData).filter(v => v !== '').length / Object.keys(formData).length) * 100) + '%'"></span>
                                        </div>
                                        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-gradient-to-r from-indigo-500 to-blue-500 h-full rounded-full transition-all duration-500"
                                                 :style="`width: ${(Object.values(formData).filter(v => v !== '').length / Object.keys(formData).length) * 100}%`"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pro Tips Card -->
                                <div class="bg-gradient-to-br from-indigo-600 to-blue-600 rounded-3xl p-6 text-white shadow-xl shadow-indigo-100">
                                    <div class="flex items-center space-x-3 mb-4">
                                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                                            <i class="ri-lightbulb-line text-xl"></i>
                                        </div>
                                        <h4 class="text-sm font-black uppercase tracking-tight">Pro Tips</h4>
                                    </div>
                                    <ul class="space-y-3">
                                        <li class="flex items-start space-x-3">
                                            <i class="ri-checkbox-circle-fill text-emerald-400 mt-0.5"></i>
                                            <p class="text-[11px] font-bold text-indigo-50 leading-relaxed">Use a professional name for institutional recognition.</p>
                                        </li>
                                        <li class="flex items-start space-x-3">
                                            <i class="ri-checkbox-circle-fill text-emerald-400 mt-0.5"></i>
                                            <p class="text-[11px] font-bold text-indigo-50 leading-relaxed">Ensure the contact email is regularly monitored.</p>
                                        </li>
                                        <li class="flex items-start space-x-3">
                                            <i class="ri-checkbox-circle-fill text-emerald-400 mt-0.5"></i>
                                            <p class="text-[11px] font-bold text-indigo-50 leading-relaxed">Descriptions help members understand your core mission.</p>
                                        </li>
                                    </ul>
                                </div>

                                <!-- Quick Actions -->
                                <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-200/40 p-6">
                                    <h4 class="text-xs font-black text-slate-900 mb-6 uppercase tracking-widest">Utility Panel</h4>
                                    <div class="space-y-3">
                                        <button @click="saveDraft()"
                                                class="w-full inline-flex items-center justify-center px-4 py-3 text-[10px] font-black text-amber-600 bg-amber-50 border border-amber-100 rounded-xl hover:bg-amber-100 transition-all uppercase tracking-widest">
                                            <i class="ri-save-3-line mr-2 text-base"></i>
                                            Save Draft
                                        </button>
                                        <button @click="loadDraft()"
                                                class="w-full inline-flex items-center justify-center px-4 py-3 text-[10px] font-black text-blue-600 bg-blue-50 border border-blue-100 rounded-xl hover:bg-blue-100 transition-all uppercase tracking-widest">
                                            <i class="ri-download-line mr-2 text-base"></i>
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
