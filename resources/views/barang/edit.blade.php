@extends('layouts.app')

@section('content')

<div class="max-w-3xl mx-auto bg-white p-8 rounded-2xl shadow">

    <h1 class="text-3xl font-bold mb-6">

        Edit Barang

    </h1>

    <form action="{{ route('barang.update', $barang) }}"
          method="POST">

        @csrf
        @method('PUT')

        <div class="mb-4">

            <label class="block mb-2 font-medium">

                Kategori

            </label>

            <select
                name="kategori_id"
                class="w-full border rounded-lg p-3">

                @foreach($kategori as $k)

                    <option
                        value="{{ $k->id }}"
                        {{ $barang->kategori_id == $k->id ? 'selected' : '' }}>

                        {{ $k->nama_kategori }}

                    </option>

                @endforeach

            </select>

        </div>

        <div class="mb-4">

            <label class="block mb-2 font-medium">

                Nama Barang

            </label>

            <input
                type="text"
                name="nama_barang"
                value="{{ $barang->nama_barang }}"
                class="w-full border rounded-lg p-3">

        </div>

        <div class="mb-4">

            <label class="block mb-2 font-medium">

                Artikel

            </label>

            <input
                type="text"
                name="artikel"
                value="{{ $barang->artikel }}"
                class="w-full border rounded-lg p-3">

        </div>

        <div class="mb-4">

            <label class="block mb-2 font-medium">

                Kode Seri

            </label>

            <input
                type="text"
                name="kode_seri"
                value="{{ $barang->kode_seri }}"
                class="w-full border rounded-lg p-3">

        </div>

        <div class="mb-4">

            <label class="block mb-2 font-medium">

                Brand

            </label>

            <input
                type="text"
                name="brand"
                value="{{ $barang->brand }}"
                class="w-full border rounded-lg p-3">

        </div>

        <div class="mb-4">

            <label class="block mb-2 font-medium">

                Spesifikasi

            </label>

            <textarea
                name="spesifikasi"
                rows="4"
                class="w-full border rounded-lg p-3">{{ $barang->spesifikasi }}</textarea>

        </div>

        <div class="flex gap-3">

            <a href="{{ route('barang.index') }}"
               class="bg-gray-500 text-white px-5 py-3 rounded-xl">

                Kembali

            </a>

            <button
                type="submit"
                class="bg-blue-600 text-white px-5 py-3 rounded-xl">

                Update

            </button>

        </div>

    </form>

</div>

@endsection