@extends('layouts.app-main-admin')

@php
    $id = $organization->id ?? 0;

    // Index/back URL fallback
    $indexUrl = Route::has('admin.organizations.index') ? route('admin.organizations.index') :
                (Route::has('organizations.index') ? route('organizations.index') : url('/admin/organizations'));

    // Show URL fallback
    $showUrl = Route::has('admin.organizations.show') ? route('admin.organizations.show', $id) :
               (Route::has('organizations.show') ? route('organizations.show', $id) : url('/admin/organizations/'.$id));

    // Update action — safe fallback
    $updateAction = Route::has('admin.organizations.update') ? route('admin.organizations.update', $id) :
                    (Route::has('organizations.update') ? route('organizations.update', $id) : url('/admin/organizations/'.$id));

    // Destroy action — safe fallback for delete buttons
    $destroyAction = Route::has('admin.organizations.destroy') ? route('admin.organizations.destroy', $id) :
                     (Route::has('organizations.destroy') ? route('organizations.destroy', $id) : url('/admin/organizations/'.$id));

    // Accurate data handling with safe property access
    $organizationName = old('name', $organization->name ?? 'Organization');
    $organizationIdValue = old('organization_id', $organization->organization_id ?? '');
    $organizationEmail = old('email', $organization->email ?? $organization->contact_email ?? '');
    $organizationPhone = old('phone', $organization->phone ?? $organization->contact_phone ?? '');
    $organizationAddress = old('address', $organization->address ?? $organization->location ?? '');
    $organizationStatus = old('status', $organization->status ?? 'active');
    $organizationLogo = $organization->logo_url ?? $organization->logo ?? null;
    $organizationDescription = old('description', $organization->description ?? '');

    // Safe count access to avoid database errors
    $membersCount = 0;
    $electionsCount = 0;
    try {
        if (isset($organization) && $organization && method_exists($organization, 'users')) {
            $membersCount = $organization->users()->count() ?? 0;
        }
        if (isset($organization) && $organization && method_exists($organization, 'elections')) {
            $electionsCount = $organization->elections()->count() ?? 0;
        }
    } catch (\Exception $e) {
        $membersCount = 0;
        $electionsCount = 0;
    }
@endphp

@section('title', 'Edit Organization - ' . $organizationName)

