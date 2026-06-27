@extends('layouts.app')

@section('content')

    <div class="max-w-3xl mx-auto">

        <div class="bg-white p-8 rounded-2xl shadow-lg">

            <h1 class="text-3xl font-bold mb-6">

                Tambah Detail Penjualan

            </h1>

            <form action="{{ route('detail-penjualan.store') }}" method="POST">

                @csrf

                <div class="mb-4">

                    <label>Penjualan</label>

                    <select name="penjualan_id" class="w-full border rounded-lg p-3">

                        @foreach($penjualan as $item)

                            <option value="{{ $item->id }}">

                                {{ $item->no_nota }}

                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="mb-4">

                    <label>Varian Barang</label>

                    <select id="varian_id" name="varian_id" class="w-full border rounded-lg p-3">

                        @foreach($varian as $item)

                            <option value="{{ $item->id }}" data-harga="{{ $item->harga_jual }}">

                                {{ $item->barang->nama_barang }}
                                - {{ $item->warna }}
                                - {{ $item->ukuran }}
                                (Stok: {{ $item->stok }})

                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="mb-4">

                    <label>Qty</label>

                    <input type="number" name="qty" class="w-full border rounded-lg p-3">

                </div>

                <div class="mb-4">

                    <label>Harga</label>

                    <input type="number" id="harga" name="harga" readonly class="w-full border rounded-lg p-3 bg-slate-100">

                </div>

                <button class="bg-blue-600 text-white px-5 py-3 rounded-xl">

                    Simpan

                </button>

            </form>

        </div>

    </div>
    <script>    
        document.addEventListener('DOMContentLoaded', function () {

            const varian =
                document.getElementById('varian_id');

            const harga =
                document.getElementById('harga');

            function updateHarga() {

                harga.value =
                    varian.options[
                        varian.selectedIndex
                    ].dataset.harga;
            }

            updateHarga();

            varian.addEventListener(
                'change',
                updateHarga
            );
        });
    </script>
@endsection