@extends('layouts.app-main-admin')

@section('content')

    <div x-data="{
        collapsed: false,
        isMobile: window.innerWidth < 1024 }"
         x-init="window.addEventListener('resize', () => { isMobile = window.innerWidth < 1024 })"
         class="flex min-h-screen bg-slate-50">

        <x-admin-sidebar />

        <!-- Main Content Area -->
        <main :class="collapsed && !isMobile ? 'ml-20' : 'ml-0 lg:ml-72'"
              class="flex-1 transition-all duration-300 min-h-screen">

            <!-- Mobile Header -->
            <header x-show="isMobile"
                    class="lg:hidden bg-white shadow-sm border-b px-4 py-3 flex items-center justify-between">
                <button @click="collapsed = false"
                        class="p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                    <i class="ri-menu-line text-lg"></i>
                </button>
                <h1 class="text-lg font-semibold text-slate-800">Candidates</h1>
            </header>

            <!-- Page Content -->
            <div class="p-6">
                <div class="max-w-7xl mx-auto">
                    <!-- Page Header -->
                    <div class="mb-6">
                        <h1 class="text-2xl font-bold text-slate-800">Settings</h1>
                        <p class="text-slate-600 mt-1">Manage election candidates and their information</p>
                    </div>

                    <!-- Your candidates content here -->
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        <p class="text-slate-500">Candidates content will go here...</p>
                    </div>
                </div>
            </div>
        </main>

    </div>

@endsection
