@extends('layouts.app-main-admin')

@section('content')

    <div class="flex-1 flex flex-col">

        <x-admin-header />

        <!-- Main Content Area -->
        <main class="flex-1 min-h-screen">

            <!-- Mobile Header -->
            <header class="lg:hidden bg-white shadow-sm border-b px-4 py-3 flex items-center justify-between">
                <button @click="collapsed = false"
                        class="p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                    <i class="ri-menu-line text-lg"></i>
                </button>
                <h1 class="text-lg font-semibold text-slate-800">Settings</h1>
            </header>

            <!-- Page Content -->
            <div class="p-6">
                <div class="max-w-7xl mx-auto">
                    <!-- Page Header -->
                    <div class="mb-6">
                        <h1 class="text-2xl font-bold text-slate-800">Settings</h1>
                        <p class="text-slate-600 mt-1">Manage system configurations and admin settings</p>
                    </div>

                    <!-- Your settings content here -->
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        <p class="text-slate-500">Settings content will go here...</p>
                    </div>
                </div>
            </div>
        </main>

    </div>

@endsection
