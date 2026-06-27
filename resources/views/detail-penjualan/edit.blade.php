@extends('layouts.app')

@section('content')

<div class="max-w-3xl mx-auto">

    <div class="bg-white p-8 rounded-2xl shadow-lg">

        <h1 class="text-3xl font-bold mb-6">

            Edit Detail Penjualan

        </h1>

        <form action="{{ route('detail-penjualan.update',$detail) }}"
              method="POST">

            @csrf
            @method('PUT')

            <div class="mb-4">

                <label>Penjualan</label>

                <select
                    name="penjualan_id"
                    class="w-full border rounded-lg p-3">

                    @foreach($penjualan as $item)

                        <option
                            value="{{ $item->id }}"
                            {{ $detail->penjualan_id == $item->id ? 'selected' : '' }}>

                            {{ $item->no_nota }}

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

                        <option
                            value="{{ $item->id }}"
                            {{ $detail->varian_id == $item->id ? 'selected' : '' }}>

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
                    value="{{ $detail->qty }}"
                    class="w-full border rounded-lg p-3">

            </div>

            <div class="mb-4">

                <label>Harga</label>

                <input
                    type="number"
                    name="harga"
                    value="{{ $detail->harga }}"
                    class="w-full border rounded-lg p-3">

            </div>

            <button
                class="bg-blue-600 text-white px-5 py-3 rounded-xl">

                Update

            </button>

        </form>

    </div>

</div>

@endsection