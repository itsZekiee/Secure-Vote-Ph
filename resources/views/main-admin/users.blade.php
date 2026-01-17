@extends('layouts.app-main-admin')

@section('content')
    <div class="flex min-h-screen bg-slate-50">
        <x-admin-sidebar />

        <main class="flex-1 min-h-screen">
            <!-- Mobile Header -->
            <header class="lg:hidden bg-white shadow-sm border-b px-4 py-4 flex items-center justify-between sticky top-0 z-40">
                <button @click="collapsed = false" class="p-2 -ml-2 rounded-lg text-slate-600 hover:bg-slate-100">
                    <i class="ri-menu-2-fill text-xl"></i>
                </button>
                <h1 class="text-lg font-bold text-slate-800">Admins & Managers</h1>
                <div class="w-8"></div>
            </header>

            <div class="px-4 sm:px-6 py-6">
                <div class="max-w-7xl mx-auto">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                        <div>
                            <h1 class="text-2xl font-bold text-slate-900">Admin Management</h1>
                            <p class="text-slate-500 text-sm mt-1">Review and approve new admin accounts.</p>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-600 text-sm flex items-center gap-3">
                            <i class="ri-checkbox-circle-fill"></i>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-xl text-blue-600 text-sm flex items-center gap-3">
                            <i class="ri-information-fill"></i>
                            {{ session('info') }}
                        </div>
                    @endif

                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-gray-200">
                                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">User</th>
                                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Role</th>
                                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Joined</th>
                                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @forelse($users as $user)
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-bold text-slate-900">{{ $user->name }}</div>
                                                        <div class="text-xs text-slate-500">{{ $user->email }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                                                    {{ ucfirst($user->role) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                @if($user->is_approved)
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full bg-green-50 text-green-700 border border-green-100">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                                        Approved
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full bg-amber-50 text-amber-700 border border-amber-100">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                        Pending Approval
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm text-slate-500">
                                                {{ $user->created_at->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    @if(!$user->is_approved)
                                                        <form action="{{ route('admin.users.approve', $user->id) }}" method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Approve">
                                                                <i class="ri-check-line text-lg"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form action="{{ route('admin.users.reject', $user->id) }}" method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit" class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Revoke Approval">
                                                                <i class="ri-close-line text-lg"></i>
                                                            </button>
                                                        </form>
                                                    @endif

                                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                                            <i class="ri-delete-bin-line text-lg"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                                <i class="ri-user-search-line text-4xl mb-2 block opacity-20"></i>
                                                No admin accounts found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection
