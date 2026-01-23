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

@section('content')
    <div x-data="partylistEditData()" class="min-h-screen bg-slate-50 flex">
        <x-admin-sidebar />

        <main class="flex-1">
            <!-- Top Navigation Bar -->
            <div class="bg-white border-b border-slate-200 sticky top-0 z-40">
                <div class="max-w-7xl mx-auto px-4 sm:px-8 py-4">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] overflow-x-auto no-scrollbar whitespace-nowrap">
                            <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-600 transition-colors">Dashboard</a>
                            <i class="ri-arrow-right-s-line text-xs"></i>
                            <a href="{{ $indexUrl }}" class="hover:text-indigo-600 transition-colors">Partylists</a>
                            <i class="ri-arrow-right-s-line text-xs text-slate-300"></i>
                            <span class="font-black text-slate-900">Edit Partylist</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-8 py-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Header -->
                        <div class="flex items-start gap-4 sm:gap-5 mb-8">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-indigo-600 flex items-center justify-center flex-shrink-0 shadow-lg shadow-indigo-100">
                                <i class="ri-flag-2-line text-white text-xl sm:text-2xl"></i>
                            </div>
                            <div class="min-w-0">
                                <h1 class="text-xl sm:text-3xl font-black text-slate-900 uppercase tracking-tight truncate sm:whitespace-normal">Edit Party List</h1>
                                <p class="text-slate-500 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1">Update party information and settings</p>
                            </div>
                        </div>

                        <form @submit.prevent="submitForm()" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <!-- Party Preview Header -->
                            <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-200/40 overflow-hidden">
                                <div class="h-20 bg-gradient-to-r from-indigo-600 via-blue-500 to-indigo-600" :style="`background: linear-gradient(to right, ${formData.color}, ${formData.color}dd)`"></div>
                                <div class="px-6 pb-6">
                                    <div class="relative flex items-end gap-4 sm:gap-5 -mt-8">
                                        <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white rounded-2xl p-1 shadow-xl">
                                            <div class="w-full h-full bg-slate-100 rounded-xl flex items-center justify-center text-slate-400 overflow-hidden">
                                                <template x-if="logoPreview">
                                                    <img :src="logoPreview" class="w-full h-full object-cover">
                                                </template>
                                                <template x-if="!logoPreview">
                                                    <i class="ri-flag-line text-2xl sm:text-3xl"></i>
                                                </template>
                                            </div>
                                        </div>
                                        <div class="flex-1 mb-1.5 min-w-0">
                                            <h1 class="text-lg sm:text-xl font-black text-slate-900 uppercase tracking-tight truncate" x-text="formData.name || 'New Party List'"></h1>
                                            <p class="text-[9px] text-slate-500 font-bold tracking-widest uppercase mt-0.5 truncate">Acronym: <span x-text="formData.acronym || 'N/A'"></span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Basic Information Card -->
                            <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-200/40 overflow-hidden transition-all hover:shadow-2xl hover:shadow-slate-200/50">
                                <div class="p-6 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center">
                                            <i class="ri-information-line text-white text-xl"></i>
                                        </div>
                                        <div>
                                            <h2 class="text-base font-black text-slate-800 uppercase tracking-tight">Basic Information</h2>
                                            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Essential Details</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-8 space-y-6">
                                        <div class="space-y-2">
                                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Party Name *</label>
                                            <div class="relative group">
                                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                                    <i class="ri-flag-2-line text-slate-400 group-focus-within:text-indigo-600 transition-colors"></i>
                                                </div>
                                                <input type="text" x-model="formData.name" @input="generateAcronym()"
                                                       class="w-full pl-11 pr-12 py-3.5 bg-slate-50 border-2 border-slate-100 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white focus:border-indigo-500 transition-all font-bold text-slate-700 text-sm shadow-sm"
                                                       placeholder="Enter official party name">
                                                <button type="button" @click="generateAcronym()" class="absolute right-4 top-1/2 -translate-y-1/2 text-indigo-600 hover:text-indigo-700 p-1 hover:bg-indigo-50 rounded-lg transition-all" title="Regenerate Acronym">
                                                    <i class="ri-refresh-line"></i>
                                                </button>
                                            </div>
                                            <div x-show="errors.name" class="mt-1 text-[9px] text-red-500 font-black uppercase tracking-wider ml-1" x-text="errors.name?.[0]"></div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div class="space-y-2">
                                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Acronym</label>
                                                <div class="relative group">
                                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                                        <i class="ri-font-size text-slate-400 group-focus-within:text-indigo-600 transition-colors"></i>
                                                    </div>
                                                    <input type="text" x-model="formData.acronym"
                                                           class="w-full pl-11 pr-5 py-3.5 bg-slate-50 border-2 border-slate-100 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white focus:border-indigo-500 transition-all font-bold text-slate-700 text-sm shadow-sm"
                                                           placeholder="Short name">
                                                </div>
                                            </div>
                                            <div class="space-y-2">
                                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Status</label>
                                                <div class="relative group">
                                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                                        <i class="ri-checkbox-circle-line text-slate-400 group-focus-within:text-indigo-600 transition-colors"></i>
                                                    </div>
                                                    <select x-model="formData.status"
                                                            class="w-full pl-11 pr-10 py-3.5 bg-slate-50 border-2 border-slate-100 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white focus:border-indigo-500 transition-all font-bold text-slate-700 text-sm shadow-sm appearance-none cursor-pointer">
                                                        <option value="active">Active</option>
                                                        <option value="inactive">Inactive</option>
                                                        <option value="suspended">Suspended</option>
                                                    </select>
                                                    <i class="ri-arrow-down-s-line absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div class="space-y-2">
                                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Organization *</label>
                                                <div class="relative group">
                                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                                        <i class="ri-building-line text-slate-400 group-focus-within:text-indigo-600 transition-colors"></i>
                                                    </div>
                                                    <select x-model="formData.organization_id"
                                                            class="w-full pl-11 pr-10 py-3.5 bg-slate-50 border-2 border-slate-100 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white focus:border-indigo-500 transition-all font-bold text-slate-700 text-sm shadow-sm appearance-none cursor-pointer">
                                                        <option value="">Select Organization</option>
                                                        @foreach($organizations ?? [] as $org)
                                                            <option value="{{ $org->id }}">{{ $org->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <i class="ri-arrow-down-s-line absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                                                </div>
                                                <div x-show="errors.organization_id" class="mt-1 text-[9px] text-red-500 font-black uppercase tracking-wider ml-1" x-text="errors.organization_id?.[0]"></div>
                                            </div>
                                            <div class="space-y-2">
                                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Election</label>
                                                <div class="relative group">
                                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                                        <i class="ri-database-2-line text-slate-400 group-focus-within:text-indigo-600 transition-colors"></i>
                                                    </div>
                                                    <select x-model="formData.election_id"
                                                            class="w-full pl-11 pr-10 py-3.5 bg-slate-50 border-2 border-slate-100 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white focus:border-indigo-500 transition-all font-bold text-slate-700 text-sm shadow-sm appearance-none cursor-pointer">
                                                        <option value="">Select Election</option>
                                                        @foreach($elections ?? [] as $election)
                                                            <option value="{{ $election->id }}">{{ $election->title }}</option>
                                                        @endforeach
                                                    </select>
                                                    <i class="ri-arrow-down-s-line absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="space-y-2">
                                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Description</label>
                                            <div class="relative group">
                                                <div class="absolute top-4 left-4 pointer-events-none">
                                                    <i class="ri-text-snippet text-slate-400 group-focus-within:text-indigo-600 transition-colors"></i>
                                                </div>
                                                <textarea x-model="formData.description" rows="4"
                                                          class="w-full pl-11 pr-5 py-3.5 bg-slate-50 border-2 border-slate-100 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white focus:border-indigo-500 transition-all font-bold text-slate-700 text-sm shadow-sm resize-none"
                                                          placeholder="Briefly describe the party list..."></textarea>
                                            </div>
                                        </div>
                                </div>
                            </div>

                            <!-- Platform Card -->
                            <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-200/40 overflow-hidden transition-all hover:shadow-2xl hover:shadow-slate-200/50">
                                <div class="p-6 bg-slate-50/50 border-b border-slate-100 flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center">
                                        <i class="ri-file-text-line text-white text-xl"></i>
                                    </div>
                                    <div>
                                        <h2 class="text-base font-black text-slate-800 uppercase tracking-tight">Platform & Agenda</h2>
                                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Political Objectives</p>
                                    </div>
                                </div>
                                <div class="p-8">
                                    <textarea x-model="formData.platform" rows="6"
                                              class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all font-bold text-slate-700 text-sm shadow-sm resize-none"
                                              placeholder="Describe the party's platform and agenda..."></textarea>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Sidebar Content -->
                    <div class="space-y-6">
                        <!-- Logo Card -->
                        <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-200/40 p-6">
                            <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <i class="ri-image-line text-indigo-600"></i>
                                Party Logo
                            </h3>

                            <div class="space-y-4">
                                <div class="relative group cursor-pointer"
                                     @click="$refs.logoInput.click()"
                                     @dragover.prevent="logoDragOver = true"
                                     @dragleave.prevent="logoDragOver = false"
                                     @drop.prevent="logoDragOver = false; handleLogoUpload($event)">
                                    <div class="aspect-square bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200 flex items-center justify-center overflow-hidden transition-all group-hover:border-indigo-400 group-hover:bg-slate-100"
                                         :class="{'border-indigo-500 bg-indigo-50': logoDragOver}">
                                        <template x-if="logoPreview">
                                            <img :src="logoPreview" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!logoPreview">
                                            <div class="text-center">
                                                <i class="ri-image-add-line text-3xl text-slate-300"></i>
                                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-2">Upload Logo</p>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="absolute inset-0 bg-indigo-600/0 group-hover:bg-indigo-600/5 transition-all rounded-2xl"></div>
                                </div>

                                <input type="file" x-ref="logoInput" class="hidden" @change="handleLogoUpload" accept="image/*">

                                <div class="flex gap-2">
                                    <button type="button" @click="$refs.logoInput.click()"
                                            class="flex-1 px-4 py-2.5 bg-indigo-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100">
                                        Browse
                                    </button>
                                    <button type="button" x-show="logoPreview" @click="logoPreview = ''; formData.logo = null"
                                            class="px-4 py-2.5 bg-red-50 text-red-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-100 transition-all">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Theme Card -->
                        <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-200/40 p-6">
                            <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <i class="ri-palette-line text-indigo-600"></i>
                                Brand Theme
                            </h3>
                            <div class="space-y-4">
                                <div class="flex items-center gap-4 p-3 bg-slate-50 rounded-2xl">
                                    <input type="color" x-model="formData.color" class="w-12 h-12 rounded-xl border-none cursor-pointer bg-transparent">
                                    <div class="flex-1">
                                        <p class="text-[10px] font-black text-slate-800 uppercase tracking-widest" x-text="formData.color"></p>
                                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Selected Color</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-5 gap-2">
                                    <template x-for="c in ['#4F46E5', '#2563EB', '#16A34A', '#DC2626', '#D97706', '#7C3AED', '#DB2777', '#0891B2', '#475569', '#111827']">
                                        <button type="button" @click="formData.color = c"
                                                class="w-full aspect-square rounded-lg border-2 border-white shadow-sm transition-transform hover:scale-110"
                                                :style="`background: ${c}`"></button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Action Card -->
                        <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-200/40 p-6">
                            <div class="space-y-3">
                                <button type="button" @click="submitForm()" :disabled="loading"
                                        class="w-full py-4 bg-indigo-600 text-white rounded-2xl text-xs font-black uppercase tracking-[0.2em] hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 flex items-center justify-center gap-3 active:scale-[0.98] disabled:opacity-50">
                                    <i class="ri-save-line text-lg" x-show="!loading"></i>
                                    <i class="ri-loader-4-line text-lg animate-spin" x-show="loading"></i>
                                    <span x-text="loading ? 'Saving...' : 'Save Changes'"></span>
                                </button>
                                <a href="{{ $indexUrl }}"
                                   class="block w-full py-4 bg-white text-slate-500 rounded-2xl text-xs font-black uppercase tracking-[0.2em] hover:bg-slate-50 border border-slate-200 transition-all text-center active:scale-[0.98]">
                                    Cancel
                                </a>
                                @if(isset($party->id))
                                    <button type="button" @click="showDeleteModal = true"
                                            class="w-full py-4 bg-red-50 text-red-600 rounded-2xl text-xs font-black uppercase tracking-[0.2em] hover:bg-red-100 transition-all text-center active:scale-[0.98]">
                                        Delete Party
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Delete Confirmation Modal -->
        <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-[100] overflow-y-auto" @keydown.escape.window="showDeleteModal = false">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="showDeleteModal = false"></div>
                <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" class="relative bg-white rounded-[2.5rem] p-8 shadow-2xl max-w-md w-full border border-slate-100">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-20 h-20 bg-red-50 rounded-3xl flex items-center justify-center mb-6">
                            <i class="ri-error-warning-line text-4xl text-red-500"></i>
                        </div>
                        <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight mb-2">Delete Party?</h3>
                        <p class="text-slate-500 font-bold text-sm leading-relaxed mb-8">Are you sure? This will permanently remove <span class="text-slate-900 font-black">{{ $party->name }}</span> and all its data. This action cannot be undone.</p>
                        <div class="grid grid-cols-2 gap-4 w-full">
                            <button @click="showDeleteModal = false" class="py-4 bg-slate-100 text-slate-600 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-200 transition-all">Cancel</button>
                            <button @click="confirmDelete()" class="py-4 bg-red-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-red-700 shadow-xl shadow-red-100 transition-all">Yes, Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(isset($party->id))
            <form id="party-delete-form" action="{{ $destroyAction }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </div>

    <script>
        function partylistEditData() {
            return {
                formData: {
                    name: '{{ old('name', $party->name ?? '') }}',
                    acronym: '{{ old('acronym', $party->acronym ?? '') }}',
                    description: '{{ old('description', $party->description ?? '') }}',
                    platform: '{{ old('platform', $party->platform ?? '') }}',
                    color: '{{ old('color', $party->color ?? '#3b82f6') }}',
                    organization_id: '{{ old('organization_id', $party->organization_id ?? '') }}',
                    election_id: '{{ old('election_id', $party->election_id ?? '') }}',
                    status: '{{ old('status', $party->status ?? 'active') }}',
                    logo: null
                },
                errors: {},
                loading: false,
                logoPreview: '{{ isset($party->logo) && Storage::exists('public/partylists/' . $party->logo) ? asset('storage/partylists/' . $party->logo) : '' }}',
                logoDragOver: false,
                showDeleteModal: false,

                async submitForm() {
                    if (!this.validateForm()) return;
                    this.loading = true;
                    this.errors = {};

                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('_method', 'PUT');

                    Object.keys(this.formData).forEach(key => {
                        if (key === 'logo' && this.formData[key]) {
                            formData.append('logo', this.formData[key]);
                        } else if (key !== 'logo') {
                            formData.append(key, this.formData[key] || '');
                        }
                    });

                    try {
                        const response = await fetch('{{ $updateAction }}', {
                            method: 'POST',
                            body: formData,
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });

                        const data = await response.json();
                        if (response.ok) {
                            window.location.href = '{{ $indexUrl }}';
                        } else {
                            this.errors = data.errors || {};
                        }
                    } catch (error) {
                        console.error(error);
                    } finally {
                        this.loading = false;
                    }
                },

                validateForm() {
                    this.errors = {};
                    if (!this.formData.name.trim()) this.errors.name = ['Party name is required'];
                    if (!this.formData.organization_id) this.errors.organization_id = ['Organization is required'];
                    return Object.keys(this.errors).length === 0;
                },

                generateAcronym() {
                    if (this.formData.name) {
                        const words = this.formData.name.trim().split(/\s+/);
                        this.formData.acronym = words.map(word => word.charAt(0).toUpperCase()).join('');
                    }
                },

                handleLogoUpload(event) {
                    const file = event.target?.files?.[0] || event.dataTransfer?.files?.[0];
                    if (!file) return;
                    this.formData.logo = file;
                    const reader = new FileReader();
                    reader.onload = (e) => this.logoPreview = e.target.result;
                    reader.readAsDataURL(file);
                },

                confirmDelete() {
                    document.getElementById('party-delete-form').submit();
                }
            }
        }
    </script>
@endsection
