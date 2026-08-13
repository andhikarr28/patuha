@extends('layouts.app')

@section('content')

    <div class="max-w-5xl mx-auto">

        {{-- HEADER --}}
        <div class="mb-6">

            @if($selectedBarang)

                <a href="{{ route('barang.show', $selectedBarang->id) }}"
                    class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-blue-600 mb-3">

                    ← Kembali ke Detail Barang

                </a>

            @else

                <a href="{{ route('barang.index') }}"
                    class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-blue-600 mb-3">

                    ← Kembali ke Master Barang

                </a>

            @endif

            <h1 class="text-3xl font-bold text-slate-900">
                Tambah Varian Barang
            </h1>

            <p class="text-slate-500 mt-1">
                Tambahkan kombinasi warna dan ukuran. SKU akan dibuat otomatis oleh sistem.
            </p>

        </div>


        {{-- ERROR VALIDATION --}}
        @if ($errors->any())

            <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">

                <p class="font-semibold text-red-700 mb-2">
                    Data belum dapat disimpan.
                </p>

                <ul class="list-disc list-inside text-sm text-red-600 space-y-1">

                    @foreach ($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        @endif


        <form action="{{ route('varian.store') }}" method="POST">

            @csrf


            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

                {{-- HEADER CARD --}}
                <div class="px-7 py-5 border-b border-slate-200">

                    <h2 class="text-xl font-bold text-slate-900">
                        Informasi Varian
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        Lengkapi informasi varian produk di bawah ini.
                    </p>

                </div>


                <div class="p-7 space-y-7">

                    {{-- BARANG --}}
                    <div>

                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Barang
                        </label>

                        @if($selectedBarang)

                            {{-- ID BARANG TETAP DIKIRIM --}}
                            <input type="hidden" name="barang_id" id="barang_id" value="{{ $selectedBarang->id }}">

                            {{-- KODE BARANG UNTUK PREVIEW SKU DI JS --}}
                            <input type="hidden" id="kode_barang_barang"
                                value="{{ $selectedBarang->kode_barang ?? 'BRG' . $selectedBarang->id }}">

                            <div class="flex items-center gap-4
                                                            bg-slate-50
                                                            border border-slate-200
                                                            rounded-xl
                                                            px-5 py-4">

                                <div class="w-12 h-12
                                                                rounded-xl
                                                                bg-blue-100
                                                                flex items-center
                                                                justify-center
                                                                text-xl">

                                    📦

                                </div>

                                <div>

                                    <p class="font-bold text-slate-900">
                                        {{ $selectedBarang->nama_barang }}
                                    </p>

                                    <p class="text-sm text-slate-500 mt-1">
                                        Barang dipilih otomatis dari Master Barang.
                                    </p>

                                </div>

                            </div>

                        @else

                            <select name="barang_id" id="barang_id" required onchange="updateKodeBarang(this)" class="w-full border border-slate-300
                                                               rounded-xl px-4 py-3
                                                               focus:outline-none
                                                               focus:ring-2
                                                               focus:ring-blue-500
                                                               focus:border-blue-500">

                                <option value="">
                                    Pilih Barang
                                </option>

                                @foreach($barang as $item)

                                    <option value="{{ $item->id }}" data-kode-barang="{{ $item->kode_barang ?? 'BRG' . $item->id }}"
                                        {{ old('barang_id') == $item->id ? 'selected' : '' }}>

                                        {{ $item->nama_barang }}

                                    </option>

                                @endforeach

                            </select>

                            <input type="hidden" id="kode_barang_barang" value="">

                        @endif

                        @error('barang_id')

                            <p class="text-red-500 text-sm mt-1">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- WARNA DAN UKURAN --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <div>

                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Warna
                            </label>

                            <input type="text" name="warna" id="warna" value="{{ old('warna') }}"
                                placeholder="Contoh: Hitam" required oninput="updateSkuPreview()" class="w-full border border-slate-300
                                                  rounded-xl px-4 py-3
                                                  focus:outline-none
                                                  focus:ring-2
                                                  focus:ring-blue-500">

                            @error('warna')

                                <p class="text-red-500 text-sm mt-1">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>


                        <div>

                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Ukuran
                            </label>

                            <input type="text" name="ukuran" id="ukuran" value="{{ old('ukuran') }}"
                                placeholder="Contoh: XL, 40, 50L" required oninput="updateSkuPreview()" class="w-full border border-slate-300
                                                  rounded-xl px-4 py-3
                                                  focus:outline-none
                                                  focus:ring-2
                                                  focus:ring-blue-500">

                            @error('ukuran')

                                <p class="text-red-500 text-sm mt-1">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>

                    </div>


                    {{-- SKU (AUTO, READ ONLY) --}}
                    <div>

                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            SKU (otomatis)
                        </label>

                        <input type="text" id="sku_preview" readonly
                            placeholder="Akan terisi otomatis setelah warna & ukuran diisi..." class="w-full border border-slate-300
                                              rounded-xl px-4 py-3
                                              bg-slate-100
                                              text-slate-600
                                              cursor-not-allowed
                                              focus:outline-none">

                        <p class="text-xs text-slate-400 mt-2">
                            SKU dibuat otomatis dari kode barang + warna + ukuran. Kode final tetap divalidasi ulang oleh
                            sistem saat disimpan.
                        </p>

                        @error('sku')

                            <p class="text-red-500 text-sm mt-1">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- HARGA & MARGIN --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <div>

                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Harga Beli
                            </label>

                            <div class="relative">

                                <span class="absolute left-4 top-1/2 -translate-y-1/2
                                     text-slate-500 font-medium">
                                    Rp
                                </span>

                                <input type="number" name="harga_beli" id="harga_beli" value="{{ old('harga_beli', 0) }}"
                                    min="0" required oninput="updateHargaJualPreview()" class="w-full border border-slate-300
                                      rounded-xl
                                      pl-12 pr-4 py-3
                                      focus:outline-none
                                      focus:ring-2
                                      focus:ring-blue-500">

                            </div>

                            @error('harga_beli')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror

                        </div>


                        <div>

                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Margin (%)
                            </label>

                            <div class="relative">

                                <input type="number" name="margin_persen" id="margin_persen"
                                    value="{{ old('margin_persen', 0) }}" min="0" step="0.1" required
                                    oninput="updateHargaJualPreview()" class="w-full border border-slate-300
                                      rounded-xl
                                      pl-4 pr-12 py-3
                                      focus:outline-none
                                      focus:ring-2
                                      focus:ring-blue-500">

                                <span class="absolute right-4 top-1/2 -translate-y-1/2
                                     text-slate-500 font-medium">
                                    %
                                </span>

                            </div>

                            @error('margin_persen')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror

                        </div>

                    </div>

                    {{-- PREVIEW HARGA JUAL (OTOMATIS, TIDAK DIKIRIM SEBAGAI INPUT) --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-xl px-5 py-4 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-blue-700">Harga Jual (otomatis)</p>
                            <p class="text-xs text-blue-500 mt-0.5">Dihitung dari Harga Beli + Margin</p>
                        </div>
                        <span id="harga_jual_preview" class="text-2xl font-bold text-blue-700">Rp 0</span>
                    </div>

                    {{-- STOK --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <div>

                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Stok Awal
                            </label>

                            <input type="hidden" name="stok" value="0">

                            <div class="w-full
                                                bg-slate-100
                                                border border-slate-200
                                                rounded-xl
                                                px-4 py-3
                                                text-slate-600">

                                0 unit

                            </div>

                            <p class="text-xs text-slate-400 mt-2">
                                Stok akan bertambah melalui proses penerimaan barang.
                            </p>

                        </div>


                        <div>

                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Stok Minimum
                            </label>

                            <input type="number" name="stok_minimum" value="{{ old('stok_minimum', 5) }}" min="0" required
                                class="w-full border border-slate-300
                                                  rounded-xl px-4 py-3
                                                  focus:outline-none
                                                  focus:ring-2
                                                  focus:ring-blue-500">

                            <p class="text-xs text-slate-400 mt-2">
                                Digunakan sebagai batas peringatan stok menipis.
                            </p>

                        </div>

                    </div>

                </div>


                {{-- FOOTER --}}
                <div class="px-7 py-5
                                    bg-slate-50
                                    border-t border-slate-200
                                    flex justify-end gap-3">

                    @if($selectedBarang)

                        <a href="{{ route('barang.show', $selectedBarang->id) }}" class="px-5 py-3
                                                      border border-slate-300
                                                      text-slate-700
                                                      font-semibold
                                                      rounded-xl
                                                      hover:bg-slate-100">

                            Batal

                        </a>

                    @else

                        <a href="{{ route('barang.index') }}" class="px-5 py-3
                                                      border border-slate-300
                                                      text-slate-700
                                                      font-semibold
                                                      rounded-xl
                                                      hover:bg-slate-100">

                            Batal

                        </a>

                    @endif


                    <button type="submit" class="px-6 py-3
                                           bg-blue-600
                                           hover:bg-blue-700
                                           text-white
                                           font-semibold
                                           rounded-xl
                                           transition">

                        Simpan Varian

                    </button>

                </div>

            </div>

        </form>

    </div>


    <script>
        function singkatWarna(warna) {
            warna = warna.trim();
            if (warna.length === 0) return '';

            const kata = warna.split(/\s+/);

            if (kata.length === 1) {
                return warna.substring(0, 3).toUpperCase();
            }

            return (kata[0][0] + kata[1][0] + kata[1].slice(-1)).toUpperCase();
        }

        function updateSkuPreview() {
            const kodeBarang = document.getElementById('kode_barang_barang').value;
            const warna = document.getElementById('warna').value;
            const ukuran = document.getElementById('ukuran').value.replace(/\s+/g, '');

            const previewEl = document.getElementById('sku_preview');

            if (!kodeBarang || !warna || !ukuran) {
                previewEl.value = '';
                return;
            }

            const sku = `${kodeBarang}-${singkatWarna(warna)}-${ukuran}`.toUpperCase();
            previewEl.value = sku;
        }

        function updateKodeBarang(selectEl) {
            const selectedOption = selectEl.options[selectEl.selectedIndex];
            const kodeBarang = selectedOption.getAttribute('data-kode-barang') || '';

            document.getElementById('kode_barang_barang').value = kodeBarang;
            updateSkuPreview();
        }

        function updateHargaJualPreview() {
            const hargaBeli = Number(document.getElementById('harga_beli').value) || 0;
            const margin = Number(document.getElementById('margin_persen').value) || 0;
            const hargaJual = Math.round(hargaBeli + (hargaBeli * margin / 100));

            document.getElementById('harga_jual_preview').textContent =
                'Rp ' + hargaJual.toLocaleString('id-ID');
        }

        // Kalau ada old('barang_id') dari validasi gagal, isi ulang kode_barang-nya
        document.addEventListener('DOMContentLoaded', function () {
            const selectEl = document.getElementById('barang_id');

            if (selectEl && selectEl.tagName === 'SELECT' && selectEl.value) {
                updateKodeBarang(selectEl);
            }

            updateSkuPreview();
            updateHargaJualPreview(); // tambahan ini
        });
    </script>

@endsection