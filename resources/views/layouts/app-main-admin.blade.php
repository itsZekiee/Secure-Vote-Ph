<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<x-default-header title="{{ $title ?? config('app.name', 'SecureVote') }} - Admin" />

@stack('styles')
<link rel="stylesheet" href="{{ asset('css/admin/responsive.css') }}">

<body class="font-sans antialiased bg-slate-50 min-h-screen"
      x-data="{
          collapsed: window.innerWidth < 1024,
          isMobile: window.innerWidth < 1024,
          init() {
              window.addEventListener('resize', () => {
                  this.isMobile = window.innerWidth < 1024;
                  if (this.isMobile) this.collapsed = true;
              });
          }
      }">

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <x-admin-sidebar />

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-h-screen transition-all duration-300 overflow-hidden"
             :class="isMobile ? 'ml-0' : (collapsed ? 'ml-16' : 'ml-72')">

            @yield('content')

        </div>
    </div>

    @stack('scripts')
</body>
</html>
