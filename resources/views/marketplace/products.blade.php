@extends('layouts.app')

@section('content')
<div class="p-4 space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <a href="{{ route('marketplace.index') }}" class="text-sm text-blue-600">← Kembali ke Marketplace</a>
            <h1 class="text-2xl font-bold">Produk Shopee</h1>
            <p class="text-gray-500 text-sm">Daftar produk yang telah disinkronkan dari Shopee ke sistem.</p>
        </div>

        <form action="{{ route('marketplace.sync.products') }}" method="POST">
            @csrf
            <button type="submit" class="bg-blue-600 text-white rounded px-4 py-2 text-sm font-semibold">🔄 Sinkron Produk</button>
        </form>
    </div>

    {{-- SUMMARY --}}
    @php
        $aktifCount = $products->filter(fn($p) => in_array(strtolower($p->status ?? ''), ['normal', 'active', 'aktif']))->count();
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div class="border rounded p-3">
            <p class="text-sm text-gray-500">Total Produk</p>
            <p class="text-xl font-bold">{{ $products->count() }}</p>
        </div>
        <div class="border rounded p-3">
            <p class="text-sm text-gray-500">Produk Aktif</p>
            <p class="text-xl font-bold text-green-600">{{ $aktifCount }}</p>
        </div>
        <div class="border rounded p-3">
            <p class="text-sm text-gray-500">Sumber Data</p>
            <p class="font-semibold">Shopee</p>
            <p class="text-xs text-green-600">● Data tersinkron</p>
        </div>
    </div>

    {{-- INFO --}}
    <div class="border border-blue-200 bg-blue-50 rounded p-4 text-sm text-blue-800">
        Data pada halaman ini berasal dari Shopee. Gunakan sinkronisasi untuk memperbarui produk jika ada perubahan.
    </div>

    {{-- PRODUCT LIST --}}
    <div class="border rounded">
        <div class="px-4 py-3 border-b flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h2 class="font-bold">Daftar Produk</h2>
                <p class="text-sm text-gray-500">Produk dari akun Shopee terhubung.</p>
            </div>
            <input type="text" id="productSearch" placeholder="Cari produk atau ID..." class="border rounded px-3 py-2 text-sm w-full md:w-64">
        </div>

        @if($products->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="px-4 py-2">Produk</th>
                            <th class="px-4 py-2">ID Shopee</th>
                            <th class="px-4 py-2 text-center">Berat</th>
                            <th class="px-4 py-2 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody id="productTable">
                        @foreach($products as $product)
                            @php
                                $status = strtolower($product->status ?? '');
                                $isActive = in_array($status, ['normal', 'active', 'aktif']);
                            @endphp
                            <tr class="product-row border-b">
                                <td class="px-4 py-3">
                                    <p class="product-name font-semibold">{{ $product->nama_produk ?: 'Produk Tanpa Nama' }}</p>
                                    <p class="text-xs text-gray-400">Produk Shopee</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="product-id text-xs bg-gray-100 rounded px-2 py-1">{{ $product->external_product_id ?: '-' }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($product->berat)
                                        {{ number_format($product->berat, 0, ',', '.') }} gram
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($isActive)
                                        <span class="text-green-700 font-semibold">● Aktif</span>
                                    @elseif($product->status)
                                        <span class="text-gray-500">● {{ ucfirst(strtolower($product->status)) }}</span>
                                    @else
                                        <span class="text-gray-400">Tidak diketahui</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div id="noSearchResult" class="hidden py-10 text-center">
                <p class="font-semibold">Produk Tidak Ditemukan</p>
                <p class="text-sm text-gray-500 mt-1">Coba gunakan nama produk atau ID Shopee yang berbeda.</p>
            </div>

            <div class="px-4 py-3 border-t text-sm text-gray-500">
                Menampilkan {{ $products->count() }} produk Shopee.
            </div>
        @else
            <div class="text-center py-10 px-4">
                <p class="font-semibold">Belum Ada Produk Shopee</p>
                <p class="text-sm text-gray-500 mt-1">Lakukan sinkronisasi untuk mengambil data produk dari Shopee.</p>
                <form action="{{ route('marketplace.sync.products') }}" method="POST" class="mt-3">
                    @csrf
                    <button type="submit" class="bg-blue-600 text-white rounded px-4 py-2 text-sm font-semibold">🔄 Sinkron Produk Sekarang</button>
                </form>
            </div>
        @endif
    </div>

</div>

<script>
const productSearch = document.getElementById('productSearch');

if (productSearch) {
    productSearch.addEventListener('input', function () {
        const keyword = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll('.product-row');
        let visible = 0;

        rows.forEach(row => {
            const name = row.querySelector('.product-name')?.textContent.toLowerCase() || '';
            const id = row.querySelector('.product-id')?.textContent.toLowerCase() || '';
            const match = name.includes(keyword) || id.includes(keyword);

            row.style.display = match ? '' : 'none';
            if (match) visible++;
        });

        const empty = document.getElementById('noSearchResult');
        if (empty) empty.classList.toggle('hidden', visible !== 0);
    });
}
</script>
@endsection