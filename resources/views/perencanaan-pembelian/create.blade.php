@extends('layouts.app')

@section('content')
    <div x-data="purchasePlan()" class="p-4 space-y-4">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Buat Perencanaan Pembelian</h1>
                <p class="text-slate-500 text-sm">Pilih supplier dan barang yang akan direncanakan untuk dibeli.</p>
            </div>
            <a href="{{ route('perencanaan-pembelian.index') }}"
                class="border border-slate-300 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                ← Kembali
            </a>
        </div>

        {{-- VALIDATION ERROR --}}
        @if($errors->any())
            <div class="border border-red-300 bg-red-50 text-red-700 rounded-lg p-4 text-sm">
                <p class="font-semibold mb-1">Perencanaan belum dapat disimpan.</p>
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('perencanaan-pembelian.store') }}" method="POST" @submit="prepareSubmit">
            @csrf

            {{-- STEP 1: SUPPLIER & TANGGAL --}}
            <div class="border border-slate-200 rounded-lg bg-white p-4 mb-4">
                <p class="text-xs font-bold text-blue-600 uppercase mb-2">Langkah 1</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label for="supplier_id" class="block text-sm font-bold text-slate-700 mb-1">Supplier <span
                                class="text-red-500">*</span></label>
                        <select id="supplier_id" name="supplier_id" x-model="supplierId" required
                            class="w-full border border-slate-300 rounded-lg px-3 py-3 text-base">
                            <option value="">Semua Barang (belum pilih supplier)</option>
                            @foreach($supplier as $item)
                                <option value="{{ $item->id }}" @selected(old('supplier_id') == $item->id)>
                                    {{ $item->nama_supplier }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-slate-400 mt-1">
                            Pilih ulang opsi "Semua Barang" kapan saja untuk melihat semua stok tanpa refresh halaman.
                        </p>
                    </div>

                    <div>
                        <label for="tanggal_perencanaan" class="block text-sm font-bold text-slate-700 mb-1">Tanggal
                            Perencanaan <span class="text-red-500">*</span></label>
                        <input type="date" id="tanggal_perencanaan" name="tanggal_perencanaan"
                            value="{{ old('tanggal_perencanaan', now()->format('Y-m-d')) }}" required
                            class="w-full border border-slate-300 rounded-lg px-3 py-3 text-base">
                    </div>
                </div>

                <div class="mt-3">
                    <label for="catatan" class="block text-sm font-bold text-slate-700 mb-1">Catatan (opsional)</label>
                    <textarea id="catatan" name="catatan" rows="2"
                        placeholder="Contoh: Restock untuk persediaan bulan depan..."
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">{{ old('catatan') }}</textarea>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-12 gap-4 items-start">

                {{-- LEFT: PILIH BARANG --}}
                <div class="xl:col-span-7 space-y-3">

                    <p class="text-xs font-bold text-blue-600 uppercase">Langkah 2 — Pilih Barang</p>

                    {{-- SEARCH --}}
                    <input type="text" x-model="search" placeholder="Cari barang, SKU, warna, ukuran..."
                        class="w-full border border-slate-300 rounded-lg px-4 py-3.5 text-base focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">

                    {{-- KATEGORI --}}
                    <div class="flex gap-2 overflow-x-auto pb-1">
                        <button type="button" @click="selectedCategory = 'Semua'"
                            :class="selectedCategory === 'Semua' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600'"
                            class="shrink-0 whitespace-nowrap rounded-lg px-4 py-2.5 text-sm font-bold">Semua</button>

                        @php
                            $categories = $varian->map(fn($item) => $item->barang?->kategori?->nama_kategori)->filter()->unique()->values();
                        @endphp

                        @foreach($categories as $category)
                            <button type="button" @click="selectedCategory = @js($category)"
                                :class="selectedCategory === @js($category) ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600'"
                                class="shrink-0 whitespace-nowrap rounded-lg px-4 py-2.5 text-sm font-bold">
                                {{ $category }}
                            </button>
                        @endforeach
                    </div>

                    {{-- KETERANGAN MODE TAMPILAN --}}
                    <div x-show="!supplierId"
                        class="px-4 py-2.5 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-700">
                        Menampilkan semua barang. Pilih supplier untuk mulai menambahkan barang ke rencana pembelian.
                    </div>

                    {{-- PRODUCT LIST --}}
                    <div class="border border-slate-200 rounded-lg bg-white overflow-hidden">

                        <div class="divide-y divide-slate-100 max-h-[60vh] overflow-y-auto">
                            @forelse($varian as $item)
                                @php
                                    $namaBarang = $item->barang?->nama_barang ?? 'Barang';
                                    $kategori = $item->barang?->kategori?->nama_kategori ?? '';
                                    $supplierIdBarang = $item->barang?->supplier_id ?? '';
                                    $warna = $item->warna ?? '-';
                                    $ukuran = $item->ukuran ?? '-';
                                    $sku = $item->sku ?? '-';
                                    $foto = $item->barang?->foto ?? null;
                                    $stokHabis = $item->stok <= 0;
                                    $stokMenipis = $item->stok > 0 && $item->stok <= $item->stok_minimum;
                                @endphp

                                <div x-show="productVisible(@js($namaBarang), @js($sku), @js($warna), @js($ukuran), @js($kategori), @js($supplierIdBarang))"
                                    x-transition class="flex items-center gap-3 px-4 py-3">

                                    <div
                                        class="w-14 h-14 shrink-0 rounded-lg bg-slate-100 overflow-hidden flex items-center justify-center">
                                        @if($foto)
                                            <img src="{{ asset('storage/' . $foto) }}" alt="{{ $namaBarang }}"
                                                class="w-full h-full object-cover">
                                        @else
                                            <span class="text-[10px] text-slate-400 text-center leading-tight">No<br>Foto</span>
                                        @endif
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <p class="font-bold text-slate-900 truncate">{{ $namaBarang }}</p>
                                        <p class="text-sm text-slate-500">{{ $warna }} / {{ $ukuran }} &middot; SKU {{ $sku }}
                                        </p>
                                        @if($stokHabis)
                                            <p class="text-xs font-semibold text-red-600 mt-0.5">Stok Habis</p>
                                        @elseif($stokMenipis)
                                            <p class="text-xs font-semibold text-yellow-600 mt-0.5">Stok Menipis</p>
                                        @else
                                            <p class="text-xs font-semibold text-green-700 mt-0.5">Stok {{ $item->stok }}</p>
                                        @endif
                                    </div>

                                    {{-- supplier sudah dipilih dan cocok: bisa ditambah --}}
                                    <template x-if="supplierId">
                                        <button type="button"
                                            @click="addItem({{ $item->id }}, {{ Js::from($namaBarang) }}, {{ Js::from($warna) }}, {{ Js::from($ukuran) }}, {{ Js::from($sku) }}, {{ (int) $item->stok }})"
                                            class="shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-600 text-white text-xl font-bold">
                                            +
                                        </button>
                                    </template>

                                    <template x-if="!supplierId">
                                        <span class="shrink-0 text-xs text-slate-400 px-2">Hanya Lihat</span>
                                    </template>
                                </div>
                            @empty
                                <div class="text-center py-14 text-slate-500">
                                    Belum ada varian barang.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- RIGHT: RENCANA (CART) --}}
                <div class="xl:col-span-5">
                    <div class="border border-slate-200 rounded-lg bg-white xl:sticky xl:top-4 overflow-hidden">

                        <div class="p-4 border-b border-slate-200 flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">Rencana Pembelian <span
                                        class="text-slate-400 font-normal" x-text="'(' + cart.length + ')'"></span></h2>
                                <p class="text-xs text-slate-500 mt-0.5"
                                    x-text="supplierName ? supplierName : 'Supplier belum dipilih'"></p>
                            </div>
                            <button type="button" x-show="cart.length > 0" @click="clearCart"
                                class="text-sm text-red-500 font-bold">Kosongkan</button>
                        </div>

                        <div x-show="cart.length === 0" class="p-8 text-center text-slate-500">
                            Belum ada barang.<br>Pilih produk di sebelah kiri untuk menambahkannya.
                        </div>

                        <div x-show="cart.length > 0" class="max-h-[40vh] overflow-y-auto divide-y divide-slate-100">
                            <template x-for="(item, index) in cart" :key="item.id">
                                <div class="p-4">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0">
                                            <p class="font-bold text-slate-900" x-text="item.nama"></p>
                                            <p class="text-sm text-slate-500"><span x-text="item.warna"></span> / <span
                                                    x-text="item.ukuran"></span> &middot; SKU <span
                                                    x-text="item.sku"></span></p>
                                        </div>
                                        <button type="button" @click="removeItem(index)"
                                            class="text-red-500 text-2xl leading-none px-1">&times;</button>
                                    </div>

                                    <div class="mt-3 grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="text-xs font-bold text-slate-500 block mb-1">Qty Rencana</label>
                                            <div
                                                class="inline-flex items-center border-2 border-slate-200 rounded-lg overflow-hidden w-full">
                                                <button type="button" @click="decreaseQty(index)"
                                                    class="w-10 h-10 text-xl font-bold hover:bg-slate-100">−</button>
                                                <input type="number" min="1" x-model.number="item.qty"
                                                    class="w-full text-center text-base font-bold outline-none">
                                                <button type="button" @click="increaseQty(index)"
                                                    class="w-10 h-10 text-xl font-bold hover:bg-slate-100">+</button>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="text-xs font-bold text-slate-500 block mb-1">Estimasi /
                                                Unit</label>
                                            <input type="number" min="0" step="1" x-model.number="item.estimasi_harga"
                                                placeholder="0"
                                                class="w-full h-10 border-2 border-slate-200 rounded-lg px-2 text-center text-base font-bold outline-none">
                                        </div>
                                    </div>

                                    <div class="mt-3 flex items-center justify-between bg-slate-50 rounded-lg px-3 py-2">
                                        <span class="text-slate-500 text-xs">Estimasi Subtotal</span>
                                        <span class="font-bold" x-text="rupiah(item.qty * item.estimasi_harga)"></span>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="p-4 border-t border-slate-200 space-y-2">
                            <div
                                class="flex justify-between items-center text-base font-bold text-slate-900 bg-slate-50 rounded-lg px-4 py-3">
                                <span>Estimasi Total</span>
                                <span class="text-2xl text-blue-600" x-text="rupiah(totalEstimasi)"></span>
                            </div>

                            <div class="flex justify-between text-sm text-slate-500 px-1">
                                <span x-text="cart.length + ' varian'"></span>
                                <span x-text="totalQty + ' unit'"></span>
                            </div>

                            <button type="submit" :disabled="cart.length === 0 || !supplierId"
                                :class="cart.length === 0 || !supplierId ? 'bg-slate-300 cursor-not-allowed' : 'bg-green-600'"
                                class="w-full rounded-lg px-4 py-4 text-lg font-bold text-white transition">
                                Simpan Perencanaan
                            </button>

                            <p class="text-center text-xs text-slate-400 mt-1">Menyimpan perencanaan tidak akan menambah
                                stok barang.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- HIDDEN CART INPUT --}}
            <template x-for="(item, index) in cart" :key="'input-' + item.id">
                <div>
                    <input type="hidden" :name="`cart[${index}][varian_id]`" :value="item.id">
                    <input type="hidden" :name="`cart[${index}][qty]`" :value="item.qty">
                    <input type="hidden" :name="`cart[${index}][estimasi_harga]`" :value="item.estimasi_harga">
                </div>
            </template>
        </form>
    </div>

    <script>
        function purchasePlan() {
            return {
                search: '',
                selectedCategory: 'Semua',
                supplierId: @js(old('supplier_id', '')),
                supplierName: '',
                cart: [],

                init() {
                    this.$nextTick(() => { this.updateSupplierName(); });

                    this.$watch('supplierId', () => {
                        this.updateSupplierName();

                        if (this.cart.length > 0) {
                            const konfirmasi = confirm('Mengganti supplier akan mengosongkan barang yang sudah dipilih. Lanjutkan?');
                            if (!konfirmasi) return;
                        }

                        this.cart = [];
                    });
                },

                updateSupplierName() {
                    const select = document.getElementById('supplier_id');
                    if (!select || !select.value) { this.supplierName = ''; return; }
                    this.supplierName = select.options[select.selectedIndex].text;
                },

                // true jika supplier barang berbeda dari supplier yang sedang dipilih
                itemMismatch(supplierBarang) {
                    if (!this.supplierId) return false;
                    return String(supplierBarang) !== String(this.supplierId);
                },

                productVisible(nama, sku, warna, ukuran, kategori, supplierBarang) {
                    const keyword = this.search.toLowerCase().trim();
                    const text = (nama + ' ' + sku + ' ' + warna + ' ' + ukuran).toLowerCase();
                    const matchSearch = keyword === '' || text.includes(keyword);
                    const matchCategory = this.selectedCategory === 'Semua' || kategori === this.selectedCategory;
                    const matchSupplier = this.itemMismatch(supplierBarang) === false;

                    return matchSearch && matchCategory && matchSupplier;
                },

                addItem(id, nama, warna, ukuran, sku, stok) {
                    const existing = this.cart.find(item => Number(item.id) === Number(id));
                    if (existing) { existing.qty = Number(existing.qty) + 1; return; }
                    this.cart.push({ id: Number(id), nama, warna, ukuran, sku, stok: Number(stok), qty: 1, estimasi_harga: 0 });
                },

                removeItem(index) { this.cart.splice(index, 1); },

                clearCart() {
                    if (confirm('Kosongkan semua barang dari perencanaan?')) { this.cart = []; }
                },

                increaseQty(index) { this.cart[index].qty++; },
                decreaseQty(index) { if (this.cart[index].qty > 1) { this.cart[index].qty--; } },

                get totalQty() {
                    return this.cart.reduce((total, item) => total + Number(item.qty || 0), 0);
                },

                get totalEstimasi() {
                    return this.cart.reduce((total, item) => total + (Number(item.qty || 0) * Number(item.estimasi_harga || 0)), 0);
                },

                rupiah(value) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(Number(value || 0));
                },

                prepareSubmit(event) {
                    if (!this.supplierId) { event.preventDefault(); alert('Pilih supplier terlebih dahulu.'); return; }
                    if (this.cart.length === 0) { event.preventDefault(); alert('Tambahkan minimal satu barang ke perencanaan.'); return; }
                    const invalidQty = this.cart.some(item => !item.qty || item.qty < 1);
                    if (invalidQty) { event.preventDefault(); alert('Qty rencana minimal 1.'); }
                },
            };
        }
    </script>
@endsection
