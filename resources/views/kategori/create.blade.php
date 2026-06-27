@extends('layouts.app')

@section('content')

<div class="max-w-xl">

    <h1 class="text-3xl font-bold mb-6">
        Tambah Kategori
    </h1>

    <div class="bg-white shadow rounded-xl p-6">

        <form action="{{ route('kategori.store') }}" method="POST">

            @csrf

            <div class="mb-5">

                <label class="block font-semibold mb-2">
                    Nama Kategori
                </label>

                <input
                    type="text"
                    name="nama_kategori"
                    value="{{ old('nama_kategori') }}"
                    class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500"
                    required>

                @error('nama_kategori')

                <p class="text-red-500 mt-2">
                    {{ $message }}
                </p>

                @enderror

            </div>

            <div class="flex gap-3">

                <button
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg">

                    Simpan

                </button>

                <a href="{{ route('kategori.index') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-2 rounded-lg">

                    Kembali

                </a>

            </div>

        </form>

    </div>

</div>

@endsection