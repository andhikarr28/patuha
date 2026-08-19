@extends('layouts.app')

@section('content')
    <div class="p-4">

        {{-- HEADER --}}
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Kasir</h1>
                <p class="text-slate-500 text-sm">Pilih barang, lalu selesaikan pembayaran.</p>
            </div>
            <a href="{{ route('penjualan.index') }}"
                class="border border-slate-300 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Riwayat Penjualan
            </a>
        </div>

        {{-- ERROR --}}
        @if ($errors->any())
            <div class="mb-4 border border-red-300 bg-red-50 text-red-700 rounded-lg p-4 text-sm">
                <p class="font-semibold mb-1">Transaksi tidak dapat diproses:</p>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="formPenjualan" action="{{ route('penjualan.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 xl:grid-cols-12 gap-4 items-start">

                {{-- KIRI: DAFTAR BARANG --}}
                <div class="xl:col-span-7 space-y-3">

                    {{-- SEARCH --}}
                    <div class="relative">
                        <input type="text" id="searchProduk" placeholder="Cari nama barang atau SKU..." autocomplete="off"
                            class="w-full border border-slate-300 rounded-lg pl-4 pr-4 py-3.5 text-base focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
                    </div>

                    {{-- KATEGORI --}}
                    <div id="categoryFilters" class="flex gap-2 overflow-x-auto pb-1">
                        <button type="button" data-category="all"
                            class="category-btn shrink-0 whitespace-nowrap rounded-lg px-4 py-2.5 text-sm font-bold bg-blue-600 text-white">
                            Semua
                        </button>

                        @php
                            $kategoriList = $varian->map(fn($v) => $v->barang?->kategori)->filter()->unique('id')->values();
                        @endphp

                        @foreach($kategoriList as $kategori)
                            <button type="button" data-category="{{ $kategori->id }}"
                                class="category-btn shrink-0 whitespace-nowrap rounded-lg px-4 py-2.5 text-sm font-bold bg-slate-100 text-slate-600">
                                {{ $kategori->nama_kategori }}
                            </button>
                        @endforeach
                    </div>

                    {{-- DAFTAR BARANG (LIST) --}}
                    <div class="border border-slate-200 rounded-lg bg-white overflow-hidden">

                        <div
                            class="px-4 py-2.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between text-xs font-bold text-slate-500 uppercase">
                            <span>Barang</span>
                            <span id="productCounter">{{ $varian->count() }} tersedia</span>
                        </div>

                        <div id="productGrid" class="divide-y divide-slate-100 max-h-[65vh] overflow-y-auto">
                            @forelse($varian as $item)
                                @php
                                    $namaBarang = $item->barang?->nama_barang ?? 'Barang';
                                    $kategoriId = $item->barang?->kategori_id ?? '';
                                    $namaLengkap = $namaBarang . ' ' . ($item->warna ?? '') . ' ' . ($item->ukuran ?? '');
                                    $foto = $item->barang?->foto ? asset('storage/' . $item->barang->foto) : null;
                                @endphp

                                <button type="button"
                                    class="product-row w-full text-left flex items-center gap-3 px-4 py-3 hover:bg-blue-50 disabled:opacity-40 disabled:cursor-not-allowed transition"
                                    data-id="{{ $item->id }}"
                                    data-name="{{ strtolower($namaLengkap . ' ' . ($item->sku ?? '')) }}"
                                    data-category="{{ $kategoriId }}"
                                    data-nama="{{ $namaBarang }} - {{ $item->warna }} - {{ $item->ukuran }}"
                                    data-harga="{{ (int) $item->harga_jual }}" data-stok="{{ (int) $item->stok }}"
                                    data-sku="{{ $item->sku ?? '-' }}" {{ $item->stok <= 0 ? 'disabled' : '' }}>

                                    <div
                                        class="w-14 h-14 shrink-0 rounded-lg bg-slate-100 overflow-hidden flex items-center justify-center">
                                        @if($foto)
                                            <img src="{{ $foto }}" alt="{{ $namaBarang }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-[10px] text-slate-400 text-center leading-tight">No<br>Foto</span>
                                        @endif
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <p class="font-bold text-slate-900 truncate">{{ $namaBarang }}</p>
                                        <p class="text-sm text-slate-500">
                                            {{ $item->warna ?: '-' }}{{ $item->ukuran ? ' / ' . $item->ukuran : '' }} &middot;
                                            SKU {{ $item->sku ?? '-' }}</p>
                                        @if($item->stok <= 0)
                                            <p class="text-xs font-semibold text-red-600 mt-0.5">Stok habis</p>
                                        @elseif($item->stok <= $item->stok_minimum)
                                            <p class="text-xs font-semibold text-yellow-600 mt-0.5">Stok {{ $item->stok }} &middot; Stok menipis</p>
                                        @else
                                            <p class="text-xs font-semibold text-green-700 mt-0.5">Stok {{ $item->stok }}</p>
                                        @endif
                                    </div>

                                    <div class="text-right shrink-0">
                                        <p class="font-bold text-blue-600 text-base">Rp
                                            {{ number_format($item->harga_jual, 0, ',', '.') }}</p>
                                        <span
                                            class="inline-flex items-center justify-center mt-1 w-8 h-8 rounded-full bg-blue-600 text-white text-lg font-bold">+</span>
                                    </div>
                                </button>
                            @empty
                                <div class="text-center py-14 text-slate-500">Belum ada varian barang.</div>
                            @endforelse
                        </div>

                        <div id="emptyProducts" class="hidden text-center py-14">
                            <p class="font-bold text-slate-700">Barang tidak ditemukan</p>
                            <p class="text-sm text-slate-500 mt-1">Coba kata pencarian lain.</p>
                        </div>
                    </div>
                </div>

                {{-- KANAN: KERANJANG --}}
                <div class="xl:col-span-5">
                    <div class="border border-slate-200 rounded-lg bg-white xl:sticky xl:top-4 overflow-hidden">

                        <div class="p-4 border-b border-slate-200 flex justify-between items-center">
                            <h2 class="text-lg font-bold text-slate-900">Keranjang <span id="cartCount"
                                    class="text-slate-400 font-normal">(0)</span></h2>
                            <button type="button" id="btnClearCart"
                                class="text-sm text-red-500 font-bold">Kosongkan</button>
                        </div>

                        <div id="cartItems" class="p-4 max-h-[38vh] overflow-y-auto">
                            <div id="emptyCart" class="text-center py-10 text-slate-500">
                                Keranjang masih kosong.<br>Klik barang di sebelah kiri untuk menambahkan.
                            </div>
                        </div>

                        <div class="border-t border-slate-200 p-4 space-y-3">

                            <div
                                class="flex justify-between items-center text-base font-bold text-slate-900 bg-slate-50 rounded-lg px-4 py-3">
                                <span>Total Bayar</span>
                                <span id="grandTotal" class="text-2xl text-blue-600">Rp 0</span>
                            </div>

                            {{-- METODE PEMBAYARAN: TOMBOL, BUKAN DROPDOWN --}}
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Metode Pembayaran</label>
                                <input type="hidden" name="metode_pembayaran" id="metodePembayaran" value="cash">
                                <div class="grid grid-cols-2 gap-2">
                                    <button type="button"
                                        class="metode-btn rounded-lg py-3 text-sm font-bold border-2 border-blue-600 bg-blue-600 text-white"
                                        data-metode="cash">Cash</button>
                                    <button type="button"
                                        class="metode-btn rounded-lg py-3 text-sm font-bold border-2 border-slate-200 text-slate-600"
                                        data-metode="qris">QRIS</button>
                                    <button type="button"
                                        class="metode-btn rounded-lg py-3 text-sm font-bold border-2 border-slate-200 text-slate-600"
                                        data-metode="transfer">Transfer</button>
                                    <button type="button"
                                        class="metode-btn rounded-lg py-3 text-sm font-bold border-2 border-slate-200 text-slate-600"
                                        data-metode="debit">Debit</button>
                                </div>
                            </div>

                            {{-- INFO TRANSAKSI RINGKAS --}}
                            <details class="text-sm">
                                <summary class="cursor-pointer text-slate-500 font-semibold">Detail transaksi (nota,
                                    tanggal, channel)</summary>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 mt-3">
                                    <div>
                                        <p class="text-xs text-slate-400 mb-1">No Nota</p>
                                        <input type="text" name="no_nota" value="PJ-{{ now()->format('YmdHis') }}" readonly
                                            class="w-full border border-slate-200 rounded px-2 py-1.5 text-sm bg-slate-50">
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-400 mb-1">Tanggal</p>
                                        <input type="date" name="tanggal_penjualan" value="{{ date('Y-m-d') }}"
                                            class="w-full border border-slate-200 rounded px-2 py-1.5 text-sm">
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-400 mb-1">Channel</p>
                                        <select name="channel"
                                            class="w-full border border-slate-200 rounded px-2 py-1.5 text-sm">
                                            <option value="offline">Offline</option>
                                            <option value="shopee">Shopee</option>
                                            <option value="tokopedia">Tokopedia</option>
                                            <option value="tiktok">TikTok Shop</option>
                                        </select>
                                    </div>
                                </div>
                            </details>

                            {{-- BAYAR --}}
                            <button id="btnBayar" type="submit" disabled
                                class="w-full bg-green-600 disabled:bg-slate-300 disabled:cursor-not-allowed text-white font-bold text-lg py-4 rounded-lg transition">
                                BAYAR <span id="buttonTotal">(Rp 0)</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let cart = [];
            let activeCategory = 'all';

            const form = document.getElementById('formPenjualan');
            const cartItems = document.getElementById('cartItems');
            const cartCount = document.getElementById('cartCount');
            const grandTotal = document.getElementById('grandTotal');
            const buttonTotal = document.getElementById('buttonTotal');
            const btnBayar = document.getElementById('btnBayar');
            const btnClearCart = document.getElementById('btnClearCart');
            const searchProduk = document.getElementById('searchProduk');
            const emptyProducts = document.getElementById('emptyProducts');
            const productCounter = document.getElementById('productCounter');
            const metodeInput = document.getElementById('metodePembayaran');

            function rupiah(value) {
                return 'Rp ' + Number(value).toLocaleString('id-ID');
            }

            /* TAMBAH PRODUK */
            document.querySelectorAll('.product-row').forEach(function (row) {
                row.addEventListener('click', function () {
                    if (row.disabled) return;

                    const varianId = row.dataset.id;
                    const nama = row.dataset.nama;
                    const harga = Number(row.dataset.harga);
                    const stok = Number(row.dataset.stok);
                    const sku = row.dataset.sku;

                    const existing = cart.find(item => String(item.varian_id) === String(varianId));

                    if (existing) {
                        if (existing.qty >= stok) {
                            alert('Jumlah tidak dapat melebihi stok tersedia.');
                            return;
                        }
                        existing.qty++;
                        existing.subtotal = existing.qty * existing.harga;
                    } else {
                        cart.push({ varian_id: varianId, nama, sku, qty: 1, harga, stok, subtotal: harga });
                    }

                    renderCart();
                });
            });

            /* RENDER KERANJANG */
            function renderCart() {
                document.querySelectorAll('.cart-hidden').forEach(el => el.remove());

                if (cart.length === 0) {
                    cartItems.innerHTML = `
                    <div class="text-center py-10 text-slate-500">
                        Keranjang masih kosong.<br>Klik barang di sebelah kiri untuk menambahkan.
                    </div>
                `;
                } else {
                    let html = '';

                    cart.forEach(function (item, index) {
                        html += `
                        <div class="py-3 border-b border-slate-100 last:border-0">
                            <div class="flex justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="font-bold text-slate-900">${item.nama}</p>
                                    <p class="text-sm text-slate-400">${rupiah(item.harga)} / pcs</p>
                                </div>
                                <button type="button" onclick="hapusItem(${index})" class="text-red-500 text-2xl leading-none px-1">&times;</button>
                            </div>

                            <div class="flex justify-between items-center mt-2">
                                <div class="inline-flex items-center border-2 border-slate-200 rounded-lg overflow-hidden">
                                    <button type="button" onclick="kurangiQty(${index})" class="w-10 h-10 text-xl font-bold hover:bg-slate-100">&minus;</button>
                                    <span class="w-10 text-center text-base font-bold">${item.qty}</span>
                                    <button type="button" onclick="tambahQty(${index})" class="w-10 h-10 text-xl font-bold hover:bg-slate-100">+</button>
                                </div>

                                <p class="font-bold text-slate-900 text-base">${rupiah(item.subtotal)}</p>
                            </div>
                        </div>
                    `;

                        form.insertAdjacentHTML('beforeend', `
                        <input class="cart-hidden" type="hidden" name="cart[${index}][varian_id]" value="${item.varian_id}">
                        <input class="cart-hidden" type="hidden" name="cart[${index}][qty]" value="${item.qty}">
                    `);
                    });

                    cartItems.innerHTML = html;
                }

                const total = cart.reduce((sum, item) => sum + item.subtotal, 0);
                const totalQty = cart.reduce((sum, item) => sum + item.qty, 0);

                cartCount.textContent = '(' + totalQty + ')';
                grandTotal.textContent = rupiah(total);
                buttonTotal.textContent = '(' + rupiah(total) + ')';

                btnBayar.disabled = cart.length === 0;
            }

            window.tambahQty = function (index) {
                const item = cart[index];
                if (item.qty >= item.stok) {
                    alert('Jumlah tidak dapat melebihi stok tersedia.');
                    return;
                }
                item.qty++;
                item.subtotal = item.qty * item.harga;
                renderCart();
            };

            window.kurangiQty = function (index) {
                const item = cart[index];
                if (item.qty <= 1) {
                    cart.splice(index, 1);
                } else {
                    item.qty--;
                    item.subtotal = item.qty * item.harga;
                }
                renderCart();
            };

            window.hapusItem = function (index) {
                cart.splice(index, 1);
                renderCart();
            };

            btnClearCart.addEventListener('click', function () {
                if (cart.length === 0) return;
                if (confirm('Kosongkan seluruh keranjang?')) {
                    cart = [];
                    renderCart();
                }
            });

            /* METODE PEMBAYARAN */
            document.querySelectorAll('.metode-btn').forEach(function (button) {
                button.addEventListener('click', function () {
                    metodeInput.value = button.dataset.metode;

                    document.querySelectorAll('.metode-btn').forEach(function (btn) {
                        btn.classList.remove('border-blue-600', 'bg-blue-600', 'text-white');
                        btn.classList.add('border-slate-200', 'text-slate-600');
                    });

                    button.classList.remove('border-slate-200', 'text-slate-600');
                    button.classList.add('border-blue-600', 'bg-blue-600', 'text-white');
                });
            });

            /* SEARCH + KATEGORI */
            function filterProducts() {
                const keyword = searchProduk.value.toLowerCase().trim();
                let visible = 0;

                document.querySelectorAll('.product-row').forEach(function (row) {
                    const name = row.dataset.name;
                    const category = row.dataset.category;

                    const matchSearch = name.includes(keyword);
                    const matchCategory = activeCategory === 'all' || String(category) === String(activeCategory);
                    const show = matchSearch && matchCategory;

                    row.classList.toggle('hidden', !show);
                    if (show) visible++;
                });

                emptyProducts.classList.toggle('hidden', visible !== 0);
                productCounter.textContent = visible + ' tersedia';
            }

            searchProduk.addEventListener('input', filterProducts);

            document.querySelectorAll('.category-btn').forEach(function (button) {
                button.addEventListener('click', function () {
                    activeCategory = button.dataset.category;

                    document.querySelectorAll('.category-btn').forEach(function (btn) {
                        btn.classList.remove('bg-blue-600', 'text-white');
                        btn.classList.add('bg-slate-100', 'text-slate-600');
                    });

                    button.classList.remove('bg-slate-100', 'text-slate-600');
                    button.classList.add('bg-blue-600', 'text-white');

                    filterProducts();
                });
            });

            /* SUBMIT */
            form.addEventListener('submit', function (event) {
                if (cart.length === 0) {
                    event.preventDefault();
                    alert('Keranjang masih kosong.');
                    return;
                }
                btnBayar.disabled = true;
                btnBayar.innerHTML = 'Memproses...';
            });

            filterProducts();
            renderCart();
        });
    </script>
@endsection
