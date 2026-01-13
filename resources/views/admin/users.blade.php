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
                <h1 class="text-lg font-bold text-slate-800">Users</h1>
                <div class="w-8"></div>
            </header>

            <div class="px-4 sm:px-6 py-6">
                <div class="max-w-7xl mx-auto">
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center">
                        <div class="w-20 h-20 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="ri-user-settings-line text-4xl text-slate-400"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-900">User Management</h2>
                        <p class="text-slate-500 mt-2">This module is under development.</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection
