<aside id="sidebar" class="bg-slate-900 text-white flex flex-col min-h-screen transition-all duration-200 ease-in-out"
    style="width: 256px;">
    {{-- BRAND --}}
    <div class="p-4 border-b border-slate-800 flex items-center justify-between">
        <div id="sidebar-brand" class="overflow-hidden whitespace-nowrap">
            <h1 class="text-xl font-bold tracking-tight">PATUHA</h1>
            <p class="text-slate-500 text-xs">Outdoor Store</p>
        </div>

        <button id="mobile-sidebar-close" type="button"
            class="md:hidden text-slate-400 hover:text-white p-1.5 rounded hover:bg-slate-800 shrink-0"
            aria-label="Tutup menu navigasi">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 6l12 12M18 6L6 18" />
            </svg>
        </button>

        <button id="sidebar-toggle" type="button"
            class="text-slate-400 hover:text-white p-1.5 rounded hover:bg-slate-800 shrink-0"
            title="Sembunyikan sidebar">
            <svg id="icon-collapse" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M11 19l-7-7 7-7M18 19l-7-7 7-7" />
            </svg>
            <svg id="icon-expand" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 hidden" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M13 5l7 7-7 7M6 5l7 7-7 7" />
            </svg>
        </button>
    </div>

    {{-- USER --}}
    <div class="px-4 pt-3">
        <div id="sidebar-user" class="bg-slate-800/60 rounded-lg px-3 py-2.5 flex items-center gap-2.5 overflow-hidden">
            <div
                class="w-7 h-7 rounded-full bg-blue-600 flex items-center justify-center text-xs font-semibold shrink-0">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="whitespace-nowrap overflow-hidden">
                <p class="text-sm font-medium leading-tight truncate">{{ auth()->user()->name }}</p>
                <p class="text-[11px] text-slate-500 capitalize leading-tight">{{ auth()->user()->role }}</p>
            </div>
        </div>
    </div>

    {{-- NAVIGATION --}}
    <nav class="flex-1 min-h-0 px-3 pt-5 pb-4 space-y-6 overflow-y-auto overflow-x-hidden text-sm">

        @php
            $navIcon = function ($path) {
                return '<svg xmlns="http://www.w3.org/2000/svg" class="w-[18px] h-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . $path . '</svg>';
            };
        @endphp

        {{-- MAIN --}}
        <div>
            <a href="{{ route('dashboard') }}"
                class="nav-link {{ request()->routeIs('dashboard') ? 'nav-active' : '' }}">
                {!! $navIcon('<path d="M3 9.5L12 3l9 6.5V20a1 1 0 01-1 1h-5v-7H9v7H4a1 1 0 01-1-1V9.5z"/>') !!}
                <span class="nav-label">Dashboard</span>
            </a>
        </div>

        {{-- PENJUALAN --}}
        @if(auth()->user()->hasRole(['kasir']))
            <div>
                <p class="nav-group-label">Penjualan</p>
                <a href="{{ route('penjualan.index') }}"
                    class="nav-link {{ request()->routeIs('penjualan.*', 'detail-penjualan.*') ? 'nav-active' : '' }}">
                    {!! $navIcon('<path d="M3 6h18M3 12h18M3 18h12"/>') !!}
                    <span class="nav-label">Transaksi Penjualan</span>
                </a>
            </div>
        @endif

        {{-- KATALOG --}}
        @if(auth()->user()->hasRole(['kasir', 'admin', 'owner']))
            <div>
                <p class="nav-group-label">Katalog</p>
                <a href="{{ route('barang.index') }}"
                    class="nav-link {{ request()->routeIs('barang.*') ? 'nav-active' : '' }}">
                    {!! $navIcon('<path d="M21 8l-9-5-9 5 9 5 9-5zM3 8v8l9 5 9-5V8M12 13v8"/>') !!}
                    <span class="nav-label">Barang</span>
                </a>
            </div>
        @endif

        {{-- MASTER DATA - ADMIN ONLY --}}
        @if(auth()->user()->hasRole(['admin']))
            <div>
                <p class="nav-group-label">Master Data</p>
                <a href="{{ route('kategori.index') }}"
                    class="nav-link {{ request()->routeIs('kategori.*') ? 'nav-active' : '' }}">
                    {!! $navIcon('<path d="M4 6h16M4 12h16M4 18h7"/>') !!}
                    <span class="nav-label">Kategori</span>
                </a>
                <a href="{{ route('supplier.index') }}"
                    class="nav-link {{ request()->routeIs('supplier.*') ? 'nav-active' : '' }}">
                    {!! $navIcon('<path d="M3 13h13l4 4V8a1 1 0 00-1-1h-3M3 13V6a1 1 0 011-1h8a1 1 0 011 1v7M3 13l3 4h9"/>') !!}
                    <span class="nav-label">Supplier</span>
                </a>
            </div>
        @endif

        {{-- PERENCANAAN - ADMIN & OWNER --}}
        @if(auth()->user()->hasRole(['admin', 'owner']))
            <div>
                <p class="nav-group-label">Perencanaan</p>
                <a href="{{ route('perencanaan-pembelian.index') }}"
                    class="nav-link {{ request()->routeIs('perencanaan-pembelian.*') ? 'nav-active' : '' }}">
                    {!! $navIcon('<path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2M9 13h6M9 17h6"/>') !!}
                    <span class="nav-label">Perencanaan Pembelian</span>
                </a>
            </div>
        @endif

        {{-- PEMBELIAN - ADMIN ONLY --}}
        @if(auth()->user()->hasRole(['admin']))
            <div>
                <p class="nav-group-label">Pembelian</p>
                <a href="{{ route('penerimaan-pembelian.index') }}"
                    class="nav-link {{ request()->routeIs('penerimaan-pembelian.*') ? 'nav-active' : '' }}">
                    {!! $navIcon('<path d="M12 3v12m0 0l-4-4m4 4l4-4M4 17v2a2 2 0 002 2h12a2 2 0 002-2v-2"/>') !!}
                    <span class="nav-label">Penerimaan</span>
                </a>
                <a href="{{ route('pembelian.index') }}"
                    class="nav-link {{ request()->routeIs('pembelian.*', 'detail-pembelian.*') ? 'nav-active' : '' }}">
                    {!! $navIcon('<path d="M6 6h15l-1.5 9h-12z"/><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/><path d="M6 6L5 3H2"/>') !!}
                    <span class="nav-label">Riwayat Pembelian</span>
                </a>
            </div>
        @endif

        {{-- MARKETPLACE - ADMIN ONLY --}}
        @if(auth()->user()->hasRole(['admin']))
            <div>
                <p class="nav-group-label">Marketplace</p>
                <a href="{{ route('marketplace.index') }}"
                    class="nav-link {{ request()->routeIs('marketplace.*', 'shopee.*') ? 'nav-active' : '' }}">
                    {!! $navIcon('<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a15 15 0 010 18M12 3a15 15 0 000 18"/>') !!}
                    <span class="nav-label">Sinkronisasi</span>
                </a>
            </div>
        @endif

        {{-- LAPORAN - ADMIN + OWNER --}}
        @if(auth()->user()->hasRole(['admin']))
            <div>
                <p class="nav-group-label">Laporan</p>
                <a href="{{ route('laporan-bulanan.index') }}"
                    class="nav-link {{ request()->routeIs('laporan-bulanan.*') ? 'nav-active' : '' }}">
                    {!! $navIcon('<rect x="3" y="4" width="18" height="17" rx="2"/><path d="M3 9h18M8 3v3M16 3v3"/>') !!}
                    <span class="nav-label">Laporan Bulanan</span>
                </a>
            </div>
        @endif

        @if(auth()->user()->hasRole(['owner']))
            <div>
                <p class="nav-group-label">Laporan</p>
                <a href="{{ route('laporan.penjualan') }}"
                    class="nav-link {{ request()->routeIs('laporan.penjualan', 'laporan.penjualan.pdf') ? 'nav-active' : '' }}">
                    {!! $navIcon('<path d="M4 19V9m6 10V5m6 14v-7"/>') !!}
                    <span class="nav-label">Rekap Penjualan</span>
                </a>
                <a href="{{ route('laporan.pembelian') }}"
                    class="nav-link {{ request()->routeIs('laporan.pembelian', 'laporan.pembelian.pdf') ? 'nav-active' : '' }}">
                    {!! $navIcon('<path d="M4 5v14m6-10v10m6-6v6"/>') !!}
                    <span class="nav-label">Rekap Pembelian</span>
                </a>
                <a href="{{ route('laporan-bulanan.index') }}"
                    class="nav-link {{ request()->routeIs('laporan-bulanan.*') ? 'nav-active' : '' }}">
                    {!! $navIcon('<rect x="3" y="4" width="18" height="17" rx="2"/><path d="M3 9h18M8 3v3M16 3v3"/>') !!}
                    <span class="nav-label">Laporan Bulanan</span>
                </a>
            </div>
        @endif

    </nav>

