@extends('layouts.app')

@section('content')

<div class="max-w-3xl mx-auto">

    <div class="bg-white p-8 rounded-2xl shadow-lg">

        <h1 class="text-3xl font-bold mb-6">

            Tambah Supplier

        </h1>

        <form action="{{ route('supplier.store') }}"
              method="POST">

            @csrf

            <div class="mb-4">

                <label>Nama Supplier</label>

                <input
                    type="text"
                    name="nama_supplier"
                    class="w-full border rounded-lg p-3">

            </div>

            <div class="mb-4">

                <label>No HP</label>

                <input
                    type="text"
                    name="no_hp"
                    class="w-full border rounded-lg p-3">

            </div>

            <div class="mb-4">

                <label>Alamat</label>

                <textarea
                    name="alamat"
                    class="w-full border rounded-lg p-3"></textarea>

            </div>

            <button
                class="bg-blue-600 text-white px-5 py-3 rounded-xl">

                Simpan

            </button>

        </form>

    </div>

</div>

@endsection