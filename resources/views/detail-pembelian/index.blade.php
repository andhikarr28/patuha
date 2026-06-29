@extends('layouts.app')

@section('content')

<div class="flex justify-between items-center mb-6">

    <h1 class="text-3xl font-bold">
        Detail Pembelian
    </h1>

</div>

@if(session('success'))

<div class="bg-green-100 border border-green-300 text-green-700 p-4 rounded-xl mb-5">

    {{ session('success') }}

</div>

@endif

{{-- INFORMASI PEMBELIAN --}}
<div class="bg-white rounded-2xl shadow-lg p-6 mb-6">

    <div class="flex justify-between items-start">

        <div>

            <h2 class="text-2xl font-bold mb-4">
                Informasi Pembelian
            </h2>

            <p class="mb-2">
                <span class="font-semibold">No Faktur :</span>
                {{ $pembelian->no_faktur }}
            </p>

            <p class="mb-2">
                <span class="font-semibold">Tanggal :</span>
                {{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->format('d M Y') }}
            </p>

            <p>
                <span class="font-semibold">Supplier :</span>
                {{ $pembelian->supplier->nama_supplier }}
            </p>

        </div>

        <div class="text-right">

            <p class="text-slate-500 mb-2">
                Total Netto
            </p>

            <h2 class="text-5xl font-bold text-green-600">
                Rp {{ number_format($pembelian->total_netto,0,',','.') }}
            </h2>

        </div>

    </div>

</div>

{{-- DETAIL BARANG --}}
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
                    Diskon
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
                    Rp {{ number_format($item->harga_satuan,0,',','.') }}
                </td>

                <td class="px-6 py-4 text-right text-red-600">
                    Rp {{ number_format($item->diskon,0,',','.') }}
                </td>

                <td class="px-6 py-4 text-right font-semibold">
                    Rp {{ number_format($item->subtotal,0,',','.') }}
                </td>

            </tr>

            @empty

            <tr>

                <td colspan="6"
                    class="text-center py-10 text-slate-500">

                    Belum ada data detail pembelian

                </td>

            </tr>

            @endforelse

        </tbody>

    </table>

</div>

{{-- RINGKASAN --}}
<div class="flex justify-end mt-6">

    <div class="bg-white rounded-2xl shadow-lg p-6 w-[400px]">

        <div class="flex justify-between mb-3">

            <span>Total Brutto</span>

            <span>
                Rp {{ number_format($pembelian->total_brutto,0,',','.') }}
            </span>

        </div>

        <div class="flex justify-between mb-3 text-red-600">

            <span>Total Diskon</span>

            <span>
                Rp {{ number_format($pembelian->total_diskon,0,',','.') }}
            </span>

        </div>

        <hr class="my-4">

        <div class="flex justify-between text-2xl font-bold text-green-600">

            <span>Total Netto</span>

            <span>
                Rp {{ number_format($pembelian->total_netto,0,',','.') }}
            </span>

        </div>

    </div>

</div>

@endsection