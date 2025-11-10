<header class="bg-white border-b border-slate-200 sticky top-0 z-30">
    <div class="flex items-center justify-between px-6 py-4">
        <div class="flex items-center gap-4">
            <button @click="collapsed = !collapsed"
                    class="lg:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100 transition-colors">
                <i class="ri-menu-line text-xl"></i>
            </button>
            <div>
                <h2 class="text-xl font-bold text-slate-800">Dashboard</h2>
                <p class="text-sm text-slate-500">Welcome back, {{ auth()->user()->name }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button class="p-2 rounded-lg text-slate-600 hover:bg-slate-100 transition-colors relative">
                <i class="ri-notification-3-line text-xl"></i>
                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>
        </div>
    </div>
</header>
