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
    $organizationSlug = old('slug', $organization->slug ?? '');
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
        <div class="bg-gradient-to-r from-white/95 via-blue-50/30 to-white/95 backdrop-blur-xl border-b border-gray-200/50 sticky top-0 z-40 shadow-sm">
            <div class="px-8 py-4">
                <div class="flex items-center justify-between">
                    <!-- Left Section: Back Button & Breadcrumb -->
                    <div class="flex items-center space-x-4">
                        <a href="{{ $indexUrl }}"
                           class="flex items-center justify-center w-10 h-10 rounded-lg bg-gray-50 hover:bg-gray-100 transition-all duration-200 border border-gray-200 hover:border-gray-300">
                            <i class="ri-arrow-left-line text-gray-600"></i>
                        </a>

                        <div class="h-6 w-px bg-gray-300"></div>

                        <!-- Breadcrumb -->
                        <nav class="flex items-center space-x-2" aria-label="Breadcrumb">
                            <ol class="flex items-center space-x-2">
                                <li>
                                    <a href="{{ url('/admin') }}" class="text-gray-500 hover:text-gray-700 text-sm">Dashboard</a>
                                </li>
                                <li>
                                    <i class="ri-arrow-right-s-line text-gray-400"></i>
                                </li>
                                <li>
                                    <a href="{{ $indexUrl }}" class="text-gray-500 hover:text-gray-700 text-sm">Organizations</a>
                                </li>
                                <li>
                                    <i class="ri-arrow-right-s-line text-gray-400"></i>
                                </li>
                                <li>
                                    <a href="{{ $showUrl }}" class="text-gray-500 hover:text-gray-700 text-sm">{{ $organizationName }}</a>
                                </li>
                                <li>
                                    <i class="ri-arrow-right-s-line text-gray-400"></i>
                                </li>
                                <li class="flex items-center">
                                    <span class="text-gray-900 text-sm font-medium">Edit</span>
                                </li>
                            </ol>
                        </nav>
                    </div>

                    <!-- Right Section: Action Buttons -->
                    <div class="flex items-center space-x-3">
                        <button @click="loadDraft()"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-all duration-200">
                            <i class="ri-download-line text-sm mr-2"></i>
                            Load Draft
                        </button>

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
            <div class="max-w-7xl mx-auto">
                <div class="flex items-start justify-between mb-8">
                    <div class="flex items-start space-x-6">
                        <!-- Organization Icon -->
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600 rounded-3xl flex items-center justify-center shadow-lg shadow-blue-500/25">
                                <i class="ri-building-line text-white text-2xl"></i>
                            </div>
                        </div>

                        <div>
                            <h1 class="text-4xl font-bold text-gray-900 mb-2">Edit Organization</h1>
                            <p class="text-lg text-gray-600 mb-4">Update organization information and settings. Changes are tracked and audited.</p>

                            <!-- Status Indicator -->
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center space-x-2">
                                    @if($organizationStatus === 'active')
                                        <div class="w-3 h-3 bg-emerald-500 rounded-full"></div>
                                        <span class="text-sm font-medium text-emerald-700">Active</span>
                                    @elseif($organizationStatus === 'inactive')
                                        <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                        <span class="text-sm font-medium text-red-700">Inactive</span>
                                    @else
                                        <div class="w-3 h-3 bg-gray-500 rounded-full"></div>
                                        <span class="text-sm font-medium text-gray-700">{{ ucfirst($organizationStatus) }}</span>
                                    @endif
                                </div>

                                <div x-show="hasChanges" class="flex items-center space-x-2">
                                    <div class="w-3 h-3 bg-amber-500 rounded-full animate-pulse"></div>
                                    <span class="text-sm font-medium text-amber-700">Unsaved Changes</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Container -->
                <form action="{{ $updateAction }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                    @csrf
                    @method('PUT')

                    <!-- Main Form Content -->
                    <div class="lg:col-span-3 space-y-8">
                        <!-- Basic Information Card -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 overflow-hidden">
                            <div class="px-8 py-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200/60">
                                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                                    <i class="ri-building-line text-blue-600 mr-2"></i>
                                    Basic Information
                                </h2>
                            </div>

                            <div class="p-8">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Organization Name -->
                                    <div>
                                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Organization Name <span class="text-red-500">*</span></label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="ri-building-4-line text-gray-400"></i>
                                            </div>
                                            <input type="text"
                                                   id="name"
                                                   name="name"
                                                   x-model="formData.name"
                                                   @input="generateSlug()"
                                                   class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                                                   placeholder="Enter organization name"
                                                   required>
                                        </div>
                                        @error('name')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Organization Slug -->
                                    <div>
                                        <label for="slug" class="block text-sm font-semibold text-gray-700 mb-2">Organization Slug</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="ri-link text-gray-400"></i>
                                            </div>
                                            <input type="text"
                                                   id="slug"
                                                   name="slug"
                                                   x-model="formData.slug"
                                                   class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('slug') border-red-500 @enderror"
                                                   placeholder="auto-generated-slug">
                                        </div>
                                        @error('slug')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Description -->
                                    <div class="md:col-span-2">
                                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                                        <textarea id="description"
                                                  name="description"
                                                  x-model="formData.description"
                                                  rows="4"
                                                  class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror"
                                                  placeholder="Brief description of the organization..."></textarea>
                                        @error('description')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information Card -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 overflow-hidden">
                            <div class="px-8 py-6 bg-gradient-to-r from-emerald-50 to-teal-50 border-b border-gray-200/60">
                                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                                    <i class="ri-contacts-line text-emerald-600 mr-2"></i>
                                    Contact Information
                                </h2>
                            </div>

                            <div class="p-8">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Email -->
                                    <div>
                                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Contact Email</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="ri-mail-line text-gray-400"></i>
                                            </div>
                                            <input type="email"
                                                   id="email"
                                                   name="email"
                                                   x-model="formData.email"
                                                   class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('email') border-red-500 @enderror"
                                                   placeholder="contact@organization.com">
                                        </div>
                                        @error('email')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Phone -->
                                    <div>
                                        <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Contact Phone</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="ri-phone-line text-gray-400"></i>
                                            </div>
                                            <input type="tel"
                                                   id="phone"
                                                   name="phone"
                                                   x-model="formData.phone"
                                                   class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                                                   placeholder="+1 (555) 123-4567">
                                        </div>
                                        @error('phone')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Address -->
                                    <div class="md:col-span-2">
                                        <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">Address</label>
                                        <div class="relative">
                                            <div class="absolute top-3 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="ri-map-pin-line text-gray-400"></i>
                                            </div>
                                            <textarea id="address"
                                                      name="address"
                                                      x-model="formData.address"
                                                      rows="3"
                                                      class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('address') border-red-500 @enderror"
                                                      placeholder="Full organization address..."></textarea>
                                        </div>
                                        @error('address')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Settings Card -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 overflow-hidden">
                            <div class="px-8 py-6 bg-gradient-to-r from-purple-50 to-pink-50 border-b border-gray-200/60">
                                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                                    <i class="ri-settings-3-line text-purple-600 mr-2"></i>
                                    Organization Settings
                                </h2>
                            </div>

                            <div class="p-8">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Status -->
                                    <div>
                                        <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="ri-toggle-line text-gray-400"></i>
                                            </div>
                                            <select id="status"
                                                    name="status"
                                                    x-model="formData.status"
                                                    class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('status') border-red-500 @enderror">
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                                <option value="pending">Pending</option>
                                                <option value="suspended">Suspended</option>
                                            </select>
                                        </div>
                                        @error('status')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 overflow-hidden">
                            <div class="p-8">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <button type="button"
                                                @click="confirmDelete()"
                                                class="inline-flex items-center px-6 py-3 border border-red-300 rounded-lg text-red-700 bg-red-50 hover:bg-red-100 hover:border-red-400 transition-all duration-200 font-semibold">
                                            <i class="ri-delete-bin-line mr-2"></i>
                                            Delete Organization
                                        </button>

                                        <button type="button"
                                                @click="saveDraft()"
                                                class="inline-flex items-center px-6 py-3 border border-blue-300 rounded-lg text-blue-700 bg-blue-50 hover:bg-blue-100 hover:border-blue-400 transition-all duration-200 font-semibold">
                                            <i class="ri-save-line mr-2"></i>
                                            Save Draft
                                        </button>
                                    </div>

                                    <div class="flex items-center space-x-4">
                                        <a href="{{ $indexUrl }}"
                                           class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 font-semibold">
                                            Cancel
                                        </a>

                                        <button type="submit"
                                                class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg shadow-blue-500/25 font-semibold">
                                            <i class="ri-check-line mr-2"></i>
                                            Update Organization
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Sidebar -->
                    <div class="space-y-6">
                        <!-- Logo Upload -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 overflow-hidden">
                            <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-gray-200/60">
                                <h3 class="text-lg font-bold text-gray-900 flex items-center">
                                    <i class="ri-image-line text-indigo-600 mr-2"></i>
                                    Organization Logo
                                </h3>
                            </div>

                            <div class="p-6">
                                <div class="text-center mb-6">
                                    @if($organizationLogo)
                                        <img src="{{ $organizationLogo }}" alt="Current Logo" class="w-24 h-24 rounded-xl object-cover mx-auto border-4 border-white shadow-lg">
                                    @else
                                        <div class="w-24 h-24 bg-gradient-to-br from-gray-400 to-gray-500 rounded-xl flex items-center justify-center mx-auto shadow-lg">
                                            <i class="ri-building-line text-white text-3xl"></i>
                                        </div>
                                    @endif
                                </div>

                                <div class="space-y-4">
                                    <input type="file"
                                           name="logo"
                                           id="logo"
                                           accept="image/*"
                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    <p class="text-xs text-gray-500">Max file size: 2MB. Supported: JPG, PNG, SVG</p>
                                </div>
                            </div>
                        </div>

                        <!-- Organization Info -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 overflow-hidden">
                            <div class="px-6 py-4 bg-gradient-to-r from-emerald-50 to-teal-50 border-b border-gray-200/60">
                                <h3 class="text-lg font-bold text-gray-900 flex items-center">
                                    <i class="ri-information-line text-emerald-600 mr-2"></i>
                                    Organization Info
                                </h3>
                            </div>

                            <div class="p-6 space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Organization ID</span>
                                    <span class="text-sm font-medium text-gray-900">#{{ $id }}</span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Members</span>
                                    <span class="text-sm font-medium text-gray-900">{{ number_format($membersCount) }}</span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Elections</span>
                                    <span class="text-sm font-medium text-gray-900">{{ number_format($electionsCount) }}</span>
                                </div>

                                @if($organization->created_at ?? false)
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Created</span>
                                        <span class="text-sm font-medium text-gray-900">{{ $organization->created_at->format('M d, Y') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 overflow-hidden">
                            <div class="px-6 py-4 bg-gradient-to-r from-amber-50 to-orange-50 border-b border-gray-200/60">
                                <h3 class="text-lg font-bold text-gray-900 flex items-center">
                                    <i class="ri-flash-line text-amber-600 mr-2"></i>
                                    Quick Actions
                                </h3>
                            </div>

                            <div class="p-6 space-y-3">
                                <a href="{{ $showUrl }}"
                                   class="w-full inline-flex items-center justify-center px-4 py-3 bg-blue-50 text-blue-700 rounded-xl text-sm font-medium hover:bg-blue-100 transition-all duration-200">
                                    <i class="ri-eye-line mr-2"></i>
                                    View Organization
                                </a>

                                <a href="{{ $indexUrl }}"
                                   class="w-full inline-flex items-center justify-center px-4 py-3 bg-gray-50 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-100 transition-all duration-200">
                                    <i class="ri-list-check mr-2"></i>
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