</aside>

<style>
    .nav-group-label {
        font-size: 10.5px;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: rgb(100 116 139);
        margin-bottom: 6px;
        padding: 0 10px;
        white-space: nowrap;
        overflow: hidden;
    }

    .nav-link {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 10px;
        margin-bottom: 2px;
        border-radius: 8px;
        color: rgb(203 213 225);
        border-left: 2px solid transparent;
        white-space: nowrap;
        overflow: hidden;
    }

    .nav-link:hover {
        background: rgba(255, 255, 255, 0.05);
        color: #fff;
    }

    .nav-link.nav-active {
        background: rgba(37, 99, 235, 0.12);
        color: #fff;
        border-left: 2px solid #3b82f6;
    }

    .nav-label {
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* --- collapsed state --- */
    #sidebar.collapsed {
        width: 76px !important;
    }

    #sidebar.collapsed .nav-label,
    #sidebar.collapsed .nav-group-label,
    #sidebar.collapsed #sidebar-brand,
    #sidebar.collapsed #sidebar-user>div:last-child {
        display: none;
    }

    #sidebar.collapsed .nav-link {
        justify-content: center;
        padding: 10px;
    }

    #sidebar.collapsed #sidebar-user {
        justify-content: center;
    }

    #sidebar.collapsed .p-4.border-b {
        justify-content: center;
    }

    /* --- MOBILE: sidebar jadi off-canvas, hilang dari layar sampai dibuka --- */
    @media (max-width: 767px) {
        #sidebar,
        #sidebar.collapsed {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            height: 100vh;
            z-index: 50;
            flex: none;
            width: 18rem !important;
            max-width: calc(100vw - 1rem);
            transform: translateX(-100%);
        }

        #sidebar.mobile-open {
            transform: translateX(0);
        }

        /* Di mobile, abaikan state "collapsed" desktop - selalu tampil penuh saat dibuka */
        #sidebar.collapsed .nav-label,
        #sidebar.collapsed .nav-group-label,
        #sidebar.collapsed #sidebar-brand,
        #sidebar.collapsed #sidebar-user>div:last-child {
            display: block;
        }

        #sidebar.collapsed .nav-link {
            justify-content: flex-start;
            padding: 8px 10px;
        }

        #sidebar.collapsed #sidebar-user {
            justify-content: flex-start;
        }

        #sidebar.collapsed .p-4.border-b {
            justify-content: space-between;
        }

        #sidebar-toggle {
            display: none;
        }
    }

    @media (min-width: 768px) {
        #sidebar {
            position: relative;
            transform: none !important;
        }
    }
