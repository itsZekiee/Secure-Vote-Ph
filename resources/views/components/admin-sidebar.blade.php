<aside x-show="!collapsed || !isMobile"
       x-cloak
       x-transition:enter="transition ease-out duration-300"
       x-transition:enter-start="-translate-x-full"
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition ease-in duration-300"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="-translate-x-full"
       :class="collapsed && !isMobile ? 'w-20' : 'w-72'"
       class="fixed lg:static inset-y-0 left-0 z-40 bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 border-r border-slate-700 shadow-xl lg:shadow-sm transition-all duration-300 flex flex-col">

    <!-- Mobile Overlay -->
    <div x-show="!collapsed && isMobile"
         x-cloak
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="collapsed = true"
         class="fixed inset-0 bg-black bg-opacity-50 lg:hidden z-30"></div>

    <div class="flex flex-col h-full min-h-0">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-5 border-b border-slate-700 bg-slate-800/50 flex-shrink-0">
            <div x-show="!collapsed || isMobile"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm">
                    <i class="ri-shield-keyhole-fill text-slate-600 text-lg"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-white tracking-wide">SecureVote</h1>
                    <p class="text-xs text-slate-300 font-medium">Administration</p>
                </div>
            </div>

            <div x-show="collapsed && !isMobile"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="w-10 h-10 bg-white rounded-lg flex items-center justify-center mx-auto shadow-sm">
                <i class="ri-shield-keyhole-fill text-slate-600 text-lg"></i>
            </div>

            <button x-show="!isMobile"
                    @click="collapsed = !collapsed"
                    class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700/50 transition-all duration-200"
                    title="Toggle Sidebar">
                <i class="ri-menu-fold-line text-lg" x-show="!collapsed"></i>
                <i class="ri-menu-unfold-line text-lg" x-show="collapsed"></i>
            </button>

            <button x-show="isMobile && !collapsed"
                    @click="collapsed = true"
                    class="p-2 rounded-lg text-white hover:bg-slate-700/50 lg:hidden transition-colors">
                <i class="ri-close-line text-lg"></i>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}"
               :class="(collapsed && !isMobile) ? 'justify-center px-0' : 'gap-3 px-4'"
               class="flex items-center py-2.5 rounded-lg text-slate-300 hover:bg-slate-700/50 hover:text-white transition-all duration-200 group {{ request()->routeIs('admin.dashboard') ? 'bg-slate-700 text-white border-l-4 border-blue-500 shadow-lg' : '' }}">
                <i class="ri-dashboard-3-line text-lg {{ request()->routeIs('admin.dashboard') ? 'text-blue-400' : 'text-slate-400' }}"></i>
                <span x-show="!collapsed || isMobile"
                      x-transition:enter="transition ease-out duration-200 delay-75"
                      x-transition:enter-start="opacity-0 translate-x-2"
                      x-transition:enter-end="opacity-100 translate-x-0"
                      class="font-medium text-sm">Dashboard</span>
            </a>

            <!-- Elections -->
            <a href="{{ route('admin.elections.index') }}"
               :class="(collapsed && !isMobile) ? 'justify-center px-0' : 'gap-3 px-4'"
               class="flex items-center py-2.5 rounded-lg text-slate-300 hover:bg-slate-700/50 hover:text-white transition-all duration-200 group {{ request()->routeIs('admin.elections.*') ? 'bg-slate-700 text-white border-l-4 border-emerald-500 shadow-lg' : '' }}">
                <i class="ri-government-line text-lg {{ request()->routeIs('admin.elections.*') ? 'text-emerald-400' : 'text-slate-400' }}"></i>
                <span x-show="!collapsed || isMobile"
                      x-transition:enter="transition ease-out duration-200 delay-75"
                      x-transition:enter-start="opacity-0 translate-x-2"
                      x-transition:enter-end="opacity-100 translate-x-0"
                      class="font-medium text-sm">Elections</span>
            </a>

            <!-- Organizations -->
            <a href="{{ route('admin.organizations.index') }}"
               :class="(collapsed && !isMobile) ? 'justify-center px-0' : 'gap-3 px-4'"
               class="flex items-center py-2.5 rounded-lg text-slate-300 hover:bg-slate-700/50 hover:text-white transition-all duration-200 group {{ request()->routeIs('admin.organizations.*') ? 'bg-slate-700 text-white border-l-4 border-cyan-500 shadow-lg' : '' }}">
                <i class="ri-building-line text-lg {{ request()->routeIs('admin.organizations.*') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                <span x-show="!collapsed || isMobile"
                      x-transition:enter="transition ease-out duration-200 delay-75"
                      x-transition:enter-start="opacity-0 translate-x-2"
                      x-transition:enter-end="opacity-100 translate-x-0"
                      class="font-medium text-sm">Organizations</span>
            </a>

            <!-- Partylists -->
            <a href="{{ route('admin.partylists.index') }}"
               :class="(collapsed && !isMobile) ? 'justify-center px-0' : 'gap-3 px-4'"
               class="flex items-center py-2.5 rounded-lg text-slate-300 hover:bg-slate-700/50 hover:text-white transition-all duration-200 group {{ request()->routeIs('admin.partylists.*') ? 'bg-slate-700 text-white border-l-4 border-indigo-500 shadow-lg' : '' }}">
                <i class="ri-flag-line text-lg {{ request()->routeIs('admin.partylists.*') ? 'text-indigo-400' : 'text-slate-400' }}"></i>
                <span x-show="!collapsed || isMobile"
                      x-transition:enter="transition ease-out duration-200 delay-75"
                      x-transition:enter-start="opacity-0 translate-x-2"
                      x-transition:enter-end="opacity-100 translate-x-0"
                      class="font-medium text-sm">Partylists</span>
            </a>

            <!-- Voters -->
            <a href="{{ route('admin.voters.index') }}"
               :class="(collapsed && !isMobile) ? 'justify-center px-0' : 'gap-3 px-4'"
               class="flex items-center py-2.5 rounded-lg text-slate-300 hover:bg-slate-700/50 hover:text-white transition-all duration-200 group {{ request()->routeIs('admin.voters.*') ? 'bg-slate-700 text-white border-l-4 border-purple-500 shadow-lg' : '' }}">
                <i class="ri-team-line text-lg {{ request()->routeIs('admin.voters.*') ? 'text-purple-400' : 'text-slate-400' }}"></i>
                <span x-show="!collapsed || isMobile"
                      x-transition:enter="transition ease-out duration-200 delay-75"
                      x-transition:enter-start="opacity-0 translate-x-2"
                      x-transition:enter-end="opacity-100 translate-x-0"
                      class="font-medium text-sm">Voters</span>
            </a>

            <!-- Candidates -->
            <a href="{{ route('admin.candidates.index') }}"
               :class="(collapsed && !isMobile) ? 'justify-center px-0' : 'gap-3 px-4'"
               class="flex items-center py-2.5 rounded-lg text-slate-300 hover:bg-slate-700/50 hover:text-white transition-all duration-200 group {{ request()->routeIs('admin.candidates.*') ? 'bg-slate-700 text-white border-l-4 border-orange-500 shadow-lg' : '' }}">
                <i class="ri-user-star-line text-lg {{ request()->routeIs('admin.candidates.*') ? 'text-orange-400' : 'text-slate-400' }}"></i>
                <span x-show="!collapsed || isMobile"
                      x-transition:enter="transition ease-out duration-200 delay-75"
                      x-transition:enter-start="opacity-0 translate-x-2"
                      x-transition:enter-end="opacity-100 translate-x-0"
                      class="font-medium text-sm">Candidates</span>
            </a>

            <!-- Reports & Tally -->
            <a href="{{ route('admin.reports.index') }}"
               :class="(collapsed && !isMobile) ? 'justify-center px-0' : 'gap-3 px-4'"
               class="flex items-center py-2.5 rounded-lg text-slate-300 hover:bg-slate-700/50 hover:text-white transition-all duration-200 group {{ request()->routeIs('admin.reports.*') ? 'bg-slate-700 text-white border-l-4 border-pink-500 shadow-lg' : '' }}">
                <i class="ri-bar-chart-box-line text-lg {{ request()->routeIs('admin.reports.*') ? 'text-pink-400' : 'text-slate-400' }}"></i>
                <span x-show="!collapsed || isMobile"
                      x-transition:enter="transition ease-out duration-200 delay-75"
                      x-transition:enter-start="opacity-0 translate-x-2"
                      x-transition:enter-end="opacity-100 translate-x-0"
                      class="font-medium text-sm">Reports & Tally</span>
            </a>

            <!-- Settings -->
            <a href="{{ route('admin.settings') }}"
               :class="(collapsed && !isMobile) ? 'justify-center px-0' : 'gap-3 px-4'"
               class="flex items-center py-2.5 rounded-lg text-slate-300 hover:bg-slate-700/50 hover:text-white transition-all duration-200 group {{ request()->routeIs('admin.settings') ? 'bg-slate-700 text-white border-l-4 border-slate-500 shadow-lg' : '' }}">
                <i class="ri-settings-3-line text-lg {{ request()->routeIs('admin.settings') ? 'text-slate-300' : 'text-slate-400' }}"></i>
                <span x-show="!collapsed || isMobile"
                      x-transition:enter="transition ease-out duration-200 delay-75"
                      x-transition:enter-start="opacity-0 translate-x-2"
                      x-transition:enter-end="opacity-100 translate-x-0"
                      class="font-medium text-sm">Settings</span>
            </a>
        </nav>

        <!-- User Profile Footer -->
        <div class="p-4 border-t border-slate-700 bg-slate-800/50 flex-shrink-0">
            <div :class="(collapsed && !isMobile) ? 'justify-center px-0' : 'gap-3'"
                 class="flex items-center p-3 rounded-lg hover:bg-slate-700/50 transition-colors cursor-pointer group">
                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold shadow-lg flex-shrink-0">
                    {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                </div>
                <div x-show="!collapsed || isMobile"
                     x-transition:enter="transition ease-out duration-200 delay-75"
                     x-transition:enter-start="opacity-0 translate-x-2"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     class="flex-1 min-w-0 overflow-hidden">
                    <div class="font-semibold text-white text-sm truncate">{{ auth()->user()->name ?? 'Unknown' }}</div>
                    <div class="text-xs text-slate-400 truncate">{{ auth()->user()->email ?? 'No email' }}</div>
                </div>
                <button x-show="!collapsed || isMobile"
                        x-transition:enter="transition ease-out duration-200 delay-100"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        class="p-1.5 rounded-md text-slate-400 hover:text-red-400 hover:bg-red-900/20 transition-all duration-200 flex-shrink-0"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        title="Logout">
                    <i class="ri-logout-box-line text-base"></i>
                </button>
            </div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>
    </div>
</aside>
