@extends('layouts.app-main-admin')

@section('content')

    <div x-data="{
        collapsed: window.innerWidth < 1024,
        isMobile: window.innerWidth < 1024 }"
         x-init="window.addEventListener('resize', () => { isMobile = window.innerWidth < 1024 })"
         class="flex min-h-screen bg-slate-50">

        <x-admin-sidebar />

    </div>

@endsection
