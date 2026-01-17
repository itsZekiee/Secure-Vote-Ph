<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<x-default-header title="{{ $title ?? config('app.name', 'SecureVote') }} - Admin" />

@stack('styles')
<link rel="stylesheet" href="{{ asset('css/admin/responsive.css') }}">
<style>
    /* Mobile-friendly Toggle Switches */
    @media (max-width: 640px) {
        .peer-checked\:after\:translate-x-full:after {
            transform: translateX(1.25rem) !important;
        }
        label.relative.inline-flex.items-center.cursor-pointer {
            min-width: 3rem;
            min-height: 1.5rem;
            padding: 0.5rem 0; /* Increase touch target */
        }
    }
</style>

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