</style>

<script>
    (function () {
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('sidebar-toggle');
        const iconCollapse = document.getElementById('icon-collapse');
        const iconExpand = document.getElementById('icon-expand');
        const mobileCloseBtn = document.getElementById('mobile-sidebar-close');
        const mobileMediaQuery = window.matchMedia('(max-width: 767px)');

        function applyState(collapsed) {
            sidebar.classList.toggle('collapsed', collapsed);
            iconCollapse.classList.toggle('hidden', collapsed);
            iconExpand.classList.toggle('hidden', !collapsed);
            toggleBtn.title = collapsed ? 'Tampilkan sidebar' : 'Sembunyikan sidebar';
        }

        const saved = localStorage.getItem('sidebar-collapsed') === '1';
        applyState(saved);

        toggleBtn.addEventListener('click', function () {
            const collapsed = !sidebar.classList.contains('collapsed');
            applyState(collapsed);
            localStorage.setItem('sidebar-collapsed', collapsed ? '1' : '0');
        });

        function setMobileSidebarOpen(isOpen) {
            if (!mobileMediaQuery.matches) {
                return;
            }

            const backdrop = document.getElementById('sidebar-backdrop');
            const mobileToggleBtn = document.getElementById('mobile-sidebar-toggle');

            sidebar.classList.toggle('mobile-open', isOpen);
            backdrop?.classList.toggle('hidden', !isOpen);
            backdrop?.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
            mobileToggleBtn?.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            document.body.classList.toggle('overflow-hidden', isOpen);

            if (isOpen) {
                mobileCloseBtn?.focus();
            } else {
                mobileToggleBtn?.focus();
            }
        }

        window.setMobileSidebarOpen = setMobileSidebarOpen;

        mobileCloseBtn?.addEventListener('click', function () {
            setMobileSidebarOpen(false);
        });

        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('sidebar-backdrop')?.addEventListener('click', function () {
                setMobileSidebarOpen(false);
            });
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && sidebar.classList.contains('mobile-open')) {
                setMobileSidebarOpen(false);
            }
        });

        mobileMediaQuery.addEventListener('change', function (event) {
            if (!event.matches) {
                const backdrop = document.getElementById('sidebar-backdrop');
                const mobileToggleBtn = document.getElementById('mobile-sidebar-toggle');

                sidebar.classList.remove('mobile-open');
                backdrop?.classList.add('hidden');
                backdrop?.setAttribute('aria-hidden', 'true');
                mobileToggleBtn?.setAttribute('aria-expanded', 'false');
                document.body.classList.remove('overflow-hidden');
            }
        });

        // Tutup sidebar mobile kalau klik salah satu menu (biar tidak nyangkut kebuka)
        document.querySelectorAll('#sidebar .nav-link').forEach(function (link) {
            link.addEventListener('click', function () {
                setMobileSidebarOpen(false);
            });
        });
    })();
</script>