@section('content')
    <div x-data="{
            formData: {
                name: @js($organizationName),
                slug: @js($organizationSlug),
                email: @js($organizationEmail),
                phone: @js($organizationPhone),
                address: @js($organizationAddress),
                description: @js($organizationDescription),
                status: @js($organizationStatus)
            },
            originalData: {
                name: @js($organization->name ?? ''),
                slug: @js($organization->slug ?? ''),
                email: @js($organization->email ?? $organization->contact_email ?? ''),
                phone: @js($organization->phone ?? $organization->contact_phone ?? ''),
                address: @js($organization->address ?? $organization->location ?? ''),
                description: @js($organization->description ?? ''),
                status: @js($organization->status ?? 'active')
            },
            errors: @json($errors->toArray()),
            showDeleteModal: false,
            hasChanges: false,
            isDirty: false,

            checkForChanges() {
                this.hasChanges = JSON.stringify(this.formData) !== JSON.stringify(this.originalData);
                this.isDirty = this.hasChanges;
            },

            saveDraft() {
                localStorage.setItem('organization_edit_draft_{{ $id }}', JSON.stringify(this.formData));
                this.showToast('Draft saved successfully', 'success');
            },

            loadDraft() {
                const draft = localStorage.getItem('organization_edit_draft_{{ $id }}');
                if (draft) {
                    this.formData = { ...this.formData, ...JSON.parse(draft) };
                    this.checkForChanges();
                    this.showToast('Draft loaded successfully', 'info');
                }
            },

            resetForm() {
                this.formData = { ...this.originalData };
                localStorage.removeItem('organization_edit_draft_{{ $id }}');
                this.hasChanges = false;
                this.isDirty = false;
                this.showToast('Form reset to original values', 'info');
            },

            confirmDelete() {
                this.showDeleteModal = true;
            },

            generateSlug() {
                this.formData.slug = this.formData.name
                    .toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-|-$/g, '');
                this.checkForChanges();
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
            $watch('formData', () => checkForChanges(), { deep: true });
            window.addEventListener('beforeunload', (e) => {
                if (isDirty) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });
         "
         class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/40 to-indigo-50/30">

        <!-- Delete Organization Modal -->
        <div x-show="showDeleteModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div x-show="showDeleteModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 transform translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 transform translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="bg-white rounded-2xl shadow-2xl max-w-md mx-4 overflow-hidden">
                <div class="p-8 text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-red-500 to-rose-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="ri-delete-bin-line text-white text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Delete Organization</h3>
                    <p class="text-gray-600 mb-6">Are you sure you want to delete "{{ $organizationName }}"? This action cannot be undone and will remove all associated data.</p>
                    <div class="flex space-x-4">
                        <button @click="showDeleteModal = false"
                                class="flex-1 px-6 py-3 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-all duration-200 font-semibold">
                            Cancel
                        </button>
                        <form action="{{ $destroyAction }}" method="POST" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full px-6 py-3 bg-gradient-to-r from-red-600 to-rose-600 text-white rounded-xl hover:from-red-700 hover:to-rose-700 transition-all duration-200 font-semibold">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Navigation Bar -->
        <div class="bg-white/80 backdrop-blur-xl border-b border-gray-200/50 shadow-sm sticky top-0 z-40">
            <!-- Mobile Header -->
            <header class="lg:hidden bg-white border-b px-4 py-4 flex items-center justify-between">
                <button @click="collapsed = false"
                        class="p-2 -ml-2 rounded-lg text-slate-600 hover:bg-slate-100">
                    <i class="ri-menu-2-fill text-xl"></i>
                </button>
                <h1 class="text-lg font-bold text-slate-800">Edit Org</h1>
                <div class="w-8"></div>
            </header>

            <div class="max-w-7xl mx-auto px-4 sm:px-8 py-4 flex items-center justify-between">
                <!-- Left Section: Back Button & Breadcrumb -->
                <div class="flex items-center space-x-4">
                    <a href="{{ $indexUrl }}"
                       class="hidden sm:flex items-center justify-center w-10 h-10 rounded-xl bg-slate-50 hover:bg-slate-100 transition-all duration-300 border border-slate-200 hover:border-slate-300 group">
                        <i class="ri-arrow-left-line text-slate-600 group-hover:-translate-x-1 transition-transform"></i>
                    </a>

                    <div class="h-6 w-px bg-slate-200 hidden sm:block"></div>

                    <nav class="hidden md:flex" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-2">
                            <li class="inline-flex items-center">
                                <span class="text-slate-400 text-[9px] font-black uppercase tracking-widest">Admin</span>
                            </li>
                            <li>
                                <i class="ri-arrow-right-s-line text-slate-300"></i>
                            </li>
                            <li>
                                <a href="{{ $indexUrl }}" class="text-slate-500 hover:text-indigo-600 text-[9px] font-black uppercase tracking-widest transition-colors">Organizations</a>
                            </li>
                            <li>
                                <i class="ri-arrow-right-s-line text-slate-300"></i>
                            </li>
                            <li class="flex items-center">
                                <span class="text-indigo-700 text-[9px] font-black uppercase tracking-widest">Edit</span>
                            </li>
                        </ol>
                    </nav>
                </div>

                <!-- Right Section: Action Buttons -->
                <div class="flex items-center space-x-2 sm:space-x-3 w-full sm:w-auto">
                    <button @click="loadDraft()"
                            class="hidden xs:inline-flex items-center px-4 py-2.5 text-[9px] font-black uppercase tracking-widest text-indigo-600 bg-indigo-50 border border-indigo-100 rounded-xl hover:bg-indigo-100 transition-all duration-300">
                        <i class="ri-download-2-line mr-1.5"></i>
                        Load Draft
                    </button>

                    <button @click="resetForm()"
                            class="hidden xs:inline-flex items-center px-4 py-2.5 text-[9px] font-black uppercase tracking-widest text-slate-600 bg-slate-50 border border-slate-200 rounded-xl hover:bg-slate-100 transition-all duration-300">
                        <i class="ri-refresh-line mr-1.5"></i>
                        Reset
                    </button>

                    <button type="submit" form="orgEditForm" class="flex-1 sm:flex-none justify-center inline-flex items-center px-4 sm:px-6 py-2.5 bg-slate-900 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-600 shadow-lg transition-all" :disabled="!isDirty">
                        <i class="ri-save-line mr-2"></i><span class="hidden sm:inline">Commit Changes</span><span class="sm:hidden">Save</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Header Section -->
        <div class="px-4 sm:px-8 py-8">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col sm:flex-row items-start justify-between gap-6 mb-8">
                    <div class="flex items-center space-x-4 sm:space-x-6">
                        <!-- Organization Icon -->
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-indigo-600 to-blue-600 rounded-2xl flex items-center justify-center shadow-xl shadow-indigo-100 transform hover:scale-105 transition-all duration-500">
                                <i class="ri-building-2-fill text-white text-2xl sm:text-3xl"></i>
                            </div>
                        </div>

                        <div class="min-w-0">
                            <h1 class="text-xl sm:text-3xl font-black text-slate-900 mb-1 tracking-tight uppercase truncate">Edit Organization</h1>
                            <p class="text-[9px] sm:text-[10px] text-slate-500 font-bold tracking-widest uppercase">Identity & Core Settings</p>
                        </div>
                    </div>
                </div>

                <!-- Form Container -->
                <form id="orgEditForm" action="{{ $updateAction }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-12 gap-8">
                    @csrf
                    @method('PUT')

                    <!-- Main Form Content -->
                    <div class="col-span-12 lg:col-span-8 space-y-8">
                        <!-- Basic Information Card -->
                        <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-200/40 overflow-hidden">
                            <div class="px-8 py-5 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-indigo-600 shadow-sm border border-slate-50">
                                        <i class="ri-building-line text-xl"></i>
                                    </div>
                                    <div>
                                        <h2 class="text-base font-black text-slate-800 uppercase tracking-tight">Basic Information</h2>
                                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Primary Identity Settings</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full text-[9px] font-black uppercase tracking-widest">Section 01</span>
                            </div>

                            <div class="p-8 space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Organization Name -->
                                    <div class="space-y-2">
                                        <label for="name" class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Organization Name <span class="text-rose-500">*</span></label>
                                        <div class="relative group">
                                            <i class="ri-building-4-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors z-10"></i>
                                            <input type="text"
                                                   id="name"
                                                   name="name"
                                                   x-model="formData.name"
                                                   @input="generateSlug()"
                                                   class="block w-full pl-12 pr-4 py-3.5 bg-slate-50 border-none rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all duration-300 text-sm font-bold text-slate-700 shadow-sm @error('name') ring-2 ring-rose-500 @enderror"
                                                   placeholder="e.g. Supreme Student Council"
                                                   required>
                                        </div>
                                        @error('name')
                                        <p class="mt-1 text-[9px] font-bold text-rose-500 uppercase tracking-wider flex items-center ml-1"><i class="ri-error-warning-line mr-1"></i> {{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Organization Slug -->
                                    <div class="space-y-2">
                                        <label for="slug" class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Unique Slug</label>
                                        <div class="relative group">
                                            <i class="ri-link absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors z-10"></i>
                                            <input type="text"
                                                   id="slug"
                                                   name="slug"
                                                   x-model="formData.slug"
                                                   class="block w-full pl-12 pr-4 py-3.5 bg-slate-50 border-none rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all duration-300 text-sm font-bold text-slate-700 shadow-sm @error('slug') ring-2 ring-rose-500 @enderror"
                                                   placeholder="auto-generated-slug">
                                        </div>
                                        @error('slug')
                                        <p class="mt-1 text-[9px] font-bold text-rose-500 uppercase tracking-wider flex items-center ml-1"><i class="ri-error-warning-line mr-1"></i> {{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Description -->
                                    <div class="md:col-span-2 space-y-2">
                                        <label for="description" class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">About Organization</label>
                                        <textarea id="description"
                                                  name="description"
                                                  x-model="formData.description"
                                                  rows="5"
                                                  class="block w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all duration-300 text-sm font-bold text-slate-700 shadow-sm resize-none @error('description') ring-2 ring-rose-500 @enderror"
                                                  placeholder="Tell us about your organization's mission and goals..."></textarea>
                                        @error('description')
                                        <p class="mt-1 text-[9px] font-bold text-rose-500 uppercase tracking-wider flex items-center ml-1"><i class="ri-error-warning-line mr-1"></i> {{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information Card -->
                        <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-200/40 overflow-hidden">
                            <div class="px-8 py-5 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-emerald-600 shadow-sm border border-slate-50">
                                        <i class="ri-contacts-line text-xl"></i>
                                    </div>
                                    <div>
                                        <h2 class="text-base font-black text-slate-800 uppercase tracking-tight">Communication Hub</h2>
                                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Contact & Location Settings</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[9px] font-black uppercase tracking-widest">Section 02</span>
                            </div>

                            <div class="p-8 space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Email -->
                                    <div class="space-y-2">
                                        <label for="email" class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Official Email</label>
                                        <div class="relative group">
                                            <i class="ri-mail-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-emerald-600 transition-colors z-10"></i>
                                            <input type="email"
                                                   id="email"
                                                   name="email"
                                                   x-model="formData.email"
                                                   class="block w-full pl-12 pr-4 py-3.5 bg-slate-50 border-none rounded-xl focus:ring-4 focus:ring-emerald-500/10 focus:bg-white transition-all duration-300 text-sm font-bold text-slate-700 shadow-sm @error('email') ring-2 ring-rose-500 @enderror"
                                                   placeholder="hello@org.com">
                                        </div>
                                        @error('email')
                                        <p class="mt-1 text-[9px] font-bold text-rose-500 uppercase tracking-wider flex items-center ml-1"><i class="ri-error-warning-line mr-1"></i> {{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Phone -->
                                    <div class="space-y-2">
                                        <label for="phone" class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Phone Number</label>
                                        <div class="relative group">
                                            <i class="ri-phone-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-emerald-600 transition-colors z-10"></i>
                                            <input type="tel"
                                                   id="phone"
                                                   name="phone"
                                                   x-model="formData.phone"
                                                   class="block w-full pl-12 pr-4 py-3.5 bg-slate-50 border-none rounded-xl focus:ring-4 focus:ring-emerald-500/10 focus:bg-white transition-all duration-300 text-sm font-bold text-slate-700 shadow-sm @error('phone') ring-2 ring-rose-500 @enderror"
                                                   placeholder="+63 900 000 0000">
                                        </div>
                                        @error('phone')
                                        <p class="mt-1 text-[9px] font-bold text-rose-500 uppercase tracking-wider flex items-center ml-1"><i class="ri-error-warning-line mr-1"></i> {{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Address -->
                                    <div class="md:col-span-2 space-y-2">
                                        <label for="address" class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Physical Address</label>
                                        <div class="relative group">
                                            <i class="ri-map-pin-line absolute left-4 top-5 text-slate-400 group-focus-within:text-emerald-600 transition-colors z-10"></i>
                                            <textarea id="address"
                                                      name="address"
                                                      x-model="formData.address"
                                                      rows="2"
                                                      class="block w-full pl-12 pr-4 py-3.5 bg-slate-50 border-none rounded-xl focus:ring-4 focus:ring-emerald-500/10 focus:bg-white transition-all duration-300 text-sm font-bold text-slate-700 shadow-sm resize-none @error('address') ring-2 ring-rose-500 @enderror"
                                                      placeholder="Enter full business address..."></textarea>
                                        </div>
                                        @error('address')
                                        <p class="mt-1 text-[9px] font-bold text-rose-500 uppercase tracking-wider flex items-center ml-1"><i class="ri-error-warning-line mr-1"></i> {{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Settings Card -->
                        <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-200/40 overflow-hidden">
                            <div class="px-8 py-5 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-purple-600 shadow-sm border border-slate-50">
                                        <i class="ri-settings-3-line text-xl"></i>
                                    </div>
                                    <div>
                                        <h2 class="text-base font-black text-slate-800 uppercase tracking-tight">Operational Status</h2>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Manage Activity Level</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1 bg-purple-50 text-purple-600 rounded-full text-[9px] font-black uppercase tracking-widest">Section 03</span>
                            </div>

                            <div class="p-8">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Status -->
                                    <div class="space-y-2">
                                        <label for="status" class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Activity Status</label>
                                        <div class="relative group">
                                            <i class="ri-toggle-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-purple-600 transition-colors z-10"></i>
                                            <select id="status"
                                                    name="status"
                                                    x-model="formData.status"
                                                    class="block w-full pl-12 pr-10 py-3.5 bg-slate-50 border-none rounded-xl focus:ring-4 focus:ring-purple-500/10 focus:bg-white transition-all duration-300 text-sm font-bold text-slate-700 shadow-sm appearance-none @error('status') ring-2 ring-rose-500 @enderror">
                                                <option value="active">Active & Operational</option>
                                                <option value="inactive">Inactive / Archived</option>
                                                <option value="pending">Awaiting Verification</option>
                                                <option value="suspended">Suspended</option>
                                            </select>
                                            <i class="ri-arrow-down-s-line absolute right-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                        </div>
                                        @error('status')
                                        <p class="mt-1 text-[9px] font-bold text-rose-500 uppercase tracking-wider flex items-center ml-1"><i class="ri-error-warning-line mr-1"></i> {{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sticky Bottom Action Bar -->
                        <div class="bg-white/95 backdrop-blur-xl border border-slate-200/60 rounded-3xl p-6 shadow-2xl sticky bottom-8 z-30 flex flex-wrap items-center justify-between gap-4">
                            <div class="flex items-center space-x-3">
                                <button type="button"
                                        @click="confirmDelete()"
                                        class="inline-flex items-center px-6 py-3 bg-rose-50 text-rose-600 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-rose-100 transition-all border border-rose-100">
                                    <i class="ri-delete-bin-line mr-2"></i>
                                    Delete
                                </button>
                                <button type="button"
                                        @click="saveDraft()"
                                        class="inline-flex items-center px-6 py-3 bg-indigo-50 text-indigo-600 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-100 transition-all border border-indigo-100">
                                    <i class="ri-save-line mr-2"></i>
                                    Save Draft
                                </button>
                            </div>

                            <div class="flex items-center space-x-3">
                                <a href="{{ $indexUrl }}"
                                   class="px-6 py-3 text-slate-500 font-black text-[10px] uppercase tracking-widest hover:text-slate-900 transition-colors">
                                    Cancel
                                </a>
                                <button type="submit"
                                        class="inline-flex items-center px-10 py-3.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-blue-500/30 hover:shadow-blue-500/50 transform hover:scale-[1.02] active:scale-[0.98] transition-all duration-200">
                                    <i class="ri-check-line mr-2 text-lg"></i>
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Sidebar -->
                    <div class="col-span-12 lg:col-span-4 space-y-6">
                        <!-- Logo Upload -->
                        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/40 border border-slate-200/60 overflow-hidden">
                            <div class="px-6 py-4 bg-slate-50/50 border-b border-slate-100">
                                <h3 class="text-xs font-black text-slate-900 flex items-center uppercase tracking-widest">
                                    <i class="ri-image-line text-indigo-600 mr-2 text-lg"></i>
                                    Identity Logo
                                </h3>
                            </div>

                            <div class="p-6">
                                <div class="text-center mb-6">
                                    @if($organizationLogo)
                                        <div class="relative inline-block group">
                                            <img src="{{ $organizationLogo }}" alt="Current Logo" class="w-24 h-24 rounded-2xl object-cover border-4 border-white shadow-xl transition-transform group-hover:scale-105">
                                            <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-emerald-500 text-white rounded-xl flex items-center justify-center shadow-lg border-2 border-white">
                                                <i class="ri-check-line"></i>
                                            </div>
                                        </div>
                                    @else
                                        <div class="w-24 h-24 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto shadow-inner border border-slate-100">
                                            <i class="ri-building-line text-slate-300 text-3xl"></i>
                                        </div>
                                    @endif
                                </div>

                                <div class="space-y-4">
                                    <div class="relative">
                                        <input type="file"
                                               name="logo"
                                               id="logo"
                                               accept="image/*"
                                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                        <div class="flex items-center justify-center w-full px-4 py-3 bg-indigo-50 text-indigo-600 rounded-xl font-black text-[10px] uppercase tracking-widest border-2 border-dashed border-indigo-200 group-hover:bg-indigo-100 transition-all">
                                            <i class="ri-upload-2-line mr-2 text-lg"></i>
                                            Change Logo
                                        </div>
                                    </div>
                                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest text-center leading-relaxed">Max file size: 2MB.<br>Supported: JPG, PNG, SVG</p>
                                </div>
                            </div>
                        </div>

                        <!-- Organization Info -->
                        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/40 border border-slate-200/60 overflow-hidden">
                            <div class="px-6 py-4 bg-slate-50/50 border-b border-slate-100">
                                <h3 class="text-xs font-black text-slate-900 flex items-center uppercase tracking-widest">
                                    <i class="ri-information-line text-emerald-600 mr-2 text-lg"></i>
                                    Audit & Stats
                                </h3>
                            </div>

                            <div class="p-6 space-y-4">
                                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100">
                                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Unique ID</span>
                                    <span class="text-xs font-black text-slate-900">#{{ $id }}</span>
                                </div>

                                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100">
                                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Total Members</span>
                                    <span class="text-xs font-black text-slate-900">{{ number_format($membersCount) }}</span>
                                </div>

                                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100">
                                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Active Elections</span>
                                    <span class="text-xs font-black text-slate-900">{{ number_format($electionsCount) }}</span>
                                </div>

                                @if($organization->created_at ?? false)
                                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100">
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">System Created</span>
                                        <span class="text-xs font-black text-slate-900">{{ $organization->created_at->format('M d, Y') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/40 border border-slate-200/60 overflow-hidden">
                            <div class="px-6 py-4 bg-slate-50/50 border-b border-slate-100">
                                <h3 class="text-xs font-black text-slate-900 flex items-center uppercase tracking-widest">
                                    <i class="ri-flash-line text-amber-600 mr-2 text-lg"></i>
                                    Quick Links
                                </h3>
                            </div>

                            <div class="p-6 space-y-3">
                                <a href="{{ $showUrl }}"
                                   class="w-full inline-flex items-center justify-center px-4 py-3 bg-blue-50 text-blue-700 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-100 transition-all">
                                    <i class="ri-eye-line mr-2 text-base"></i>
                                    View Live Profile
                                </a>

                                <a href="{{ $indexUrl }}"
                                   class="w-full inline-flex items-center justify-center px-4 py-3 bg-slate-50 text-slate-700 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-100 transition-all">
                                    <i class="ri-list-check mr-2 text-base"></i>
                                    All Organizations
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
