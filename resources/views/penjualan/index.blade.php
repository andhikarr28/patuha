@extends('layouts.app')

@section('content')

<div class="flex justify-between items-center mb-6">

    <h1 class="text-3xl font-bold">
        Data Penjualan
    </h1>

    <a href="{{ route('penjualan.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-xl">

        + Tambah Penjualan

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

                <th class="px-6 py-4 text-left">No Nota</th>
                <th class="px-6 py-4 text-left">Tanggal</th>
                <th class="px-6 py-4 text-left">Channel</th>
                <th class="px-6 py-4 text-right">Total</th>
                <th class="px-6 py-4 text-center">Aksi</th>

            </tr>

        </thead>

        <tbody>

            @forelse($penjualan as $item)

            <tr class="border-t hover:bg-slate-50">

                <td class="px-6 py-4">
                    {{ $item->no_nota }}
                </td>

                <td class="px-6 py-4">
                    {{ $item->tanggal_penjualan }}
                </td>

                <td class="px-6 py-4 capitalize">
                    {{ $item->channel }}
                </td>

                <td class="px-6 py-4 text-right">
                    Rp {{ number_format($item->total,0,',','.') }}
                </td>

                <td class="px-6 py-4">

                    <div class="flex justify-center gap-2">

                        <a href="{{ route('detail-penjualan.index',['penjualan_id'=>$item->id]) }}"
                           class="bg-blue-600 text-white px-4 py-2 rounded-lg">

                            Detail

                        </a>

                        <a href="{{ route('penjualan.edit',$item) }}"
                           class="bg-yellow-500 text-white px-4 py-2 rounded-lg">

                            Edit

                        </a>

                        <form action="{{ route('penjualan.destroy',$item) }}"
                              method="POST">

                            @csrf
                            @method('DELETE')

                            <button
                                onclick="return confirm('Yakin hapus penjualan?')"
                                class="bg-red-600 text-white px-4 py-2 rounded-lg">

                                Hapus

                            </button>

                        </form>

                    </div>

                </td>

            </tr>

            @empty

            <tr>

                <td colspan="5"
                    class="text-center py-10">

                    Belum ada data

                </td>

            </tr>

            @endforelse

        </tbody>

    </table>

</div>

@endsection