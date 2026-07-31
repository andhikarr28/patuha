<aside class="w-64 bg-slate-900 text-white flex flex-col min-h-screen">

    {{-- BRAND --}}
    <div class="p-4 border-b border-slate-700">
        <h1 class="text-2xl font-bold">PATUHA</h1>
        <p class="text-slate-400 text-sm">Outdoor Store</p>

        <div class="mt-4 bg-slate-800 rounded p-3">
            <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
            <p class="text-xs text-slate-400 capitalize mt-1">● {{ auth()->user()->role }}</p>
        </div>
    </div>

    {{-- NAVIGATION --}}
    <nav class="flex-1 p-4 space-y-5 overflow-y-auto text-sm">

        {{-- MAIN --}}
        <div>
            <p class="text-xs uppercase text-slate-500 mb-2">Main</p>
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                🏠 Dashboard
            </a>
        </div>

        {{-- PENJUALAN --}}
        @if(auth()->user()->hasRole(['kasir', 'admin', 'owner']))
            <div>
                <p class="text-xs uppercase text-slate-500 mb-2">Penjualan</p>
                <a href="{{ route('penjualan.index') }}" class="flex items-center gap-2 px-3 py-2 rounded {{ request()->routeIs('penjualan.*', 'detail-penjualan.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                    💰 Transaksi Penjualan
                </a>
            </div>
        @endif

        {{-- ADMIN + OWNER ONLY --}}
        @if(auth()->user()->hasRole(['admin', 'owner']))

            {{-- MASTER DATA --}}
            <div>
                <p class="text-xs uppercase text-slate-500 mb-2">Master Data</p>
                <div class="space-y-1">
                    <a href="{{ route('kategori.index') }}" class="flex items-center gap-2 px-3 py-2 rounded {{ request()->routeIs('kategori.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                        📁 Kategori
                    </a>
                    <a href="{{ route('barang.index') }}" class="flex items-center gap-2 px-3 py-2 rounded {{ request()->routeIs('barang.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                        📦 Barang
                    </a>
                    <a href="{{ route('supplier.index') }}" class="flex items-center gap-2 px-3 py-2 rounded {{ request()->routeIs('supplier.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                        🚚 Supplier
                    </a>
                </div>
            </div>

            {{-- PEMBELIAN --}}
            <div>
                <p class="text-xs uppercase text-slate-500 mb-2">Pembelian</p>
                <div class="space-y-1">
                    <a href="{{ route('perencanaan-pembelian.index') }}" class="flex items-center gap-2 px-3 py-2 rounded {{ request()->routeIs('perencanaan-pembelian.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                        📋 Perencanaan
                    </a>
                    <a href="{{ route('penerimaan-pembelian.index') }}" class="flex items-center gap-2 px-3 py-2 rounded {{ request()->routeIs('penerimaan-pembelian.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                        📥 Penerimaan
                    </a>
                    <a href="{{ route('pembelian.index') }}" class="flex items-center gap-2 px-3 py-2 rounded {{ request()->routeIs('pembelian.*', 'detail-pembelian.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                        🛒 Riwayat Pembelian
                    </a>
                </div>
            </div>

            {{-- MARKETPLACE --}}
            <div>
                <p class="text-xs uppercase text-slate-500 mb-2">Marketplace</p>
                <a href="{{ route('marketplace.index') }}" class="flex items-center gap-2 px-3 py-2 rounded {{ request()->routeIs('marketplace.*', 'shopee.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                    🌐 Sinkronisasi
                </a>
            </div>

            {{-- LAPORAN --}}
            <div>
                <p class="text-xs uppercase text-slate-500 mb-2">Laporan</p>
                <div class="space-y-1">
                    <a href="{{ route('laporan.penjualan') }}" class="flex items-center gap-2 px-3 py-2 rounded {{ request()->routeIs('laporan.penjualan', 'laporan.penjualan.pdf') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                        📈 Laporan Penjualan
                    </a>
                    <a href="{{ route('laporan.pembelian') }}" class="flex items-center gap-2 px-3 py-2 rounded {{ request()->routeIs('laporan.pembelian', 'laporan.pembelian.pdf') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                        📉 Laporan Pembelian
                    </a>
                </div>
            </div>

        @endif

    </nav>

    {{-- FOOTER --}}
    <div class="p-4 border-t border-slate-700">
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-3 py-2 text-slate-300 hover:bg-slate-800 rounded text-sm">
            👤 Profil
        </a>
    </div>

</aside>