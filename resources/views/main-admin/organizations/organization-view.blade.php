@extends('layouts.app-main-admin')

@php
    $id = $organization->id ?? 0;

    // Index/back URL fallback
    $indexUrl = Route::has('admin.organizations.index') ? route('admin.organizations.index') :
                (Route::has('organizations.index') ? route('organizations.index') : url('/admin/organizations'));

    // Edit, members, destroy fallbacks
    $editUrl = Route::has('admin.organizations.edit') ? route('admin.organizations.edit', $id) :
               (Route::has('organizations.edit') ? route('organizations.edit', $id) : url('/admin/organizations/'.$id.'/edit'));

    $membersUrl = Route::has('admin.organizations.users.index') ? route('admin.organizations.users.index', $id) :
                  (Route::has('organizations.users.index') ? route('organizations.users.index', $id) : url('/admin/organizations/'.$id.'/users'));

    $destroyAction = Route::has('admin.organizations.destroy') ? route('admin.organizations.destroy', $id) :
                     (Route::has('organizations.destroy') ? route('organizations.destroy', $id) : url('/admin/organizations/'.$id));

    $partylists = $organization->partylists ?? ($partylists ?? collect());
@endphp

@section('title', ($organization->name ?? 'Organization') . ' — Overview')

@section('content')
    <div class="container mx-auto p-6">

        <!-- Top Navigation Bar -->
        <div class="bg-white/80 backdrop-blur-xl border-b border-gray-200/50 sticky top-0 z-40">
            <div class="px-8 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <a href="{{ $indexUrl }}"
                           class="group flex items-center justify-center w-10 h-10 rounded-xl bg-gray-100 hover:bg-blue-50 transition-all duration-200 border border-gray-200 hover:border-blue-200"
                           aria-label="Back to organizations index">
                            <i class="ri-arrow-left-line text-gray-600 group-hover:text-blue-600 transition-colors" aria-hidden="true"></i>
                        </a>

                        <div class="h-8 w-px bg-gray-200" aria-hidden="true"></div>

                        <nav class="flex items-center space-x-2 text-sm" aria-label="Breadcrumb">
                            <span class="text-gray-500">Admin</span>
                            <i class="ri-arrow-right-s-line text-gray-400" aria-hidden="true"></i>
                            <span class="text-gray-500">Organizations</span>
                            <i class="ri-arrow-right-s-line text-gray-400" aria-hidden="true"></i>
                            <span class="text-gray-900 font-semibold">Overview</span>
                        </nav>
                    </div>

                    <div class="flex items-center space-x-3">
                        <a href="{{ $editUrl }}"
                           class="inline-flex items-center px-4 py-2 text-sm font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-all duration-200"
                           aria-label="Edit organization">
                            <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2"/>
                            </svg>
                            Edit
                        </a>

                        <form action="{{ $destroyAction }}" method="POST" onsubmit="return confirm('Delete this organization? This action cannot be undone.');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-red-600 rounded-lg hover:bg-red-700 transition-all duration-200"
                                    aria-label="Delete organization">
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

        <!-- Page header (Back + Title) -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6 mt-6">
            <div class="flex items-center gap-4">
                <a href="{{ url()->previous() ?: $indexUrl }}"
                   class="inline-flex items-center gap-3 px-3 py-2 bg-white border rounded-md text-sm font-medium text-gray-700 hover:shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                   aria-label="Back">
                    <svg class="w-4 h-4 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path d="M15 18l-6-6 6-6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                    </svg>
                    Back
                </a>

                <div>
                    <div class="text-sm text-gray-500">Organizations</div>
                    <h1 class="text-2xl font-semibold text-gray-900 truncate max-w-2xl">{{ $organization->name ?? 'Organization' }}</h1>
                    @if(!empty($organization->subtitle))
                        <div class="text-sm text-gray-500 mt-1">{{ $organization->subtitle }}</div>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ $membersUrl }}" class="inline-flex items-center gap-2 px-3 py-2 bg-gray-50 border rounded-md text-sm text-gray-700 hover:bg-gray-100">
                    Manage Members
                </a>
            </div>
        </div>

        <!-- Main layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Primary content -->
            <section class="lg:col-span-2 bg-white border border-gray-100 rounded-2xl shadow-sm p-6">
                <div class="flex items-start gap-6">
                    <div class="w-28 h-28 bg-gray-50 rounded-lg border flex items-center justify-center overflow-hidden">
                        @if(!empty($organization->logo_url))
                            <img src="{{ $organization->logo_url }}" alt="{{ $organization->name }}" class="object-cover w-full h-full"/>
                        @else
                            <svg class="w-12 h-12 text-gray-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                <circle cx="12" cy="12" r="10" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
                            </svg>
                        @endif
                    </div>

                    <div class="flex-1">
                        <div class="flex items-start justify-between">
                            <div class="min-w-0">
                                <h2 class="text-xl font-semibold text-gray-900 truncate">{{ $organization->name }}</h2>
                                <p class="text-sm text-gray-600 mt-2">{{ $organization->description ?? 'No description provided.' }}</p>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-gray-700">
                            <div class="rounded-lg border bg-gray-50 p-3">
                                <div class="text-xs text-gray-500">Slug</div>
                                <div class="font-medium text-gray-800 truncate">{{ $organization->slug ?? '-' }}</div>
                            </div>

                            <div class="rounded-lg border bg-gray-50 p-3">
                                <div class="text-xs text-gray-500">Status</div>
                                <div class="font-medium text-gray-800 capitalize">{{ $organization->status ?? 'inactive' }}</div>
                            </div>

                            <div class="rounded-lg border bg-gray-50 p-3">
                                <div class="text-xs text-gray-500">Contact</div>
                                <div class="font-medium text-gray-800 truncate">{{ $organization->email ?? '-' }} • {{ $organization->phone ?? '-' }}</div>
                            </div>

                            <div class="rounded-lg border bg-gray-50 p-3">
                                <div class="text-xs text-gray-500">Created</div>
                                <div class="font-medium text-gray-800">{{ optional($organization->created_at)->toDayDateTimeString() ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <section class="mt-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-3">Settings</h3>

                    @if(!empty($organization->settings))
                        <ul class="space-y-3">
                            @foreach($organization->settings as $key => $value)
                                @php
                                    $settingEditUrl = Route::has('admin.organizations.settings.edit') ? route('admin.organizations.settings.edit', [$id, $key]) :
                                                     (Route::has('organizations.settings.edit') ? route('organizations.settings.edit', [$id, $key]) : url('/admin/organizations/'.$id.'/settings/'.$key.'/edit'));

                                    $settingDestroyAction = Route::has('admin.organizations.settings.destroy') ? route('admin.organizations.settings.destroy', [$id, $key]) :
                                                            (Route::has('organizations.settings.destroy') ? route('organizations.settings.destroy', [$id, $key]) : url('/admin/organizations/'.$id.'/settings/'.$key));
                                @endphp

                                <li class="flex items-center justify-between p-3 border rounded-md bg-white">
                                    <div class="min-w-0">
                                        <div class="text-sm font-medium text-gray-800">{{ $key }}</div>
                                        <div class="text-xs text-gray-500 mt-1 truncate">{{ is_array($value) ? json_encode($value) : (string)$value }}</div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <a href="{{ $settingEditUrl }}" class="inline-flex items-center p-2 rounded-md text-indigo-600 hover:bg-indigo-50" title="Edit {{ $key }}">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                                <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2"/>
                                            </svg>
                                        </a>

                                        <form action="{{ $settingDestroyAction }}" method="POST" onsubmit="return confirm('Remove setting {{ $key }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center p-2 rounded-md text-red-600 hover:bg-red-50" title="Remove {{ $key }}">
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                                    <path d="M3 6h18" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
                                                    <path d="M9 6v12M15 6v12" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
                                                    <path d="M10 6h4" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-gray-500">No custom settings configured.</p>
                    @endif
                </section>
            </section>

            <!-- Sidebar -->
            <aside class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6 space-y-4">
                <h3 class="text-lg font-medium text-gray-800">Quick Stats</h3>

                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs text-gray-500">Voters</div>
                            <div class="text-xl font-semibold text-gray-900">{{ $organization->voters_count ?? 0 }}</div>
                        </div>
                        <div class="text-indigo-600">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                <path d="M12 20v-6" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
                            </svg>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs text-gray-500">Elections</div>
                            <div class="text-xl font-semibold text-gray-900">{{ $organization->elections_count ?? 0 }}</div>
                        </div>
                        <div class="text-indigo-600">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                <path d="M3 6h18" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
                            </svg>
                        </div>
                    </div>

                    <div>
                        <a href="{{ $membersUrl }}" class="block w-full text-center px-3 py-2 bg-gray-50 border rounded-md text-sm text-gray-700 hover:bg-gray-100">
                            Manage Members
                        </a>
                    </div>
                </div>
            </aside>
        </div>

        <!-- Partylist table -->
        <section class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-medium text-gray-800">Partylists</h2>

                @php
                    $partyCreateUrl = Route::has('admin.partylists.create') ? route('admin.partylists.create', ['organization' => $id]) :
                                     (Route::has('partylists.create') ? route('partylists.create', ['organization' => $id]) : url('/admin/partylists/create?organization='.$id));
                @endphp

                <a href="{{ $partyCreateUrl }}" class="inline-flex items-center gap-2 px-3 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path d="M12 5v14M5 12h14" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                    </svg>
                    New Partylist
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acronym</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seats</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($partylists as $party)
                        @php
                            $partyId = $party->id ?? 0;
                            $partyShow = Route::has('admin.partylists.show') ? route('admin.partylists.show', $partyId) :
                                         (Route::has('partylists.show') ? route('partylists.show', $partyId) : url('/admin/partylists/'.$partyId));

                            $partyEdit = Route::has('admin.partylists.edit') ? route('admin.partylists.edit', $partyId) :
                                         (Route::has('partylists.edit') ? route('partylists.edit', $partyId) : url('/admin/partylists/'.$partyId.'/edit'));

                            $partyDestroy = Route::has('admin.partylists.destroy') ? route('admin.partylists.destroy', $partyId) :
                                            (Route::has('partylists.destroy') ? route('partylists.destroy', $partyId) : url('/admin/partylists/'.$partyId));
                        @endphp

                        <tr>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 truncate max-w-xs">{{ $party->name }}</div>
                                <div class="text-xs text-gray-500 mt-1 line-clamp-2 max-w-sm">{{ $party->description ?? '' }}</div>
                            </td>

                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700 truncate max-w-xs">{{ $party->acronym ?? '-' }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">{{ $party->seats ?? 0 }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ ($party->status ?? '') === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                                    {{ ucfirst($party->status ?? 'inactive') }}
                                </span>
                            </td>

                            <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="inline-flex items-center gap-3">
                                    <a href="{{ $partyShow }}" class="text-indigo-600 hover:underline">View</a>
                                    <a href="{{ $partyEdit }}" class="text-indigo-600 hover:underline">Edit</a>

                                    <form action="{{ $partyDestroy }}" method="POST" onsubmit="return confirm('Delete this partylist?');" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">
                                No partylists connected to this organization.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
