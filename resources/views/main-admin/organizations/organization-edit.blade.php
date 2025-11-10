@extends('layouts.app-main-admin')

@php
    $id = $organization->id ?? 0;

    // Index/back URL fallback
    $indexUrl = Route::has('admin.organizations.index') ? route('admin.organizations.index') :
                (Route::has('organizations.index') ? route('organizations.index') : url('/admin/organizations'));

    // Update action — safe fallback
    if (Route::has('admin.organizations.update')) {
        $updateAction = route('admin.organizations.update', $id);
    } elseif (Route::has('organizations.update')) {
        $updateAction = route('organizations.update', $id);
    } else {
        $updateAction = url('/admin/organizations/'.$id);
    }

    // Destroy action — safe fallback for delete buttons
    if (Route::has('admin.organizations.destroy')) {
        $destroyAction = route('admin.organizations.destroy', $id);
    } elseif (Route::has('organizations.destroy')) {
        $destroyAction = route('organizations.destroy', $id);
    } else {
        $destroyAction = url('/admin/organizations/'.$id);
    }
@endphp

@section('title', 'Edit Organization - ' . ($organization->name ?? 'Organization'))

@section('content')
    <div class="container mx-auto p-6">

        <!-- Top Navigation Bar -->
        <div class="bg-white/80 backdrop-blur-xl border-b border-gray-200/50 sticky top-0 z-40">
            <div class="px-8 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <a href="{{ $indexUrl }}"
                           class="group flex items-center justify-center w-10 h-10 rounded-xl bg-gray-100 hover:bg-blue-50 transition-all duration-200 border border-gray-200 hover:border-blue-200">
                            <i class="ri-arrow-left-line text-gray-600 group-hover:text-blue-600 transition-colors"></i>
                        </a>
                        <div class="h-8 w-px bg-gray-200"></div>
                        <nav class="flex items-center space-x-2 text-sm">
                            <span class="text-gray-500">Admin</span>
                            <i class="ri-arrow-right-s-line text-gray-400"></i>
                            <span class="text-gray-500">Organizations</span>
                            <i class="ri-arrow-right-s-line text-gray-400"></i>
                            <span class="text-gray-900 font-semibold">Edit Organization</span>
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

        <header class="flex items-start justify-between mb-6 mt-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 leading-tight">Edit Organization</h1>
                <p class="text-sm text-gray-600 mt-1">Update organization information and settings. Changes are versioned and audited.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ $indexUrl }}" class="inline-flex items-center gap-2 px-3 py-2 bg-white border rounded text-sm text-gray-700 hover:shadow">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M15 18l-6-6 6-6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                    Back
                </a>
            </div>
        </header>

        <!-- Update form -->
        <form action="{{ $updateAction }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            @csrf
            @method('PUT')

            <section class="lg:col-span-2 bg-white shadow rounded-md p-6">
                <h2 class="text-lg font-medium text-gray-800 mb-4">Organization Details</h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <input name="name" value="{{ old('name', $organization->name ?? '') }}" required
                               class="mt-1 block w-full border rounded px-3 py-2 focus:outline-none focus:ring" />
                        @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Slug</label>
                        <input name="slug" value="{{ old('slug', $organization->slug ?? '') }}" class="mt-1 block w-full border rounded px-3 py-2 bg-gray-50" />
                        @error('slug') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Contact Email</label>
                            <input name="email" value="{{ old('email', $organization->email ?? '') }}" type="email" class="mt-1 block w-full border rounded px-3 py-2" />
                            @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Phone</label>
                            <input name="phone" value="{{ old('phone', $organization->phone ?? '') }}" class="mt-1 block w-full border rounded px-3 py-2" />
                            @error('phone') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Address</label>
                        <textarea name="address" rows="3" class="mt-1 block w-full border rounded px-3 py-2">{{ old('address', $organization->address ?? '') }}</textarea>
                        @error('address') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" class="mt-1 block w-48 border rounded px-3 py-2">
                            <option value="active" {{ old('status', $organization->status ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $organization->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
            </section>

            <aside class="bg-white shadow rounded-md p-6 flex flex-col gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Logo</label>
                    <div class="flex items-center gap-3">
                        <div class="w-20 h-20 bg-gray-100 rounded overflow-hidden flex items-center justify-center border">
                            @if(!empty($organization->logo_url))
                                <img src="{{ $organization->logo_url }}" alt="{{ $organization->name }}" class="object-cover w-full h-full" />
                            @else
                                <svg class="w-10 h-10 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                    <circle cx="12" cy="12" r="10" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
                                    <path d="M8 14s1.5-2 4-2 4 2 4 2" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2"/>
                                </svg>
                            @endif
                        </div>

                        <div class="flex-1">
                            <input type="file" name="logo" accept="image/*" class="block w-full text-sm text-gray-600" />
                            <p class="text-xs text-gray-500 mt-1">SVG, PNG or JPG — max 2MB.</p>
                        </div>

                        @if(!empty($organization->logo_url))
                            <button type="submit" name="remove_logo" value="1" class="inline-flex items-center px-2 py-2 bg-red-50 text-red-600 rounded" aria-label="Remove logo">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                    <polyline points="3 6 5 6 21 6" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2"/>
                                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2"/>
                                    <path d="M10 11v6" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2"/>
                                    <path d="M14 11v6" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2"/>
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>

                <div class="mt-auto flex gap-2 w-full">
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded shadow">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                            <path d="M19 21H5a2 2 0 0 1-2-2V7l7-4h6l7 4v12a2 2 0 0 1-2 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2"/>
                            <path d="M7 10h10" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2"/>
                        </svg>
                        Save
                    </button>

                    <a href="{{ $indexUrl }}" class="inline-flex items-center px-4 py-2 border rounded text-sm text-gray-700">
                        Cancel
                    </a>

                    @if(isset($organization->id))
                        <button type="button"
                                onclick="if(confirm('Delete this organization? This action cannot be undone.')) document.getElementById('org-delete-form').submit();"
                                class="ml-auto inline-flex items-center gap-2 px-3 py-2 bg-red-600 text-white rounded">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                <path d="M3 6h18" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2"/>
                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2"/>
                                <path d="M10 11v6" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2"/>
                                <path d="M14 11v6" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2"/>
                            </svg>
                            Delete
                        </button>
                    @endif
                </div>
            </aside>
        </form>

        @if(isset($organization->id))
            <!-- External delete form (outside the update form) -->
            <form id="org-delete-form" action="{{ $destroyAction }}" method="POST" style="display:none;">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </div>
@endsection
