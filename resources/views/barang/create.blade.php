@extends('layouts.app')

@section('content')
<div class="p-4 max-w-lg">

    <h1 class="text-2xl font-bold mb-4">Tambah Barang</h1>

    <div class="border rounded p-4">
        <form action="{{ route('barang.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1">Kategori</label>
                <select name="kategori_id" class="w-full border rounded px-3 py-2 text-sm">
                    @foreach($kategori as $k)
                        <option value="{{ $k->id }}" @selected(old('kategori_id') == $k->id)>{{ $k->nama_kategori }}</option>
                    @endforeach
                </select>
                @error('kategori_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1">Nama Barang</label>
                <input type="text" name="nama_barang" value="{{ old('nama_barang') }}" class="w-full border rounded px-3 py-2 text-sm" required>
                @error('nama_barang')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1">Artikel</label>
                <input type="text" name="artikel" value="{{ old('artikel') }}" class="w-full border rounded px-3 py-2 text-sm">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1">Kode Seri</label>
                <input type="text" name="kode_seri" value="{{ old('kode_seri') }}" class="w-full border rounded px-3 py-2 text-sm">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1">Brand</label>
                <input type="text" name="brand" value="{{ old('brand') }}" class="w-full border rounded px-3 py-2 text-sm">
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
                <textarea name="spesifikasi" rows="4" class="w-full border rounded px-3 py-2 text-sm">{{ old('spesifikasi') }}</textarea>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 text-white rounded px-4 py-2 text-sm font-semibold">Simpan</button>
                <a href="{{ route('barang.index') }}" class="border rounded px-4 py-2 text-sm font-semibold">Kembali</a>
            </div>
        </form>
    </div>

</div>
@endsection