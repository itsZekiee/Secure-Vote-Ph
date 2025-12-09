{{-- resources/views/main-admin/partylist/partylists-create.blade.php --}}
@extends('layouts.app-main-admin')

@section('content')
    <div x-data="{
                formData: {
                    name: '',
                    acronym: '',
                    description: '',
                    platform: '',
                    logo: null,
                    logoName: '',
                    color: '#3b82f6',
                    organization_id: '',
                    election_id: '',
                    status: 'active'
                },
                errors: {},
                loading: false,
                logoPreview: null,
                logoDragOver: false,
                currentStep: 1,
                showPreview: false,
                isDirty: false,
                showModal: false,
                modalType: 'success',
                modalTitle: '',
                modalMessage: '',

                progressPercent() {
                    const fields = ['name','acronym','description','platform','organization_id','logo'];
                    const filled = fields.reduce((acc, key) => acc + (this.formData[key] ? 1 : 0), 0);
                    return Math.round((filled / fields.length) * 100);
                },

                nextStep() {
                    if (this.validateStep1()) this.currentStep = 2;
                },

                prevStep() { this.currentStep = 1; },

                    validateStep1() {
                    const requiredFields = ['name', 'organization_id'];
                    let valid = true;
                    this.errors = {};
                    requiredFields.forEach(field => {
                        if (!this.formData[field]) {
                            this.errors[field] = ['This field is required'];
                            valid = false;
                        }
                    });
                    if (!valid) this.showNotification('Please fill in all required fields', 'error');
                    return valid;
                },

                async submitForm() {
                    if (!this.validateStep1()) return;
                    this.loading = true;
                    this.errors = {};

                    try {
                        const payload = new FormData();
                        Object.keys(this.formData).forEach(k => {
                            if (this.formData[k] !== null && this.formData[k] !== '') {
                                if (k === 'logo' && this.formData.logo instanceof File) {
                                    payload.append('logo', this.formData.logo);
                                } else if (k !== 'logo' && k !== 'logoName') {
                                    payload.append(k, this.formData[k]);
                                }
                            }
                        });

                        const token = document.querySelector('meta[name=csrf-token]')?.getAttribute('content') ||
                                     document.querySelector('input[name=_token]')?.value;
                        payload.append('_token', token);

                        const response = await fetch('{{ route('admin.partylists.store') }}', {
                            method: 'POST',
                            body: payload,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                            }
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            this.showResponseModal('success','Party List Created','Your party list has been created successfully.');
                            localStorage.removeItem('partylist_draft');
                            setTimeout(() => window.location.href = '{{ route('admin.partylists.index') }}', 2000);
                        } else {
                            this.errors = data.errors || {};
                            this.showResponseModal('error','Creation Failed','Please check the form for errors and try again.');
                        }
                    } catch (error) {
                        console.error('Submit error:', error);
                        this.showResponseModal('error','System Error','An unexpected error occurred. Please try again.');
                    } finally {
                        this.loading = false;
                    }
                },

                resetForm() {
                    this.formData = { name: '', acronym: '', description: '', platform: '', logo: null, logoName: '', color:'#3b82f6', organization_id:'', status:'active' };
                    this.logoPreview = null; this.errors = {}; this.isDirty = false; this.currentStep = 1;
                    this.showNotification('Form has been reset', 'info');
                },

                handleLogoUpload(event) {
                    const file = event.target.files ? event.target.files[0] : event;
                    if (!file) return;
                    if (file.size > 2 * 1024 * 1024) { this.showNotification('File size must not exceed 2MB','error'); return; }
                    if (!file.type.startsWith('image/')) { this.showNotification('Please upload a valid image file','error'); return; }
                    this.formData.logo = file;
                    this.formData.logoName = file.name;
                    const reader = new FileReader();
                    reader.onload = (e) => this.logoPreview = e.target.result;
                    reader.readAsDataURL(file);
                    this.isDirty = true;
                },

                handleDragOver(e) { e.preventDefault(); this.logoDragOver = true; },
                handleDragLeave() { this.logoDragOver = false; },
                handleDrop(e) {
                    e.preventDefault(); this.logoDragOver = false;
                    const file = e.dataTransfer.files[0];
                    if (file) this.handleLogoUpload(file);
                },

                generateAcronym() {
                    if (this.formData.name) {
                        this.formData.acronym = this.formData.name.split(' ').map(w=>w.charAt(0).toUpperCase()).join('').slice(0,5);
                    }
                },

                saveDraft() {
                    const draft = { ...this.formData }; delete draft.logo; delete draft.logoName;
                    localStorage.setItem('partylist_draft', JSON.stringify(draft));
                    this.showNotification('Draft saved successfully', 'success');
                },

                loadDraft() {
                    const draft = localStorage.getItem('partylist_draft');
                    if (!draft) { this.showNotification('No draft found','info'); return; }
                    const data = JSON.parse(draft);
                    this.formData = { ...this.formData, ...data }; this.isDirty = true; this.showNotification('Draft loaded successfully','info');
                },

                showResponseModal(type, title, message) { this.modalType = type; this.modalTitle = title; this.modalMessage = message; this.showModal = true; },
                closeModal() { this.showModal = false; },

                showNotification(message, type='info') {
                    const n = document.createElement('div');
                    const colors = {
                        success: 'bg-emerald-500 border-emerald-600',
                        error:'bg-red-500 border-red-600',
                        info:'bg-blue-500 border-blue-600'
                    };
                    n.className = `fixed top-6 right-6 \${colors[type]} text-white px-6 py-4 rounded-lg shadow-lg border-l-4 z-50 max-w-sm transition-all transform`;
                    n.innerHTML = `<div class='flex items-center space-x-3'><i class='ri-information-line'></i><span>\${message}</span></div>`;
                    n.style.transform = 'translateX(100%)';
                    document.body.appendChild(n);
                    setTimeout(() => n.style.transform = 'translateX(0)', 100);
                    setTimeout(() => {
                        n.style.transform = 'translateX(100%)';
                        setTimeout(() => n.remove(), 300);
                    }, 4000);
                }
             }" x-init="
                $watch('formData', () => isDirty = true, { deep: true });
                window.addEventListener('beforeunload', (e) => { if (isDirty) { e.preventDefault(); e.returnValue = ''; } });
             " class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">

        {{-- Modal --}}
        <div x-show="showModal" x-cloak
            class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div x-show="showModal" class="bg-white rounded-3xl w-full max-w-lg shadow-2xl overflow-hidden"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">
                <div class="p-8 text-center"
                    :class="modalType === 'success' ? 'bg-gradient-to-r from-emerald-50 to-teal-50' : 'bg-gradient-to-r from-red-50 to-pink-50'">
                    <div class="mx-auto w-24 h-24 rounded-full flex items-center justify-center mb-6"
                        :class="modalType === 'success' ? 'bg-emerald-100' : 'bg-red-100'">
                        <i x-show="modalType === 'success'" class="ri-check-line text-4xl text-emerald-600"></i>
                        <i x-show="modalType === 'error'" class="ri-error-warning-line text-4xl text-red-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900" x-text="modalTitle"></h3>
                    <p class="text-gray-600 mt-3" x-text="modalMessage"></p>
                </div>
                <div class="p-6 bg-gray-50 flex justify-center space-x-4">
                    <button @click="closeModal()"
                        class="px-6 py-3 rounded-xl bg-gray-700 text-white font-medium hover:bg-gray-800 transition-colors">Close</button>
                    <a x-show="modalType === 'success'" href="{{ route('admin.partylists.index') }}"
                        class="px-6 py-3 rounded-xl bg-white border-2 border-gray-200 text-gray-700 font-medium hover:bg-gray-50 transition-colors">View
                        All</a>
                </div>
            </div>
        </div>

        {{-- Top Header --}}
        <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-xl border-b border-gray-200/50 shadow-sm">
            <div class="max-w-7xl mx-auto px-6 py-5 flex items-center justify-between">
                <nav class="flex items-center text-sm space-x-3">
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-blue-600 transition-colors"><i
                            class="ri-home-4-line text-lg"></i></a>
                    <span class="text-gray-400">/</span>
                    <a href="{{ route('admin.partylists.index') }}"
                        class="text-gray-600 hover:text-blue-600 transition-colors font-medium">Party Lists</a>
                    <span class="text-gray-400">/</span>
                    <span class="font-bold text-gray-900">Create</span>
                </nav>

                <div class="flex items-center space-x-3">
                    <button @click="loadDraft()"
                        class="px-4 py-2.5 text-sm border-2 border-gray-200 rounded-xl font-medium hover:border-blue-300 hover:text-blue-600 transition-all">
                        <i class="ri-download-line mr-2"></i>Load Draft
                    </button>
                    <button @click="saveDraft()"
                        class="px-4 py-2.5 text-sm bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-medium hover:from-blue-700 hover:to-indigo-700 transition-all shadow-md">
                        <i class="ri-save-line mr-2"></i>Save Draft
                    </button>
                </div>
            </div>
        </header>

        {{-- Page title --}}
        <section class="max-w-7xl mx-auto px-6 py-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-6">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-blue-600 to-purple-600 rounded-2xl flex items-center justify-center text-white text-3xl shadow-lg">
                        <i class="ri-team-line"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Create Party List</h1>
                        <p class="text-gray-600 mt-1">Build your political party's identity and platform</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <button @click="resetForm()"
                        class="px-5 py-3 border-2 border-gray-200 rounded-xl font-medium hover:border-red-300 hover:text-red-600 transition-all">
                        <i class="ri-refresh-line mr-2"></i>Reset
                    </button>
                    <button @click="showPreview = !showPreview"
                        class="px-5 py-3 bg-gray-100 border-2 border-gray-200 rounded-xl font-medium hover:bg-gray-150 transition-all">
                        <i class="ri-eye-line mr-2"></i>Preview
                    </button>
                </div>
            </div>
        </section>

        {{-- Main --}}
        <main class="max-w-7xl mx-auto px-6 pb-12">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                    {{-- Server/Client validation summary --}}
                    <div x-show="Object.keys(errors).length"
                        class="mb-6 p-4 rounded-xl border-l-4 border-red-500 bg-red-50">
                        <div class="flex items-start">
                            <i class="ri-error-warning-line text-red-500 text-xl mr-3 mt-0.5"></i>
                            <div>
                                <strong class="block text-red-800 font-semibold">Please fix the following issues:</strong>
                                <ul class="mt-2 list-disc pl-5 text-sm text-red-700 space-y-1">
                                    <template x-for="(v,k) in errors" :key="k">
                                        <li x-text="Array.isArray(v) ? v[0] : v"></li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Step 1 --}}
                    <div x-show="currentStep === 1" class="space-y-8">
                        <div class="text-center pb-6 border-b border-gray-100">
                            <h2 class="text-2xl font-bold text-gray-900">Party Identity</h2>
                            <p class="text-gray-600 mt-2">Define your party's core information and visual branding</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Party Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" x-model="formData.name" @input="generateAcronym()" aria-required="true"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all"
                                    :class="errors.name ? 'border-red-300 bg-red-50' : ''" placeholder="Enter party name">
                                <div class="text-sm text-red-600 mt-2 font-medium" x-text="errors.name?.[0]"></div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Acronym</label>
                                <input type="text" x-model="formData.acronym"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all"
                                    placeholder="Auto-generated or custom">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                            <textarea x-model="formData.description" rows="4"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all resize-none"
                                placeholder="Brief description of your party"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Platform & Agenda</label>
                            <textarea x-model="formData.platform" rows="5"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all resize-none"
                                placeholder="Describe your party's platform, goals, and political agenda"></textarea>
                        </div>

                        <div class="pt-6 border-t border-gray-100">
                            <h3 class="text-xl font-bold text-gray-900 mb-6">Visual Identity</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                {{-- Logo Upload --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">Party Logo</label>
                                    <div @dragover.prevent="handleDragOver($event)" @dragleave.prevent="handleDragLeave()"
                                        @drop.prevent="handleDrop($event)"
                                        class="border-3 rounded-2xl p-6 text-center transition-all cursor-pointer"
                                        :class="logoDragOver ? 'border-blue-400 bg-blue-50 border-solid' : 'border-dashed border-gray-300 bg-white hover:border-blue-300'">
                                        <div x-show="!logoPreview" class="space-y-3">
                                            <i class="ri-upload-cloud-2-line text-4xl text-gray-400"></i>
                                            <div class="text-sm text-gray-600">
                                                <p class="font-medium">Drop logo here or click to upload</p>
                                                <p class="text-xs mt-1">PNG, JPG up to 2MB</p>
                                            </div>
                                            <input type="file" @change="handleLogoUpload($event)" accept="image/*"
                                                class="hidden">
                                        </div>

                                        <div x-show="logoPreview" class="relative">
                                            <img :src="logoPreview" class="w-20 h-20 object-cover rounded-xl mx-auto mb-3">
                                            <p class="text-sm text-gray-600 truncate" x-text="formData.logoName"></p>
                                            <button type="button"
                                                @click="formData.logo = null; logoPreview = null; formData.logoName = ''"
                                                class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full text-xs hover:bg-red-600">
                                                <i class="ri-close-line"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Color --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">Brand Color</label>
                                    <div class="space-y-3">
                                        <input type="color" x-model="formData.color"
                                            class="w-full h-12 border-2 border-gray-200 rounded-xl cursor-pointer">
                                        <div class="text-center">
                                            <span class="text-sm font-mono px-3 py-1 bg-gray-100 rounded"
                                                x-text="formData.color"></span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Organization --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                                        Organization <span class="text-red-500">*</span>
                                    </label>
                                    <select x-model="formData.organization_id"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all"
                                        :class="errors.organization_id ? 'border-red-300 bg-red-50' : ''">
                                        <option value="">Select Organization</option>
                                        @foreach($organizations ?? [] as $organization)
                                            <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="text-sm text-red-600 mt-2 font-medium" x-text="errors.organization_id?.[0]">
                                    </div>
                                </div>
                                {{-- Election --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                                        Election
                                    </label>
                                    <select x-model="formData.election_id"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all"
                                        :class="errors.election_id ? 'border-red-300 bg-red-50' : ''">
                                        <option value="">Select Election</option>
                                        @foreach($elections ?? [] as $election)
                                            <option value="{{ $election->id }}">
                                                {{ $election->title ?? $election->name ?? 'Election #' . $election->id }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="text-sm text-red-600 mt-2 font-medium" x-text="errors.election_id?.[0]">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between items-center pt-8 border-t border-gray-100">
                            <a href="{{ route('admin.partylists.index') }}"
                                class="px-6 py-3 border-2 border-gray-200 rounded-xl font-medium hover:border-red-300 hover:text-red-600 transition-all">
                                <i class="ri-arrow-left-line mr-2"></i>Cancel
                            </a>
                            <button @click="nextStep()"
                                class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-semibold hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg">
                                Next: Review <i class="ri-arrow-right-line ml-2"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Step 2: Review --}}
                    <div x-show="currentStep === 2" class="space-y-8">
                        <div class="text-center pb-6 border-b border-gray-100">
                            <h2 class="text-2xl font-bold text-gray-900">Review & Submit</h2>
                            <p class="text-gray-600 mt-2">Please review all information before creating your party list</p>
                        </div>

                        <div class="space-y-6">
                            <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl border border-blue-100">
                                <h4 class="font-bold text-gray-900 mb-4 flex items-center">
                                    <i class="ri-information-line mr-2 text-blue-600"></i>
                                    Basic Information
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="font-semibold text-gray-700">Party Name:</span>
                                        <span class="ml-2" x-text="formData.name || 'Not specified'"></span>
                                    </div>
                                    <div>
                                        <span class="font-semibold text-gray-700">Acronym:</span>
                                        <span class="ml-2" x-text="formData.acronym || 'N/A'"></span>
                                    </div>
                                    <div x-show="formData.description" class="md:col-span-2">
                                        <span class="font-semibold text-gray-700">Description:</span>
                                        <p class="mt-1 text-gray-600" x-text="formData.description"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-6 bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl border border-green-100"
                                x-show="formData.platform">
                                <h4 class="font-bold text-gray-900 mb-4 flex items-center">
                                    <i class="ri-flag-line mr-2 text-green-600"></i>
                                    Platform & Agenda
                                </h4>
                                <p class="text-sm text-gray-700" x-text="formData.platform"></p>
                            </div>

                            <div
                                class="p-6 bg-gradient-to-r from-purple-50 to-pink-50 rounded-2xl border border-purple-100">
                                <h4 class="font-bold text-gray-900 mb-4 flex items-center">
                                    <i class="ri-palette-line mr-2 text-purple-600"></i>
                                    Visual Identity
                                </h4>
                                <div class="flex items-center space-x-6">
                                    <div class="flex-shrink-0">
                                        <div x-show="logoPreview" class="w-16 h-16 rounded-2xl overflow-hidden shadow-md">
                                            <img :src="logoPreview" class="w-full h-full object-cover">
                                        </div>
                                        <div x-show="!logoPreview"
                                            class="w-16 h-16 rounded-2xl flex items-center justify-center text-white text-2xl font-bold shadow-md"
                                            :style="'background-color:' + formData.color"
                                            x-text="(formData.name || 'P').charAt(0)">
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-sm">
                                            <span class="font-semibold text-gray-700">Brand Color:</span>
                                            <span class="ml-2 font-mono" x-text="formData.color"></span>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1"
                                            x-text="formData.logoName ? formData.logoName : 'No logo uploaded'"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between items-center pt-8 border-t border-gray-100">
                            <button @click="prevStep()"
                                class="px-6 py-3 border-2 border-gray-200 rounded-xl font-medium hover:border-blue-300 hover:text-blue-600 transition-all">
                                <i class="ri-arrow-left-line mr-2"></i>Back to Edit
                            </button>
                            <button @click="submitForm()" :disabled="loading"
                                class="px-8 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-lg disabled:opacity-60 disabled:cursor-not-allowed">
                                <span x-show="!loading" class="flex items-center">
                                    <i class="ri-check-line mr-2"></i>Create Party List
                                </span>
                                <span x-show="loading" class="flex items-center">
                                    <i class="ri-loader-4-line animate-spin mr-2"></i>Creating...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Enhanced Sidebar --}}
                <aside class="space-y-6">
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
                        <h3 class="font-bold text-gray-900 flex items-center mb-4">
                            <i class="ri-progress-3-line mr-2 text-blue-600"></i>
                            Progress
                        </h3>
                        <div>
                            <div class="flex justify-between text-sm mb-3">
                                <span class="text-gray-600">Completion</span>
                                <span class="font-bold text-blue-600" x-text="progressPercent() + '%'"></span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                                <div class="h-4 bg-gradient-to-r from-blue-500 to-indigo-500 transition-all duration-500 rounded-full"
                                    :style="{ width: progressPercent() + '%' }"></div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-amber-50 to-yellow-50 border-2 border-amber-200 rounded-2xl p-6">
                        <h3 class="font-bold text-amber-900 flex items-center mb-4">
                            <i class="ri-lightbulb-line mr-2"></i>
                            Guidelines
                        </h3>
                        <ul class="text-sm text-amber-800 space-y-2">
                            <li class="flex items-start">
                                <i class="ri-check-line mr-2 mt-0.5 text-amber-600"></i>
                                Use clear, memorable names
                            </li>
                            <li class="flex items-start">
                                <i class="ri-check-line mr-2 mt-0.5 text-amber-600"></i>
                                Square logos work best
                            </li>
                            <li class="flex items-start">
                                <i class="ri-check-line mr-2 mt-0.5 text-amber-600"></i>
                                Keep platform focused
                            </li>
                            <li class="flex items-start">
                                <i class="ri-check-line mr-2 mt-0.5 text-amber-600"></i>
                                Choose distinctive colors
                            </li>
                        </ul>
                    </div>

                    <div x-show="showPreview && formData.name"
                        class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
                        <h3 class="font-bold text-gray-900 flex items-center mb-4">
                            <i class="ri-eye-line mr-2 text-green-600"></i>
                            Live Preview
                        </h3>
                        <div class="border-2 border-gray-100 rounded-xl p-4 bg-gradient-to-br from-gray-50 to-white">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div x-show="logoPreview" class="w-14 h-14 rounded-xl overflow-hidden shadow-md">
                                        <img :src="logoPreview" class="w-full h-full object-cover">
                                    </div>
                                    <div x-show="!logoPreview"
                                        class="w-14 h-14 rounded-xl flex items-center justify-center text-white font-bold shadow-md"
                                        :style="'background-color:' + formData.color"
                                        x-text="(formData.name || 'P').charAt(0)">
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-bold text-gray-900 truncate" x-text="formData.name"></div>
                                    <div class="text-sm text-gray-500" x-text="formData.acronym || ''"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </main>

        @csrf
    </div>
@endsection