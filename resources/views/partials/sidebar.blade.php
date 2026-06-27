<aside class="w-72 bg-slate-900 text-white flex flex-col">

    <div class="p-6 border-b border-slate-700">

        <h1 class="text-3xl font-bold tracking-wide">
            PATUHA
        </h1>

        <p class="text-slate-400">
            Outdoor Store
        </p>

    </div>

    <nav class="flex-1 p-5 space-y-6">

        <div>

            <p class="text-xs uppercase text-slate-500 mb-3">
                Main
            </p>

            <a href="/dashboard" class="block px-4 py-3 rounded-xl hover:bg-blue-600 transition">

                🏠 Dashboard

            </a>

        </div>

        <div>

            <p class="text-xs uppercase text-slate-500 mb-3">
                Master Data
            </p>

            <div class="space-y-2">

                <a href="/kategori" class="block px-4 py-3 rounded-xl hover:bg-blue-600">
                    📁 Kategori
                </a>

                <a href="/barang" class="block px-4 py-3 rounded-xl hover:bg-blue-600">
                    📦 Barang
                </a>

                <a href="/varian" class="block px-4 py-3 rounded-xl hover:bg-blue-600">
                    🎨 Varian
                </a>

                <a href="/supplier" class="block px-4 py-3 rounded-xl hover:bg-blue-600">
                    🚚 Supplier
                </a>

            </div>

        </div>

        <div>

            <p class="text-xs uppercase text-slate-500 mb-3">
                Transaksi
            </p>

            <div class="space-y-2">

                <a href="/pembelian" class="block px-4 py-3 rounded-xl hover:bg-blue-600">
                    🛒 Pembelian
                </a>

                <a href="/penjualan" class="block px-4 py-3 rounded-xl hover:bg-blue-600">
                    💰 Penjualan
                </a>

            </div>

        </div>

        <div>

            <p class="text-xs uppercase text-slate-500 mb-3">
                Marketplace
            </p>

            <a href="/marketplace" class="block px-4 py-3 rounded-xl hover:bg-blue-600">

                🌐 Sinkronisasi

            </a>

        </div>

        <div>

            <p class="text-xs uppercase text-slate-500 mb-3">
                Laporan
            </p>

            <div class="space-y-2">

                <a href="{{ route('laporan.penjualan') }}" class="block px-4 py-3 rounded-xl hover:bg-blue-600">

                    📈 Laporan Penjualan

                </a>

                <a href="{{ route('laporan.pembelian') }}" class="block px-4 py-3 rounded-xl hover:bg-blue-600">

                    📉 Laporan Pembelian

                </a>

            </div>

        </div>

    </nav>

</aside>