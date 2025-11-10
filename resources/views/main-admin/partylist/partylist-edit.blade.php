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
    <div class="container mx-auto p-6">

        <!-- Top Navigation Bar -->
        <div class=" backdrop-blur-xl border-b border-gray-200/50 sticky top-0 z-40">
            <div class="px-8 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <a href="{{ $indexUrl }}"
                           class="group flex items-center justify-center w-10 h-10 rounded-xl bg-gray-100 hover:bg-blue-50 transition-all duration-200 border border-gray-200 hover:border-blue-200"
                           aria-label="Back to partylists index">
                            <i class="ri-arrow-left-line text-gray-600 group-hover:text-blue-600 transition-colors" aria-hidden="true"></i>
                        </a>

                        <div class="h-8 w-px bg-gray-200" aria-hidden="true"></div>

                        <nav class="flex items-center space-x-2 text-sm" aria-label="Breadcrumb">
                            <span class="text-gray-500">Admin</span>
                            <i class="ri-arrow-right-s-line text-gray-400" aria-hidden="true"></i>
                            <span class="text-gray-500">Partylists</span>
                            <i class="ri-arrow-right-s-line text-gray-400" aria-hidden="true"></i>
                            <span class="text-gray-900 font-semibold">Edit</span>
                        </nav>
                    </div>

                    <div aria-hidden="true"></div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Edit Partylist</h1>
                <p class="text-sm text-gray-500">Update partylist details. Changes are audited.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ $indexUrl }}" class="inline-flex items-center px-3 py-2 border rounded text-sm text-gray-700 hover:shadow">
                    Back
                </a>
            </div>
        </div>

        <form action="{{ $updateAction }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            @csrf
            @method('PUT')

            <section class="lg:col-span-2 bg-white border rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-medium text-gray-800 mb-4">Partylist Details</h2>

                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input id="name" name="name" value="{{ old('name', $party->name ?? '') }}" required
                               class="mt-1 block w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-indigo-300" />
                        @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="acronym" class="block text-sm font-medium text-gray-700">Acronym</label>
                            <input id="acronym" name="acronym" value="{{ old('acronym', $party->acronym ?? '') }}"
                                   class="mt-1 block w-full border rounded px-3 py-2" />
                            @error('acronym') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="seats" class="block text-sm font-medium text-gray-700">Seats</label>
                            <input id="seats" name="seats" type="number" min="0" value="{{ old('seats', $party->seats ?? 0) }}"
                                   class="mt-1 block w-full border rounded px-3 py-2" />
                            @error('seats') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select id="status" name="status" class="mt-1 block w-full border rounded px-3 py-2">
                                <option value="active" {{ old('status', $party->status ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $party->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea id="description" name="description" rows="5" class="mt-1 block w-full border rounded px-3 py-2">{{ old('description', $party->description ?? '') }}</textarea>
                        @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            <aside class="bg-white border rounded-lg shadow-sm p-6 flex flex-col gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Logo</label>
                    <div class="flex items-center gap-4">
                        <div class="w-20 h-20 bg-gray-100 rounded-md overflow-hidden border flex items-center justify-center">
                            @if(!empty($party->logo_url))
                                <img src="{{ $party->logo_url }}" alt="{{ $party->name }}" class="object-cover w-full h-full" />
                            @else
                                <svg class="w-8 h-8 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5-2 4-2 4 2 4 2"/></svg>
                            @endif
                        </div>

                        <div class="flex-1">
                            <input type="file" name="logo" accept="image/*" class="block w-full text-sm text-gray-600" />
                            <p class="text-xs text-gray-500 mt-1">PNG/JPG/SVG, max 2MB.</p>
                        </div>

                        @if(!empty($party->logo_url))
                            <button type="submit" name="remove_logo" value="1" class="inline-flex items-center px-2 py-2 bg-red-50 text-red-600 rounded" aria-label="Remove logo">
                                Remove
                            </button>
                        @endif
                    </div>
                </div>

                <div class="mt-auto flex gap-2 w-full">
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded shadow-sm">
                        Save
                    </button>

                    <a href="{{ $indexUrl }}" class="inline-flex items-center px-4 py-2 border rounded text-sm text-gray-700">Cancel</a>

                    @if(isset($party->id))
                        <button type="button" onclick="if(confirm('Delete this partylist? This action cannot be undone.')) document.getElementById('party-delete-form').submit();" class="ml-auto inline-flex items-center gap-2 px-3 py-2 bg-red-600 text-white rounded">
                            Delete
                        </button>
                    @endif
                </div>
            </aside>
        </form>

        @if(isset($party->id))
            <form id="party-delete-form" action="{{ $destroyAction }}" method="POST" style="display:none;">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </div>

    <style>
        .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
    </style>
@endsection
