@extends('layouts.app-main-admin')

@php
    $id = $party->id ?? 0;

    $indexUrl = Route::has('admin.partylists.index') ? route('admin.partylists.index') :
                (Route::has('partylists.index') ? route('partylists.index') : url('/admin/partylists'));

    if (Route::has('admin.partylists.update')) {
        $updateAction = route('admin.partylists.update', $id);
    } elseif (Route::has('partylists.update')) {
        $updateAction = route('partylists.update', $id);
    } else {
        $updateAction = url('/admin/partylists/'.$id);
    }

    if (Route::has('admin.partylists.destroy')) {
        $destroyAction = route('admin.partylists.destroy', $id);
    } elseif (Route::has('partylists.destroy')) {
        $destroyAction = route('partylists.destroy', $id);
    } else {
        $destroyAction = url('/admin/partylists/'.$id);
    }
@endphp

@section('title', isset($party->name) ? 'Edit Partylist — '.$party->name : 'Edit Partylist')

@section('content')
    <div x-data="{
    formData: {
        name: '{{ old('name', $party->name ?? '') }}',
        acronym: '{{ old('acronym', $party->acronym ?? '') }}',
        description: '{{ old('description', $party->description ?? '') }}',
        platform: '{{ old('platform', $party->platform ?? '') }}',
        color: '{{ old('color', $party->color ?? '#3b82f6') }}',
        organization_id: '{{ old('organization_id', $party->organization_id ?? '') }}',
        status: '{{ old('status', $party->status ?? 'active') }}',
        logo: null,
        logoName: ''
    },
    errors: {},
    loading: false,
    logoPreview: '{{ isset($party->logo) && Storage::exists('public/partylists/' . $party->logo) ? asset('storage/partylists/' . $party->logo) : '' }}',
    logoDragOver: false,
    showDeleteModal: false,
    isDirty: false,

    async submitForm() {
        if (!this.validateForm()) return;
        this.loading = true;
        this.errors = {};

        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name=csrf-token]').content);
        formData.append('_method', 'PUT');

        Object.keys(this.formData).forEach(key => {
            if (key === 'logo' && this.formData[key]) {
                formData.append('logo', this.formData[key]);
            } else if (key !== 'logo' && key !== 'logoName') {
                formData.append(key, this.formData[key] || '');
            }
        });

        try {
            const response = await fetch('{{ $updateAction }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                this.showNotification('Party list updated successfully!', 'success');
                this.isDirty = false;
                setTimeout(() => window.location.href = '{{ $indexUrl }}', 1500);
            } else {
                const data = await response.json();
                if (data.errors) {
                    this.errors = data.errors;
                    this.showNotification('Please check the form for errors', 'error');
                } else {
                    this.showNotification('An unexpected error occurred', 'error');
                }
            }
        } catch (error) {
            this.showNotification('An error occurred. Please try again.', 'error');
        } finally {
            this.loading = false;
        }
    },

    validateForm() {
        this.errors = {};
        let valid = true;

        if (!this.formData.name.trim()) {
            this.errors.name = ['Party name is required'];
            valid = false;
        }

        if (!this.formData.organization_id) {
            this.errors.organization_id = ['Organization is required'];
            valid = false;
        }

        return valid;
    },

    confirmDelete() {
        this.showDeleteModal = false;
        document.getElementById('party-delete-form').submit();
    },

    handleLogoUpload(event) {
        const file = event.target && event.target.files ? event.target.files[0] : event;
        if (!file) return;

        if (file.size > 2 * 1024 * 1024) {
            this.showNotification('File size must not exceed 2MB', 'error');
            return;
        }

        if (!file.type.startsWith('image/')) {
            this.showNotification('Please upload a valid image file', 'error');
            return;
        }

        this.formData.logo = file;
        this.formData.logoName = file.name;

        const reader = new FileReader();
        reader.onload = (e) => this.logoPreview = e.target.result;
        reader.readAsDataURL(file);
        this.isDirty = true;
    },

    handleDragOver(e) {
        e.preventDefault();
        this.logoDragOver = true;
    },

    handleDragLeave() {
        this.logoDragOver = false;
    },

    handleDrop(e) {
        e.preventDefault();
        this.logoDragOver = false;
        const file = e.dataTransfer.files[0];
        if (file) this.handleLogoUpload(file);
    },

    generateAcronym() {
        if (this.formData.name) {
            const words = this.formData.name.trim().split(/\s+/);
            this.formData.acronym = words.map(word => word.charAt(0).toUpperCase()).join('');
            this.isDirty = true;
        }
    },

    showNotification(message, type = 'info') {
        // Build notification safely (no string concatenation with HTML)
        const colors = {
            success: 'bg-emerald-500',
            error: 'bg-red-500',
            info: 'bg-blue-500',
            warning: 'bg-yellow-500'
        };

        const notification = document.createElement('div');
        notification.setAttribute('role', 'status');
        notification.className = 'fixed top-6 right-6 text-white px-5 py-3 rounded-lg shadow-lg z-50 max-w-xs flex items-center space-x-3';
        notification.classList.add(colors[type] || colors.info);
        notification.style.transition = 'transform 300ms ease, opacity 300ms ease';
        notification.style.transform = 'translateX(120%)';
        notification.style.opacity = '0';

        const icon = document.createElement('span');
        icon.className = 'text-lg';
        // optional icon mapping (keeps markup safe)
        icon.textContent = type === 'success' ? '✓' : type === 'error' ? '⚠' : 'ℹ';
        icon.setAttribute('aria-hidden', 'true');

        const text = document.createElement('div');
        text.className = 'text-sm';
        text.textContent = message;

        notification.appendChild(icon);
        notification.appendChild(text);

        document.body.appendChild(notification);

        // Trigger enter animation
        requestAnimationFrame(() => {
            notification.style.transform = 'translateX(0)';
            notification.style.opacity = '1';
        });

        // Auto remove
        setTimeout(() => {
            notification.style.transform = 'translateX(120%)';
            notification.style.opacity = '0';
            setTimeout(() => {
                if (notification && notification.parentNode) notification.parentNode.removeChild(notification);
            }, 300);
        }, 3000);
    }
    }"
         x-init="
    $watch('formData', () => isDirty = true, { deep: true });
    window.addEventListener('beforeunload', (e) => {
    if (isDirty) {
    e.preventDefault();
    e.returnValue = '';
    }
    });
    "
         class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">

        <!-- Enhanced Floating Save Button -->
        <div class="fixed bottom-6 right-6 z-50 flex flex-col space-y-3">
            <button type="button" @click="submitForm()"
                    :disabled="loading"
                    class="flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-full shadow-xl hover:shadow-2xl hover:from-blue-700 hover:to-blue-800 disabled:opacity-50 transform transition-all duration-300 hover:scale-105 focus:ring-4 focus:ring-blue-300 group">
                <i class="ri-save-line mr-2 group-hover:animate-pulse" x-show="!loading"></i>
                <i class="ri-loader-4-line mr-2 animate-spin" x-show="loading"></i>
                <span x-text="loading ? 'Saving...' : 'Save Changes'" class="font-medium"></span>
            </button>

            <div x-show="isDirty" x-transition class="text-center">
            <span class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full border border-yellow-200">
                <i class="ri-error-warning-line mr-1"></i>
                Unsaved changes
            </span>
            </div>
        </div>

        <!-- Enhanced Delete Modal -->
        <div x-show="showDeleteModal" x-cloak class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div x-show="showDeleteModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="bg-white rounded-2xl w-full max-w-md p-6 shadow-2xl border border-gray-200">
                <div class="flex items-center space-x-4 mb-6">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="ri-error-warning-line text-xl text-red-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Delete Party List</h3>
                        <p class="text-gray-500 text-sm">This action cannot be undone.</p>
                    </div>
                </div>
                <p class="text-gray-600 mb-6">Are you sure you want to delete <strong class="text-gray-900">{{ $party->name }}</strong>?</p>
                <div class="flex justify-end space-x-3">
                    <button type="button" @click="showDeleteModal = false"
                            class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors font-medium">Cancel</button>
                    <button type="button" @click="confirmDelete()"
                            class="px-6 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg hover:from-red-700 hover:to-red-800 transition-all font-medium shadow-lg">Delete</button>
                </div>
            </div>
        </div>

        <!-- Enhanced Header -->
        <div class="bg-white/90 backdrop-blur-md border-b border-gray-200/50 shadow-sm sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-6 py-4">
                <div class="flex items-center justify-between">
                    <!-- Enhanced Breadcrumb -->
                    <div class="flex items-center space-x-4">
                        <a href="{{ $indexUrl }}" class="p-2 hover:bg-gray-100 rounded-xl transition-all duration-200 hover:scale-105 group">
                            <i class="ri-arrow-left-line text-gray-600 text-lg group-hover:text-blue-600"></i>
                        </a>
                        <nav class="flex items-center space-x-2 text-sm text-gray-600">
                            <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 transition-colors font-medium">Dashboard</a>
                            <i class="ri-arrow-right-s-line text-gray-400"></i>
                            <a href="{{ $indexUrl }}" class="hover:text-blue-600 transition-colors font-medium">Partylists</a>
                            <i class="ri-arrow-right-s-line text-gray-400"></i>
                            <span class="text-gray-900 font-semibold">Edit {{ $party->name ?? 'Partylist' }}</span>
                        </nav>
                    </div>

                    <!-- Header Actions -->
                    <div class="flex items-center space-x-4">
                        <div x-show="isDirty" x-transition class="flex items-center text-sm text-yellow-700 bg-yellow-50 px-3 py-1 rounded-full border border-yellow-200">
                            <i class="ri-error-warning-line mr-1"></i>
                            Unsaved changes
                        </div>

                        <button type="button" @click="submitForm()"
                                :disabled="loading"
                                class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 disabled:opacity-50 transition-all duration-200 shadow-lg font-medium focus:ring-4 focus:ring-blue-300">
                            <i class="ri-save-line mr-2" x-show="!loading"></i>
                            <i class="ri-loader-4-line mr-2 animate-spin" x-show="loading"></i>
                            <span x-text="loading ? 'Saving...' : 'Save Changes'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Progress Bar -->
        <div class="bg-white/90 backdrop-blur-md border-b border-gray-200/50">
            <div class="max-w-7xl mx-auto px-6">
                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 transition-all duration-700 ease-out rounded-full relative"
                         :style="`width: ${Math.min(100, ((formData.name ? 20 : 0) + (formData.organization_id ? 20 : 0) + (formData.description ? 20 : 0) + (formData.platform ? 20 : 0) + (logoPreview ? 20 : 0)))}%`">
                        <div class="absolute inset-0 bg-white/30 animate-pulse rounded-full"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Main Content -->
        <div class="max-w-7xl mx-auto px-6 py-8">
            <!-- Enhanced Page Header -->
            <div class="mb-8 text-center lg:text-left">
                <h1 class="text-4xl font-bold bg-gradient-to-r from-gray-900 via-gray-800 to-gray-700 bg-clip-text text-transparent mb-3">Edit Party List</h1>
                <p class="text-gray-600 text-lg">Update party list information and settings with enhanced design.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Enhanced Main Form -->
                <div class="lg:col-span-3 space-y-8">
                    <!-- Basic Information Card -->
                    <div class="bg-white/95 backdrop-blur-sm rounded-2xl border border-gray-200/60 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200/50 bg-gradient-to-r from-blue-50/80 to-indigo-50/80">
                            <h2 class="text-xl font-bold text-gray-900 flex items-center">
                                <i class="ri-information-line mr-2 text-blue-600"></i>
                                Basic Information
                            </h2>
                            <p class="text-gray-600 text-sm mt-1">Essential party details and identification</p>
                        </div>
                        <div class="p-8 space-y-8">
                            <!-- Party Name -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Party Name *</label>
                                <div class="relative">
                                    <input type="text"
                                           x-model="formData.name"
                                           @input="generateAcronym()"
                                           placeholder="Enter party name"
                                           class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all text-gray-900 placeholder-gray-400">
                                    <button type="button"
                                            @click="generateAcronym()"
                                            class="absolute right-3 top-3 text-blue-600 hover:text-blue-700 transition-colors"
                                            title="Generate Acronym">
                                        <i class="ri-refresh-line"></i>
                                    </button>
                                </div>
                                <div x-show="errors.name" x-transition class="text-red-500 text-sm">
                                    <span x-text="errors.name ? errors.name[0] : ''"></span>
                                </div>
                            </div>

                            <!-- Acronym and Status -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Acronym</label>
                                    <input type="text"
                                           x-model="formData.acronym"
                                           placeholder="Auto-generated or custom"
                                           class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all text-gray-900 placeholder-gray-400">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Status</label>
                                    <select x-model="formData.status"
                                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all text-gray-900">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="suspended">Suspended</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Organization -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Organization *</label>
                                <select x-model="formData.organization_id"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all text-gray-900">
                                    <option value="">Select Organization</option>
                                    @foreach($organizations ?? [] as $org)
                                        <option value="{{ $org->id }}">{{ $org->name }}</option>
                                    @endforeach
                                </select>
                                <div x-show="errors.organization_id" x-transition class="text-red-500 text-sm">
                                    <span x-text="errors.organization_id ? errors.organization_id[0] : ''"></span>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Description</label>
                                <textarea x-model="formData.description"
                                          placeholder="Brief description of the party..."
                                          rows="4"
                                          class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all resize-none text-gray-900 placeholder-gray-400"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Platform Section -->
                    <div class="bg-white/95 backdrop-blur-sm rounded-2xl border border-gray-200/60 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200/50 bg-gradient-to-r from-green-50/80 to-emerald-50/80">
                            <h2 class="text-xl font-bold text-gray-900 flex items-center">
                                <i class="ri-file-text-line mr-2 text-green-600"></i>
                                Platform & Agenda
                            </h2>
                            <p class="text-gray-600 text-sm mt-1">Party platform and political agenda</p>
                        </div>
                        <div class="p-8">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Platform Details</label>
                            <textarea x-model="formData.platform"
                                      placeholder="Describe the party's platform, policies, and agenda..."
                                      rows="6"
                                      class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all resize-none text-gray-900 placeholder-gray-400"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Sidebar -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Logo Upload -->
                    <div class="bg-white/95 backdrop-blur-sm rounded-2xl border border-gray-200/60 p-6 shadow-xl hover:shadow-2xl transition-all duration-300">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                            <i class="ri-image-line mr-2 text-purple-600"></i>
                            Party Logo
                        </h3>

                        <div class="space-y-4">
                            <!-- Enhanced Logo Preview -->
                            <div class="w-full h-48 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden transition-all duration-300 hover:border-purple-400 cursor-pointer"
                                 :class="{ 'border-purple-500 bg-purple-50': logoDragOver }"
                                 @dragover="handleDragOver"
                                 @dragleave="handleDragLeave"
                                 @drop="handleDrop"
                                 @click="$refs.logoInput.click()">
                                <div x-show="logoPreview" class="w-full h-full">
                                    <img :src="logoPreview" alt="Logo Preview" class="w-full h-full object-cover rounded-xl">
                                </div>
                                <div x-show="!logoPreview" class="text-center text-gray-500">
                                    <i class="ri-image-add-line text-4xl mb-2"></i>
                                    <p class="text-sm">Drop logo here or click to upload</p>
                                    <p class="text-xs text-gray-400">Max 2MB • JPG, PNG, GIF</p>
                                </div>
                            </div>

                            <!-- Upload Controls -->
                            <input type="file"
                                   x-ref="logoInput"
                                   @change="handleLogoUpload($event)"
                                   accept="image/*"
                                   class="hidden">

                            <div class="flex space-x-2">
                                <button type="button"
                                        @click="$refs.logoInput.click()"
                                        class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-sm font-medium">
                                    <i class="ri-upload-2-line mr-1"></i>
                                    Choose Logo
                                </button>
                                <button type="button"
                                        @click="logoPreview = ''; formData.logo = null; formData.logoName = ''"
                                        x-show="logoPreview"
                                        class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Color Theme -->
                    <div class="bg-white/95 backdrop-blur-sm rounded-2xl border border-gray-200/60 p-6 shadow-xl hover:shadow-2xl transition-all duration-300">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                            <i class="ri-palette-line mr-2 text-orange-600"></i>
                            Brand Color
                        </h3>

                        <div class="space-y-4">
                            <div class="relative">
                                <input type="color"
                                       x-model="formData.color"
                                       class="w-full h-12 rounded-xl border-2 border-gray-300 cursor-pointer">
                                <div class="absolute inset-0 rounded-xl ring-4 ring-white shadow-lg pointer-events-none"></div>
                            </div>

                            <div class="grid grid-cols-4 gap-3">
                                <button type="button" @click="formData.color = '#3b82f6'" class="w-full h-10 bg-blue-500 rounded-lg hover:scale-110 transition-transform shadow-lg border-2 border-white" title="Blue"></button>
                                <button type="button" @click="formData.color = '#ef4444'" class="w-full h-10 bg-red-500 rounded-lg hover:scale-110 transition-transform shadow-lg border-2 border-white" title="Red"></button>
                                <button type="button" @click="formData.color = '#10b981'" class="w-full h-10 bg-emerald-500 rounded-lg hover:scale-110 transition-transform shadow-lg border-2 border-white" title="Emerald"></button>
                                <button type="button" @click="formData.color = '#f59e0b'" class="w-full h-10 bg-amber-500 rounded-lg hover:scale-110 transition-transform shadow-lg border-2 border-white" title="Amber"></button>
                                <button type="button" @click="formData.color = '#8b5cf6'" class="w-full h-10 bg-violet-500 rounded-lg hover:scale-110 transition-transform shadow-lg border-2 border-white" title="Violet"></button>
                                <button type="button" @click="formData.color = '#06b6d4'" class="w-full h-10 bg-cyan-500 rounded-lg hover:scale-110 transition-transform shadow-lg border-2 border-white" title="Cyan"></button>
                                <button type="button" @click="formData.color = '#84cc16'" class="w-full h-10 bg-lime-500 rounded-lg hover:scale-110 transition-transform shadow-lg border-2 border-white" title="Lime"></button>
                                <button type="button" @click="formData.color = '#64748b'" class="w-full h-10 bg-slate-500 rounded-lg hover:scale-110 transition-transform shadow-lg border-2 border-white" title="Slate"></button>
                            </div>

                            <div class="text-xs text-gray-600 bg-gray-50 p-3 rounded-xl border">
                                <span class="font-medium">Selected: </span>
                                <code x-text="formData.color" class="bg-white px-2 py-1 rounded border font-mono"></code>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Actions -->
                    <div class="bg-white/95 backdrop-blur-sm rounded-2xl border border-gray-200/60 p-6 shadow-xl hover:shadow-2xl transition-all duration-300">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                            <i class="ri-settings-3-line mr-2 text-gray-600"></i>
                            Actions
                        </h3>

                        <div class="space-y-4">
                            <button type="button" @click="submitForm()"
                                    :disabled="loading"
                                    class="w-full px-4 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 disabled:opacity-50 transition-all transform hover:scale-105 font-medium shadow-lg focus:ring-4 focus:ring-blue-300">
                                <i class="ri-save-line mr-2" x-show="!loading"></i>
                                <i class="ri-loader-4-line mr-2 animate-spin" x-show="loading"></i>
                                <span x-text="loading ? 'Saving...' : 'Save Changes'"></span>
                            </button>

                            <a href="{{ $indexUrl }}" class="block w-full px-4 py-4 text-center border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all font-medium">
                                <i class="ri-close-line mr-2"></i>
                                Cancel
                            </a>

                            @if(isset($party->id))
                                <button type="button"
                                        @click="showDeleteModal = true"
                                        class="w-full px-4 py-4 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-xl hover:from-red-700 hover:to-red-800 transition-all font-medium shadow-lg">
                                    <i class="ri-delete-bin-line mr-2"></i>
                                    Delete Party List
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden Delete Form -->
        @if(isset($party->id))
            <form id="party-delete-form" action="{{ $destroyAction }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </div>

    <style>
        [x-cloak] { display: none !important; }

        /* Enhanced scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, #cbd5e1, #94a3b8);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(to bottom, #94a3b8, #64748b);
        }

        /* Custom focus styles */
        input:focus, select:focus, textarea:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
    </style>
@endsection
