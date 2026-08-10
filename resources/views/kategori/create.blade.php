@extends('layouts.app')

@section('content')
<div class="p-4 max-w-lg">

    <h1 class="text-2xl font-bold mb-4">Tambah Kategori</h1>

    <div class="border rounded p-4">
        <form action="{{ route('kategori.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1">Nama Kategori</label>
                <input type="text" name="nama_kategori" value="{{ old('nama_kategori') }}"
                    class="w-full border rounded px-3 py-2 text-sm" required>

                @error('nama_kategori')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1">Kode Kategori</label>
                <input type="text" name="kode" value="{{ old('kode') }}" maxlength="10"
                    class="w-full border rounded px-3 py-2 text-sm uppercase" placeholder="Contoh: JH" required>
                <p class="text-xs text-gray-400 mt-1">
                    Kode singkat (maks 10 karakter), dipakai untuk membentuk Kode Barang otomatis. Contoh: "Jas Hujan" &rarr; JH.
                </p>

                @error('kode')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 text-white rounded px-4 py-2 text-sm font-semibold">Simpan</button>
                <a href="{{ route('kategori.index') }}" class="border rounded px-4 py-2 text-sm font-semibold">Kembali</a>
            </div>
        </form>
    </div>

</div>
@endsection