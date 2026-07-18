<aside class="w-72 bg-slate-900 text-white flex flex-col">

    {{-- ========================================================= --}}
    {{-- BRAND --}}
    {{-- ========================================================= --}}

    <div class="p-6 border-b border-slate-700">

        <h1 class="text-3xl font-bold tracking-wide">
            PATUHA
        </h1>

        <p class="text-slate-400">
            Outdoor Store
        </p>

    </div>


    {{-- ========================================================= --}}
    {{-- NAVIGATION --}}
    {{-- ========================================================= --}}

    <nav class="flex-1 p-5 space-y-6 overflow-y-auto">

        {{-- ===================================================== --}}
        {{-- MAIN --}}
        {{-- ===================================================== --}}

        <div>

            <p class="text-xs uppercase text-slate-500 mb-3">
                Main
            </p>

            <a href="/dashboard" class="block px-4 py-3 rounded-xl transition
                    {{ request()->is('dashboard*')
    ? 'bg-blue-600 text-white'
    : 'hover:bg-slate-800' }}">
                🏠 Dashboard
            </a>

        </div>


        {{-- ===================================================== --}}
        {{-- MASTER DATA --}}
        {{-- ===================================================== --}}

        <div>

            <p class="text-xs uppercase text-slate-500 mb-3">
                Master Data
            </p>

            <div class="space-y-2">

                <a href="/kategori" class="block px-4 py-3 rounded-xl transition
                        {{ request()->is('kategori*')
    ? 'bg-blue-600 text-white'
    : 'hover:bg-slate-800' }}">
                    📁 Kategori
                </a>


                <a href="/barang" class="block px-4 py-3 rounded-xl transition
                        {{ request()->is('barang*')
    ? 'bg-blue-600 text-white'
    : 'hover:bg-slate-800' }}">
                    📦 Barang
                </a>

                <a href="/supplier" class="block px-4 py-3 rounded-xl transition
                        {{ request()->is('supplier*')
    ? 'bg-blue-600 text-white'
    : 'hover:bg-slate-800' }}">
                    🚚 Supplier
                </a>

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- TRANSAKSI --}}
        {{-- ===================================================== --}}

        <div>

            <p class="text-xs uppercase text-slate-500 mb-3">
                Transaksi
            </p>

            <div class="space-y-2">

                {{-- PENJUALAN / POS --}}

                <a href="/penjualan" class="block px-4 py-3 rounded-xl transition
                        {{ request()->is('penjualan*')
    ? 'bg-blue-600 text-white'
    : 'hover:bg-slate-800' }}">
                    💰 Penjualan / POS
                </a>


                {{-- ============================================= --}}
                {{-- PEMBELIAN --}}
                {{-- ============================================= --}}

                <div>

                    <p class="text-xs uppercase text-slate-500 mb-3">
                        Pembelian
                    </p>

                    <div class="space-y-2">

                        {{-- PERENCANAAN --}}
                        <a href="{{ route('perencanaan-pembelian.index') }}" class="block px-4 py-3 rounded-xl hover:bg-blue-600 transition
           {{ request()->routeIs('perencanaan-pembelian.*')
    ? 'bg-blue-600 text-white'
    : '' }}">

                            📋 Perencanaan

                        </a>

                        {{-- PENERIMAAN --}}
                        <a href="{{ route('penerimaan-pembelian.index') }}" class="block px-4 py-3 rounded-xl hover:bg-blue-600 transition
           {{ request()->routeIs('penerimaan-pembelian.*')
    ? 'bg-blue-600 text-white'
    : '' }}">

                            📥 Penerimaan Barang

                        </a>

                        {{-- RIWAYAT --}}
                        <a href="{{ route('pembelian.index') }}" class="block px-4 py-3 rounded-xl hover:bg-blue-600 transition
           {{ request()->routeIs('pembelian.*')
    ? 'bg-blue-600 text-white'
    : '' }}">

                            🕘 Riwayat Pembelian

                        </a>

                    </div>

                </div>

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- MARKETPLACE --}}
        {{-- ===================================================== --}}

        <div>

            <p class="text-xs uppercase text-slate-500 mb-3">
                Marketplace
            </p>

            <a href="/marketplace" class="block px-4 py-3 rounded-xl transition
                    {{ request()->is('marketplace*')
    ? 'bg-blue-600 text-white'
    : 'hover:bg-slate-800' }}">
                🌐 Sinkronisasi
            </a>

        </div>


        {{-- ===================================================== --}}
        {{-- LAPORAN --}}
        {{-- ===================================================== --}}

        <div>

            <p class="text-xs uppercase text-slate-500 mb-3">
                Laporan
            </p>

            <div class="space-y-2">

                <a href="{{ route('laporan.penjualan') }}" class="block px-4 py-3 rounded-xl transition
                        {{ request()->routeIs('laporan.penjualan')
    ? 'bg-blue-600 text-white'
    : 'hover:bg-slate-800' }}">
                    📈 Laporan Penjualan
                </a>


                <a href="{{ route('laporan.pembelian') }}" class="block px-4 py-3 rounded-xl transition
                        {{ request()->routeIs('laporan.pembelian')
    ? 'bg-blue-600 text-white'
    : 'hover:bg-slate-800' }}">
                    📉 Laporan Pembelian
                </a>

            </div>

        </div>

    </nav>

</aside>