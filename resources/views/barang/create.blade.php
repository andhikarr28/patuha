@extends('layouts.app')

@section('content')
    <div class="p-4 max-w-lg">

        <h1 class="text-2xl font-bold mb-4">Tambah Barang</h1>

        <div class="border rounded p-4">
            <form action="{{ route('barang.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-1">Kategori</label>
                    <select name="kategori_id" id="kategori_id" onchange="updatePreview()"
                        class="w-full border rounded px-3 py-2 text-sm">
                        <option value="">Pilih Kategori</option>
                        @foreach($kategori as $k)
                            <option value="{{ $k->id }}"
                                data-kode="{{ $k->kode ?? '' }}"
                                @selected(old('kategori_id') == $k->id)>
                                {{ $k->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                    @error('kategori_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-1">Supplier</label>
                    <select name="supplier_id" class="w-full border rounded px-3 py-2 text-sm" required>
                        <option value="">Pilih Supplier</option>
                        @foreach($supplier as $s)
                            <option value="{{ $s->id }}" {{ old('supplier_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->nama_supplier }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-1">Nama Barang</label>
                    <input type="text" name="nama_barang" value="{{ old('nama_barang') }}"
                        class="w-full border rounded px-3 py-2 text-sm" required>
                    @error('nama_barang')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-1">Artikel</label>
                    <input type="text" name="artikel" id="artikel" value="{{ old('artikel') }}"
                        oninput="updatePreview()"
                        class="w-full border rounded px-3 py-2 text-sm">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-1">Brand</label>
                    <input type="text" name="brand" id="brand" value="{{ old('brand') }}"
                        oninput="updatePreview()"
                        class="w-full border rounded px-3 py-2 text-sm">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-1">Kode Seri</label>
                    <input type="text" name="kode_seri" id="kode_seri" value="{{ old('kode_seri') }}"
                        oninput="updatePreview()"
                        placeholder="Contoh: 23"
                        class="w-full border rounded px-3 py-2 text-sm" required>
                    <p class="text-xs text-gray-400 mt-1">Diisi manual, bebas — bisa angka atau kode apapun.</p>
                    @error('kode_seri')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4 bg-gray-50 border rounded px-3 py-2">
                    <p class="text-xs font-semibold text-gray-700 mb-1">Preview Kode Barang:</p>
                    <p id="kode_preview" class="font-mono text-sm text-gray-500">
                        Pilih kategori & isi kode seri...
                    </p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-1">Foto</label>
                    <input type="file" name="foto" accept="image/*" class="w-full border rounded px-3 py-2 text-sm">
                    @error('foto')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-1">Spesifikasi</label>
                    <textarea name="spesifikasi" rows="4"
                        class="w-full border rounded px-3 py-2 text-sm">{{ old('spesifikasi') }}</textarea>
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                        class="bg-blue-600 text-white rounded px-4 py-2 text-sm font-semibold">Simpan</button>
                    <a href="{{ route('barang.index') }}" class="border rounded px-4 py-2 text-sm font-semibold">Kembali</a>
                </div>
            </form>
        </div>

    </div>

    <script>
        function singkat(teks) {
            teks = teks.trim();
            if (teks === '') return 'XXX';

            const kata = teks.split(/\s+/);

            if (kata.length === 1) {
                return teks.substring(0, 3).toUpperCase();
            }

            let hasil = kata.map(k => k[0]).join('');
            if (hasil.length < 3) {
                hasil += kata[kata.length - 1].slice(-1);
            }

            return hasil.substring(0, 3).toUpperCase();
        }

        function updatePreview() {
            const kategoriSelect = document.getElementById('kategori_id');
            const selectedOption = kategoriSelect.options[kategoriSelect.selectedIndex];
            const kodeKategori = selectedOption ? (selectedOption.getAttribute('data-kode') || '') : '';

            const brand = document.getElementById('brand').value;
            const artikel = document.getElementById('artikel').value;
            const kodeSeri = document.getElementById('kode_seri').value.trim();

            const previewEl = document.getElementById('kode_preview');

            if (!kodeKategori) {
                previewEl.textContent = 'Pilih kategori terlebih dahulu...';
                return;
            }

            if (!kodeSeri) {
                previewEl.textContent = 'Isi Kode Seri untuk melihat preview lengkap...';
                return;
            }

            const kodeBrand = singkat(brand);
            const kodeArtikel = singkat(artikel);

            previewEl.textContent = `${kodeKategori}-${kodeBrand}-${kodeArtikel}-${kodeSeri}`.toUpperCase();
        }

        document.addEventListener('DOMContentLoaded', updatePreview);
    </script>
@endsection