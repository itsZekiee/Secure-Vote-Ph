<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<x-default-header title="{{ config('app.name', 'SecureVote') }} - Admin" />

@stack('styles')

<body class="font-sans antialiased bg-gradient-to-br from-gray-50 to-white min-h-screen">
@yield('content')

@stack('scripts')
</body>
</html>
