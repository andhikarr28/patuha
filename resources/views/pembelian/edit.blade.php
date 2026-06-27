@extends('layouts.app')

@section('content')

<div class="max-w-3xl mx-auto">

    <div class="bg-white p-8 rounded-2xl shadow-lg">

        <h1 class="text-3xl font-bold mb-6">

            Edit Pembelian

        </h1>

        <form action="{{ route('pembelian.update', $pembelian) }}"
              method="POST">

            @csrf
            @method('PUT')

            <div class="mb-4">

                <label class="block mb-2">

                    No Faktur

                </label>

                <input
                    type="text"
                    name="no_faktur"
                    value="{{ $pembelian->no_faktur }}"
                    class="w-full border rounded-lg p-3">

            </div>

            <div class="mb-4">

                <label class="block mb-2">

                    Tanggal Pembelian

                </label>

                <input
                    type="date"
                    name="tanggal_pembelian"
                    value="{{ $pembelian->tanggal_pembelian }}"
                    class="w-full border rounded-lg p-3">

            </div>

            <div class="mb-4">

                <label class="block mb-2">

                    Supplier

                </label>

                <select
                    name="supplier_id"
                    class="w-full border rounded-lg p-3">

                    @foreach($supplier as $item)

                        <option
                            value="{{ $item->id }}"
                            {{ $item->id == $pembelian->supplier_id ? 'selected' : '' }}>

                            {{ $item->nama_supplier }}

                        </option>

                    @endforeach

                </select>

            </div>

            <div class="flex gap-3">

                <a href="{{ route('pembelian.index') }}"
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

</div>

@endsection