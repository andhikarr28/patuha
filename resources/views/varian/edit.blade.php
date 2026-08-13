@extends('layouts.app')

@section('content')

    <div class="max-w-5xl mx-auto">

        {{-- HEADER --}}
        <div class="mb-6">

            <a href="{{ route('barang.show', $varian->barang_id) }}" class="inline-flex items-center gap-2
                          text-sm font-medium
                          text-slate-500
                          hover:text-blue-600
                          mb-3">

                ← Kembali ke Detail Barang

            </a>

            <h1 class="text-3xl font-bold text-slate-900">
                Edit Varian Barang
            </h1>

            <p class="text-slate-500 mt-1">
                Perbarui informasi varian produk.
            </p>

        </div>


        {{-- ERROR --}}
        @if ($errors->any())

            <div class="mb-6
                                    bg-red-50
                                    border border-red-200
                                    rounded-xl p-4">

                <p class="font-semibold text-red-700 mb-2">
                    Data belum dapat diperbarui.
                </p>

                <ul class="list-disc list-inside
                                       text-sm text-red-600
                                       space-y-1">

                    @foreach ($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        @endif


        <form action="{{ route('varian.update', $varian) }}" method="POST">

            @csrf
            @method('PUT')


            <div class="bg-white
                            border border-slate-200
                            rounded-2xl
                            shadow-sm
                            overflow-hidden">


                {{-- HEADER CARD --}}
                <div class="px-7 py-5 border-b border-slate-200">

                    <h2 class="text-xl font-bold text-slate-900">
                        Informasi Varian
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        Edit atribut dan harga varian produk.
                    </p>

                </div>


                <div class="p-7 space-y-7">


                    {{-- BARANG INDUK --}}
                    <div>

                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Barang
                        </label>

                        <input type="hidden" name="barang_id" value="{{ $varian->barang_id }}">

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

                                    {{ $varian->barang->nama_barang }}

                                </p>

                                <p class="text-sm text-slate-500 mt-1">
                                    Barang induk tidak dapat diubah dari halaman edit varian.
                                </p>

                            </div>

                        </div>

                    </div>


                    {{-- WARNA UKURAN --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <div>

                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Warna
                            </label>

                            <input type="text" name="warna" value="{{ old('warna', $varian->warna) }}"
                                placeholder="Contoh: Hitam" class="w-full
                                              border border-slate-300
                                              rounded-xl px-4 py-3
                                              focus:outline-none
                                              focus:ring-2
                                              focus:ring-blue-500">

                        </div>


                        <div>

                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Ukuran
                            </label>

                            <input type="text" name="ukuran" value="{{ old('ukuran', $varian->ukuran) }}"
                                placeholder="Contoh: XL, 40, 50L" class="w-full
                                              border border-slate-300
                                              rounded-xl px-4 py-3
                                              focus:outline-none
                                              focus:ring-2
                                              focus:ring-blue-500">

                        </div>

                    </div>


                    {{-- SKU --}}
                    <div>

                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            SKU
                        </label>

                        <input type="text" name="sku" value="{{ old('sku', $varian->sku) }}" class="w-full
                                          border border-slate-300
                                          rounded-xl px-4 py-3
                                          focus:outline-none
                                          focus:ring-2
                                          focus:ring-blue-500">

                        <p class="text-xs text-slate-400 mt-2">
                            SKU harus unik untuk setiap varian.
                        </p>

                    </div>


                    {{-- HARGA & MARGIN --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <div>

                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Harga Beli
                            </label>

                            <div class="relative">

                                <span class="absolute
                                 left-4 top-1/2
                                 -translate-y-1/2
                                 text-slate-500
                                 font-medium">

                                    Rp

                                </span>

                                <input type="number" name="harga_beli" id="harga_beli"
                                    value="{{ old('harga_beli', $varian->harga_beli) }}" min="0" required
                                    oninput="updateHargaJualPreview()" class="w-full
                                  border border-slate-300
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
                                    value="{{ old('margin_persen', $varian->margin_persen) }}" min="0" step="0.1" required
                                    oninput="updateHargaJualPreview()" class="w-full
                                  border border-slate-300
                                  rounded-xl
                                  pl-4 pr-12 py-3
                                  focus:outline-none
                                  focus:ring-2
                                  focus:ring-blue-500">

                                <span class="absolute
                                 right-4 top-1/2
                                 -translate-y-1/2
                                 text-slate-500
                                 font-medium">

                                    %

                                </span>

                            </div>

                            @error('margin_persen')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror

                        </div>

                    </div>

                    {{-- PREVIEW HARGA JUAL (OTOMATIS) --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-xl px-5 py-4 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-blue-700">Harga Jual (otomatis)</p>
                            <p class="text-xs text-blue-500 mt-0.5">Harga saat ini: Rp
                                {{ number_format($varian->harga_jual, 0, ',', '.') }}
                            </p>
                        </div>
                        <span id="harga_jual_preview" class="text-2xl font-bold text-blue-700">Rp 0</span>
                    </div>


                    {{-- STOK --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <div>

                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Stok Saat Ini
                            </label>

                            {{-- supaya controller lama yang masih
                            memvalidasi stok tetap menerima nilainya --}}
                            <input type="hidden" name="stok" value="{{ $varian->stok }}">

                            <div class="flex items-center justify-between
                                            bg-slate-50
                                            border border-slate-200
                                            rounded-xl
                                            px-5 py-4">

                                <div>

                                    <p class="text-2xl font-bold text-slate-900">
                                        {{ $varian->stok }}
                                    </p>

                                    <p class="text-sm text-slate-500">
                                        unit tersedia
                                    </p>

                                </div>

                                <div class="text-2xl">
                                    📦
                                </div>

                            </div>

                            <p class="text-xs text-slate-400 mt-2">
                                Stok tidak diedit manual. Perubahan stok dilakukan melalui transaksi penerimaan dan
                                penjualan.
                            </p>

                        </div>


                        <div>

                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Stok Minimum
                            </label>

                            <input type="number" name="stok_minimum"
                                value="{{ old('stok_minimum', $varian->stok_minimum) }}" min="0" required class="w-full
                                              border border-slate-300
                                              rounded-xl px-4 py-3
                                              focus:outline-none
                                              focus:ring-2
                                              focus:ring-blue-500">

                            <p class="text-xs text-slate-400 mt-2">
                                Sistem menggunakan nilai ini untuk mendeteksi stok menipis.
                            </p>

                        </div>

                    </div>

                </div>


                {{-- FOOTER --}}
                <div class="px-7 py-5
                                bg-slate-50
                                border-t border-slate-200
                                flex justify-end gap-3">

                    <a href="{{ route('barang.show', $varian->barang_id) }}" class="px-5 py-3
                                  border border-slate-300
                                  text-slate-700
                                  font-semibold
                                  rounded-xl
                                  hover:bg-slate-100">

                        Batal

                    </a>


                    <button type="submit" class="px-6 py-3
                                       bg-blue-600
                                       hover:bg-blue-700
                                       text-white
                                       font-semibold
                                       rounded-xl
                                       transition">

                        Simpan Perubahan

                    </button>

                </div>

            </div>

        </form>

    </div>
    <script>
        function updateHargaJualPreview() {
            const hargaBeli = Number(document.getElementById('harga_beli').value) || 0;
            const margin = Number(document.getElementById('margin_persen').value) || 0;
            const hargaJual = Math.round(hargaBeli + (hargaBeli * margin / 100));

            document.getElementById('harga_jual_preview').textContent =
                'Rp ' + hargaJual.toLocaleString('id-ID');
        }

        document.addEventListener('DOMContentLoaded', updateHargaJualPreview);
    </script>

@endsection