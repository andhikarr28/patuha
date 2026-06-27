@extends('layouts.app')

@section('content')

<div class="flex justify-between items-center mb-6">

    <h1 class="text-3xl font-bold">
        Detail Pembelian
    </h1>

    <a href="{{ route('detail-pembelian.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-xl">

        + Tambah Detail

    </a>

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

                <th class="px-6 py-4 text-left">Faktur</th>
                <th class="px-6 py-4 text-left">Barang</th>
                <th class="px-6 py-4 text-left">Varian</th>
                <th class="px-6 py-4 text-center">Qty</th>
                <th class="px-6 py-4 text-right">Harga</th>
                <th class="px-6 py-4 text-right">Diskon</th>
                <th class="px-6 py-4 text-right">Subtotal</th>
                <th class="px-6 py-4 text-center">Aksi</th>

            </tr>

        </thead>

        <tbody>

            @forelse($detail as $item)

            <tr class="border-t hover:bg-slate-50">

                <td class="px-6 py-4">
                    {{ $item->pembelian->no_faktur }}
                </td>

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

                <td class="px-6 py-4 text-right">
                    Rp {{ number_format($item->diskon,0,',','.') }}
                </td>

                <td class="px-6 py-4 text-right font-semibold">
                    Rp {{ number_format($item->subtotal,0,',','.') }}
                </td>

                <td class="px-6 py-4">

                    <div class="flex justify-center gap-2">

                        <a href="{{ route('detail-pembelian.edit',$item) }}"
                           class="bg-yellow-500 text-white px-4 py-2 rounded-lg">

                            Edit

                        </a>

                        <form action="{{ route('detail-pembelian.destroy',$item) }}"
                              method="POST">

                            @csrf
                            @method('DELETE')

                            <button
                                onclick="return confirm('Yakin hapus detail pembelian?')"
                                class="bg-red-600 text-white px-4 py-2 rounded-lg">

                                Hapus

                            </button>

                        </form>

                    </div>

                </td>

            </tr>

            @empty

            <tr>

                <td colspan="8"
                    class="text-center py-10 text-slate-500">

                    Belum ada data

                </td>

            </tr>

            @endforelse

        </tbody>

    </table>

</div>

@endsection