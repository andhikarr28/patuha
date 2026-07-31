@extends('layouts.app')

@section('content')
<div class="p-4 max-w-lg">

    <h1 class="text-2xl font-bold mb-4">Edit Kategori</h1>

    <div class="border rounded p-4">
        <form action="{{ route('kategori.update', $kategori) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1">Nama Kategori</label>
                <input type="text" name="nama_kategori" value="{{ old('nama_kategori', $kategori->nama_kategori) }}"
                    class="w-full border rounded px-3 py-2 text-sm" required>

                @error('nama_kategori')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 text-white rounded px-4 py-2 text-sm font-semibold">Update</button>
                <a href="{{ route('kategori.index') }}" class="border rounded px-4 py-2 text-sm font-semibold">Kembali</a>
            </div>
        </form>
    </div>

</div>
@endsection