@extends('layouts.app-main-admin')

@php
    // Defensive guard: support both `$party` and `$partylist`
    $party = $party ?? ($partylist ?? null);
    if (is_null($party)) {
        abort(404, 'Partylist not found.');
    }

    $id = $party->id ?? 0;

    $indexUrl = Route::has('admin.partylists.index') ? route('admin.partylists.index') :
                (Route::has('partylists.index') ? route('partylists.index') : url('/admin/partylists'));

    if (Route::has('admin.partylists.edit')) {
        $editUrl = route('admin.partylists.edit', $id);
    } elseif (Route::has('partylists.edit')) {
        $editUrl = route('partylists.edit', $id);
    } else {
        $editUrl = url('/admin/partylists/'.$id.'/edit');
    }

    if (Route::has('admin.partylists.destroy')) {
        $destroyAction = route('admin.partylists.destroy', $id);
    } elseif (Route::has('partylists.destroy')) {
        $destroyAction = route('partylists.destroy', $id);
    } else {
        $destroyAction = url('/admin/partylists/'.$id);
    }
@endphp

@section('title', ($party->name ?? 'Partylist') . ' — Overview')

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
                            <span class="text-gray-900 font-semibold">Overview</span>
                        </nav>
                    </div>

                    <div class="flex items-center space-x-3">
                        <a href="{{ $editUrl }}"
                           class="inline-flex items-center px-4 py-2 text-sm font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-all duration-200"
                           aria-label="Edit partylist">
                            <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2"/>
                            </svg>
                            Edit
                        </a>

                        <form action="{{ $destroyAction }}" method="POST" onsubmit="return confirm('Delete this partylist?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-red-600 rounded-lg hover:bg-red-700 transition-all duration-200"
                                    aria-label="Delete partylist">
                                <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                    <path d="M3 6h18" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
                                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
                                </svg>
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>



        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <section class="lg:col-span-2 bg-white border rounded-lg shadow-sm p-6">
                <div class="flex gap-6">
                    <div class="w-28 h-28 bg-gray-50 rounded-lg border flex items-center justify-center overflow-hidden">
                        @if(!empty($party->logo_url))
                            <img src="{{ $party->logo_url }}" alt="{{ $party->name }}" class="object-cover w-full h-full"/>
                        @else
                            <svg class="w-12 h-12 text-gray-300" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"/></svg>
                        @endif
                    </div>

                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="mt-4 text-2xl font-semibold text-gray-900 truncate max-w-2xl">{{ $party->name ?? 'Partylist' }}</h1>
                                <div class="text-xs text-gray-500">Acronym</div>
                                <div class="text-lg font-semibold text-gray-900 truncate max-w-xs">{{ $party->acronym ?? '-' }}</div>
                            </div>

                            <div>
                                <div class="text-xs text-gray-500">Seats</div>
                                <div class="text-lg font-semibold text-gray-900 text-right">{{ $party->seats ?? 0 }}</div>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-gray-700">
                            <div class="rounded-lg border bg-gray-50 p-3">
                                <div class="text-xs text-gray-500">Status</div>
                                <div class="font-medium text-gray-800 capitalize">{{ $party->status ?? 'inactive' }}</div>
                            </div>

                            <div class="rounded-lg border bg-gray-50 p-3">
                                <div class="text-xs text-gray-500">Created</div>
                                <div class="font-medium text-gray-800">{{ optional($party->created_at)->toDayDateTimeString() ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <section class="mt-6">
                    <h2 class="text-lg font-medium text-gray-800 mb-3">About</h2>
                    <p class="text-sm text-gray-600">{{ $party->description ?? 'No description provided.' }}</p>
                </section>
            </section>

            <aside class="bg-white border rounded-lg shadow-sm p-6 space-y-4">
                <h3 class="text-lg font-medium text-gray-800">Quick Actions</h3>

                <div class="space-y-2">
                    <a href="{{ $editUrl }}" class="block w-full text-center px-3 py-2 bg-indigo-600 text-white rounded-md">Edit Partylist</a>
                    <a href="{{ $indexUrl }}" class="block w-full text-center px-3 py-2 border rounded-md text-sm text-gray-700">All Partylists</a>
                </div>
            </aside>
        </div>

        <section class="bg-white border rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-medium text-gray-800">Candidates</h2>
                <a href="{{ route('admin.candidates.create', ['partylist' => $id]) ?? url('/admin/candidates/create?partylist='.$id) }}" class="inline-flex items-center gap-2 px-3 py-2 bg-indigo-600 text-white rounded-md text-sm">New Candidate</a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($party->candidates ?? [] as $candidate)
                        <tr>
                            <td class="px-4 py-4 text-sm text-gray-900">{{ $candidate->name ?? '—' }}</td>
                            <td class="px-4 py-4 text-sm text-gray-700">{{ $candidate->position ?? '—' }}</td>
                            <td class="px-4 py-4 text-sm text-gray-700">{{ ucfirst($candidate->status ?? 'inactive') }}</td>
                            <td class="px-4 py-4 text-right text-sm">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.candidates.show', $candidate->id) ?? url('/admin/candidates/'.$candidate->id) }}" class="text-indigo-600 hover:underline">View</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500">
                                No candidates added to this partylist.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <style>
        .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    </style>
@endsection
