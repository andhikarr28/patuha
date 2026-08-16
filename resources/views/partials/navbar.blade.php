<header class="bg-white border-b border-slate-200 px-4 md:px-8 h-20 flex items-center justify-between gap-3 min-w-0">

    <div class="flex items-center gap-3 min-w-0 flex-1">

        {{-- Mobile sidebar toggle --}}
        <button
            id="mobile-sidebar-toggle"
            type="button"
            class="md:hidden text-slate-500 hover:text-slate-900 p-2 -ml-2 rounded-lg hover:bg-slate-100"
            aria-label="Buka menu navigasi"
            aria-controls="sidebar"
            aria-expanded="false"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <div class="min-w-0">
            <h2 class="text-xl md:text-2xl font-bold text-slate-900 leading-tight truncate">
                @yield('page-title', 'Dashboard')
            </h2>
            <p class="text-xs text-slate-400 hidden md:block">
                {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
            </p>
        </div>

    </div>

    <div class="flex items-center gap-1.5 md:gap-4 shrink-0">

        {{-- USER MENU --}}
        <div class="relative" id="user-menu">
            <button
                id="user-menu-button"
                type="button"
                class="flex items-center gap-1.5 md:gap-2.5 pl-2 pr-1 py-1 rounded-full hover:bg-slate-100"
            >
                <div class="text-right min-w-0 max-w-24 md:max-w-none">
                    <p class="text-xs md:text-sm font-medium text-slate-800 leading-tight truncate">
                        {{ Auth::user()->name }}
                    </p>
                    <p class="text-[10px] md:text-[11px] text-slate-400 capitalize leading-tight truncate">
                        {{ Auth::user()->role }}
                    </p>
                </div>
                <div class="w-9 h-9 rounded-full bg-slate-900 text-white flex items-center justify-center text-sm font-semibold shrink-0">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400 hidden md:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 9l6 6 6-6"/>
                </svg>
            </button>

            {{-- DROPDOWN --}}
            <div
                id="user-menu-dropdown"
                class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-100 py-1.5 z-50"
            >
                <div class="px-3.5 py-2 border-b border-slate-100 md:hidden">
                    <p class="text-sm font-medium text-slate-800">{{ Auth::user()->name }}</p>
                    <p class="text-[11px] text-slate-400 capitalize">{{ Auth::user()->role }}</p>
                </div>

                <a href="{{ route('profile.edit') }}"
                   class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-slate-600 hover:bg-slate-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/>
                    </svg>
                    Profil Saya
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-2.5 px-3.5 py-2 text-sm text-red-500 hover:bg-red-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>

    </div>

</header>

<script>
    (function () {
        const btn = document.getElementById('user-menu-button');
        const dropdown = document.getElementById('user-menu-dropdown');

        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            dropdown.classList.toggle('hidden');
        });

        document.addEventListener('click', function (e) {
            if (!dropdown.contains(e.target) && !btn.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });

        // Mobile sidebar toggle
        const mobileBtn = document.getElementById('mobile-sidebar-toggle');

        if (mobileBtn) {
            mobileBtn.addEventListener('click', function () {
                const sidebar = document.getElementById('sidebar');
                window.setMobileSidebarOpen?.(!sidebar?.classList.contains('mobile-open'));
            });
        }
    })();
</script>
