@extends('layouts.app')

@section('content')

<div class="flex justify-between items-center mb-6">

    <h1 class="text-3xl font-bold">
        Data Varian Barang
    </h1>

    <a href="{{ route('varian.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-xl">

        + Tambah Varian

    </a>

</div>

@if(session('success'))

<div class="bg-green-100 text-green-700 p-4 rounded-xl mb-5">

    {{ session('success') }}

</div>

@endif

<div class="bg-white rounded-2xl shadow-lg overflow-hidden">

    <table class="w-full">

        <thead class="bg-slate-100">

            <tr>

                <th class="px-6 py-4 text-left">Barang</th>
                <th class="px-6 py-4 text-left">Warna</th>
                <th class="px-6 py-4 text-left">Ukuran</th>
                <th class="px-6 py-4 text-left">SKU</th>
                <th class="px-6 py-4 text-left">Harga Beli</th>
                <th class="px-6 py-4 text-left">Harga Jual</th>
                <th class="px-6 py-4 text-left">Margin</th>
                <th class="px-6 py-4 text-center">Stok</th>
                <th class="px-6 py-4 text-center">Min Stok</th>
                <th class="px-6 py-4 text-center">Aksi</th>

            </tr>

        </thead>

        <tbody>

            @forelse($varian as $item)

            <tr class="border-t hover:bg-slate-50">

                <td class="px-6 py-4">
                    {{ $item->barang->nama_barang }}
                </td>

                <td class="px-6 py-4">
                    {{ $item->warna }}
                </td>

                <td class="px-6 py-4">
                    {{ $item->ukuran }}
                </td>

                <td class="px-6 py-4">
                    {{ $item->sku }}
                </td>

                <td class="px-6 py-4">
                    Rp {{ number_format($item->harga_beli ?? 0, 0, ',', '.') }}
                </td>

                <td class="px-6 py-4">
                    Rp {{ number_format($item->harga_jual, 0, ',', '.') }}
                </td>

                <td class="px-6 py-4">

                    <span class="font-semibold text-green-600">

                        Rp {{ number_format(($item->harga_jual ?? 0) - ($item->harga_beli ?? 0), 0, ',', '.') }}

                    </span>

                </td>

                <td class="px-6 py-4 text-center">

                    @if($item->stok <= $item->stok_minimum)

                        <span class="bg-red-100 text-red-600 px-3 py-1 rounded-lg font-semibold">

                            {{ $item->stok }}

                        </span>

                    @else

                        {{ $item->stok }}

                    @endif

                </td>

                <td class="px-6 py-4 text-center">

                    {{ $item->stok_minimum }}

                </td>

                <td class="px-6 py-4">

                    <div class="flex justify-center gap-2">

                        <a href="{{ route('varian.edit', $item) }}"
                           class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg">

                            Edit

                        </a>

                        <form action="{{ route('varian.destroy', $item) }}"
                              method="POST">

                            @csrf
                            @method('DELETE')

                            <button
                                onclick="return confirm('Yakin hapus varian ini?')"
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">

                                Hapus

                            </button>

                        </form>

                    </div>

                </td>

            </tr>

            @empty

            <tr>

                <td colspan="10"
                    class="text-center py-10">

                    Belum ada data

                </td>

            </tr>

            @endforelse

        </tbody>

    </table>

</div>

@endsection