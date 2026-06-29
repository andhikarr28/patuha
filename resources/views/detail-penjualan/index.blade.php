@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto">

    <div class="bg-white rounded-2xl shadow-lg p-8 mb-6">

        <div class="flex justify-between items-start">

            <div>

                <h1 class="text-3xl font-bold mb-4">

                    Detail Transaksi Penjualan

                </h1>

                <div class="space-y-2">

                    <p>
                        <span class="font-semibold">
                            No Nota :
                        </span>

                        {{ $penjualan->no_nota }}
                    </p>

                    <p>
                        <span class="font-semibold">
                            Tanggal :
                        </span>

                        {{ $penjualan->tanggal_penjualan }}
                    </p>

                    <p>
                        <span class="font-semibold">
                            Channel :
                        </span>

                        {{ ucfirst($penjualan->channel) }}
                    </p>

                    <p>
                        <span class="font-semibold">
                            Metode Pembayaran :
                        </span>

                        {{ ucfirst($penjualan->metode_pembayaran) }}
                    </p>

                </div>

            </div>

            <div class="text-right">

                <p class="text-gray-500">

                    Total Transaksi

                </p>

                <h2 class="text-4xl font-bold text-green-600">

                    Rp {{ number_format($penjualan->total,0,',','.') }}

                </h2>

            </div>

        </div>

    </div>

    @if(session('success'))

        <div class="bg-green-100 border border-green-300 text-green-700 p-4 rounded-xl mb-5">

            {{ session('success') }}

        </div>

    @endif

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

        <table class="w-full">

            <thead class="bg-slate-100">

                <tr>

                    <th class="px-6 py-4 text-left">
                        Barang
                    </th>

                    <th class="px-6 py-4 text-left">
                        Varian
                    </th>

                    <th class="px-6 py-4 text-center">
                        Qty
                    </th>

                    <th class="px-6 py-4 text-right">
                        Harga
                    </th>

                    <th class="px-6 py-4 text-right">
                        Subtotal
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($detail as $item)

                    <tr class="border-t hover:bg-slate-50">

                        <td class="px-6 py-4">

                            {{ $item->varian->barang->nama_barang }}

                        </td>

                        <td class="px-6 py-4">

                            {{ $item->varian->warna }}
                            /
                            {{ $item->varian->ukuran }}

                        </td>

                        <td class="px-6 py-4 text-center">

                            {{ $item->qty }}

                        </td>

                        <td class="px-6 py-4 text-right">

                            Rp {{ number_format($item->harga,0,',','.') }}

                        </td>

                        <td class="px-6 py-4 text-right font-semibold">

                            Rp {{ number_format($item->subtotal,0,',','.') }}

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="5" class="text-center py-10">

                            Belum ada item transaksi

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection