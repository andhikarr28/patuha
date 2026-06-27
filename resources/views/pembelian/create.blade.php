@extends('layouts.app')

@section('content')

<div class="max-w-3xl mx-auto">

    <div class="bg-white p-8 rounded-2xl shadow-lg">

        <h1 class="text-3xl font-bold mb-6">

            Tambah Pembelian

        </h1>

        <form action="{{ route('pembelian.store') }}"
              method="POST">

            @csrf

            <div class="mb-4">

                <label>No Faktur</label>

                <input
                    type="text"
                    name="no_faktur"
                    class="w-full border rounded-lg p-3">

            </div>

            <div class="mb-4">

                <label>Tanggal Pembelian</label>

                <input
                    type="date"
                    name="tanggal_pembelian"
                    class="w-full border rounded-lg p-3">

            </div>

            <div class="mb-4">

                <label>Supplier</label>

                <select
                    name="supplier_id"
                    class="w-full border rounded-lg p-3">

                    @foreach($supplier as $item)

                        <option value="{{ $item->id }}">
                            {{ $item->nama_supplier }}
                        </option>

                    @endforeach

                </select>

            </div>

            <button
                class="bg-blue-600 text-white px-5 py-3 rounded-xl">

                Simpan

            </button>

        </form>

    </div>

</div>

@endsection