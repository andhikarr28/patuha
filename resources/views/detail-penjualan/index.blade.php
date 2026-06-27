@extends('layouts.app')

@section('content')

<div class="flex justify-between items-center mb-6">

    <h1 class="text-3xl font-bold">
        Detail Penjualan
    </h1>

    <a href="{{ route('detail-penjualan.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-xl">

        + Tambah Detail

    </a>

</div>

@if(session('success'))

<div class="bg-green-100 border border-green-300 text-green-700 p-4 rounded-xl mb-5">

    {{ session('success') }}

</div>

@endif

@if(session('error'))

<div class="bg-red-100 border border-red-300 text-red-700 p-4 rounded-xl mb-5">

    {{ session('error') }}

</div>

@endif

<div class="bg-white rounded-2xl shadow-lg overflow-hidden">

    <table class="w-full">

        <thead class="bg-slate-100">

            <tr>

                <th class="px-6 py-4 text-left">Nota</th>
                <th class="px-6 py-4 text-left">Barang</th>
                <th class="px-6 py-4 text-left">Varian</th>
                <th class="px-6 py-4 text-center">Qty</th>
                <th class="px-6 py-4 text-right">Harga</th>
                <th class="px-6 py-4 text-right">Subtotal</th>
                <th class="px-6 py-4 text-center">Aksi</th>

            </tr>

        </thead>

        <tbody>

            @forelse($detail as $item)

            <tr class="border-t hover:bg-slate-50">

                <td class="px-6 py-4">
                    {{ $item->penjualan->no_nota }}
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
                    Rp {{ number_format($item->harga,0,',','.') }}
                </td>

                <td class="px-6 py-4 text-right font-semibold">
                    Rp {{ number_format($item->subtotal,0,',','.') }}
                </td>

                <td class="px-6 py-4">

                    <div class="flex justify-center gap-2">

                        <a href="{{ route('detail-penjualan.edit',$item) }}"
                           class="bg-yellow-500 text-white px-4 py-2 rounded-lg">

                            Edit

                        </a>

                        <form action="{{ route('detail-penjualan.destroy',$item) }}"
                              method="POST">

                            @csrf
                            @method('DELETE')

                            <button
                                onclick="return confirm('Yakin hapus detail penjualan?')"
                                class="bg-red-600 text-white px-4 py-2 rounded-lg">

                                Hapus

                            </button>

                        </form>

                    </div>

                </td>

            </tr>

            @empty

            <tr>

                <td colspan="7"
                    class="text-center py-10">

                    Belum ada data

                </td>

            </tr>

            @endforelse

        </tbody>

    </table>

</div>

@endsection