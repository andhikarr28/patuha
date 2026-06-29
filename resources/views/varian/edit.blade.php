@extends('layouts.app')

@section('content')

    <div class="max-w-3xl mx-auto">

        <div class="bg-white rounded-2xl shadow-lg p-8">

            <h1 class="text-3xl font-bold mb-8">

                Edit Varian Barang

            </h1>

            <form action="{{ route('varian.update', $varian) }}" method="POST">

                @csrf
                @method('PUT')

                <div class="mb-5">

                    <label class="block mb-2 font-medium">

                        Barang

                    </label>

                    <select name="barang_id" class="w-full border rounded-lg p-3">

                        @foreach($barang as $item)

                            <option value="{{ $item->id }}" {{ $varian->barang_id == $item->id ? 'selected' : '' }}>

                                {{ $item->nama_barang }}

                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="mb-5">

                    <label class="block mb-2 font-medium">

                        Warna

                    </label>

                    <input type="text" name="warna" value="{{ $varian->warna }}" class="w-full border rounded-lg p-3">

                </div>

                <div class="mb-5">

                    <label class="block mb-2 font-medium">

                        Ukuran

                    </label>

                    <input type="text" name="ukuran" value="{{ $varian->ukuran }}" class="w-full border rounded-lg p-3">

                </div>

                <div class="mb-5">

                    <label class="block mb-2 font-medium">

                        Stok

                    </label>

                    <input type="number" name="stok" value="{{ $varian->stok }}" class="w-full border rounded-lg p-3">

                </div>

                <div class="mb-5">

                    <label class="block mb-2 font-medium">

                        SKU

                    </label>

                    <input type="text" name="sku" value="{{ $varian->sku }}" class="w-full border rounded-lg p-3">

                </div>

                <div class="mb-5">

                    <label class="block mb-2 font-medium">

                        Harga Beli

                    </label>

                    <input type="number" name="harga_beli" value="{{ $varian->harga_beli }}"
                        class="w-full border rounded-lg p-3">

                </div>

                <div class="mb-5">

                    <label class="block mb-2 font-medium">

                        Harga Jual

                    </label>

                    <input type="number" name="harga_jual" value="{{ $varian->harga_jual }}"
                        class="w-full border rounded-lg p-3">

                </div>

                <div class="mb-5">

                    <label class="block mb-2 font-medium">

                        Stok Minimum

                    </label>

                    <input type="number" name="stok_minimum" value="{{ $varian->stok_minimum }}"
                        class="w-full border rounded-lg p-3">

                </div>

                <div class="flex gap-3">

                    <a href="{{ route('varian.index') }}" class="bg-gray-500 text-white px-5 py-3 rounded-xl">

                        Kembali

                    </a>

                    <button type="submit" class="bg-blue-600 text-white px-5 py-3 rounded-xl">

                        Update

                    </button>

                </div>

            </form>

        </div>

    </div>

@endsection