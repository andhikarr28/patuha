@extends('layouts.app')

@section('content')

<div class="max-w-3xl mx-auto">

    <div class="bg-white p-8 rounded-2xl shadow-lg">

        <h1 class="text-3xl font-bold mb-6">

            Tambah Detail Pembelian

        </h1>

        <form action="{{ route('detail-pembelian.store') }}"
              method="POST">

            @csrf

            <div class="mb-4">

                <label>Pembelian</label>

                <select
                    name="pembelian_id"
                    class="w-full border rounded-lg p-3">

                    @foreach($pembelian as $item)

                        <option value="{{ $item->id }}">

                            {{ $item->no_faktur }}

                        </option>

                    @endforeach

                </select>

            </div>

            <div class="mb-4">

                <label>Varian Barang</label>

                <select
                    name="varian_id"
                    class="w-full border rounded-lg p-3">

                    @foreach($varian as $item)

                        <option value="{{ $item->id }}">

                            {{ $item->barang->nama_barang }}
                            -
                            {{ $item->warna }}
                            -
                            {{ $item->ukuran }}

                        </option>

                    @endforeach

                </select>

            </div>

            <div class="mb-4">

                <label>Qty</label>

                <input
                    type="number"
                    name="qty"
                    class="w-full border rounded-lg p-3">

            </div>

            <div class="mb-4">

                <label>Harga Satuan</label>

                <input
                    type="number"
                    name="harga_satuan"
                    class="w-full border rounded-lg p-3">

            </div>

            <div class="mb-4">

                <label>Diskon</label>

                <input
                    type="number"
                    name="diskon"
                    value="0"
                    class="w-full border rounded-lg p-3">

            </div>

            <button
                class="bg-blue-600 text-white px-5 py-3 rounded-xl">

                Simpan

            </button>

        </form>

    </div>

</div>

@endsection