@extends('layouts.app')

@section('content')

<div class="max-w-xl">

    <h1 class="text-3xl font-bold mb-6">
        Edit Kategori
    </h1>

    <div class="bg-white shadow rounded-xl p-6">

        <form action="{{ route('kategori.update',$kategori) }}" method="POST">

            @csrf
            @method('PUT')

            <div class="mb-5">

                <label class="block font-semibold mb-2">
                    Nama Kategori
                </label>

                <input
                    type="text"
                    name="nama_kategori"
                    value="{{ old('nama_kategori',$kategori->nama_kategori) }}"
                    class="w-full border rounded-lg px-4 py-2"
                    required>

            </div>

            <div class="flex gap-3">

                <button
                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-5 py-2 rounded-lg">

                    Update

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