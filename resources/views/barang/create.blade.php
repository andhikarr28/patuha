@extends('layouts.app')

@section('content')

    <div class="max-w-3xl mx-auto bg-white p-8 rounded-2xl shadow">

        <h1 class="text-3xl font-bold mb-6">

            Tambah Barang

        </h1>

        <form action="{{ route('barang.store') }}" method="POST">

            @csrf

            <div class="mb-4">

                <label>Kategori</label>

                <select name="kategori_id" class="w-full border rounded-lg p-3">

                    @foreach($kategori as $k)

                        <option value="{{ $k->id }}">
                            {{ $k->nama_kategori }}
                        </option>

                    @endforeach

                </select>

            </div>

            <div class="mb-4">

                <label>Nama Barang</label>

                <input type="text" name="nama_barang" class="w-full border rounded-lg p-3">

            </div>

            <div class="mb-4">

                <label>Artikel</label>

                <input type="text" name="artikel" class="w-full border rounded-lg p-3">

            </div>

            <div class="mb-4">

                <label>Kode Seri</label>

                <input type="text" name="kode_seri" class="w-full border rounded-lg p-3">

            </div>

            <div class="mb-4">

                <label>Brand</label>

                <input type="text" name="brand" class="w-full border rounded-lg p-3">

            </div>

            <div class="mb-4">

                <label>Spesifikasi</label>

                <textarea name="spesifikasi" rows="4" class="w-full border rounded-lg p-3"></textarea>

            </div>

            <button class="bg-blue-600 text-white px-5 py-3 rounded-xl">

                Simpan

            </button>

        </form>

    </div>

@endsection