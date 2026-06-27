@extends('layouts.app')

@section('content')

    <h1 class="text-3xl font-bold mb-6">
        Laporan Penjualan
    </h1>

    <div class="mb-4">

        <a href="{{ route('laporan.penjualan.pdf') }}" class="bg-red-600 text-white px-5 py-3 rounded-xl">

            📄 Cetak PDF

        </a>

    </div>

    <form method="GET" class="bg-white p-6 rounded-2xl shadow mb-6">

        <div class="grid grid-cols-3 gap-4">

            <div>

                <label class="block mb-2 font-medium">
                    Tanggal Awal
                </label>

                <input type="date" name="tanggal_awal" value="{{ request('tanggal_awal') }}"
                    class="w-full border rounded-lg p-3">

            </div>

            <div>

                <label class="block mb-2 font-medium">
                    Tanggal Akhir
                </label>

                <input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}"
                    class="w-full border rounded-lg p-3">

            </div>

            <div class="flex items-end">

                <button class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded-lg p-3">

                    Tampilkan

                </button>

            </div>

        </div>

    </form>

    <div class="bg-white rounded-2xl shadow overflow-hidden">

        <table class="w-full">

            <thead class="bg-slate-100">

                <tr>

                    <th class="px-6 py-4 text-left">Nota</th>
                    <th class="px-6 py-4 text-left">Tanggal</th>
                    <th class="px-6 py-4 text-left">Channel</th>
                    <th class="px-6 py-4 text-right">Total</th>

                </tr>

            </thead>

            <tbody>

                @foreach($penjualan as $item)

                    <tr class="border-t">

                        <td class="px-6 py-4">
                            {{ $item->no_nota }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $item->tanggal_penjualan }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $item->channel }}
                        </td>

                        <td class="px-6 py-4 text-right">
                            Rp {{ number_format($item->total, 0, ',', '.') }}
                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    </div>

    <div class="bg-green-600 text-white mt-6 p-6 rounded-2xl">

        <h2 class="text-xl font-bold">
            Total Penjualan
        </h2>

        <p class="text-3xl font-bold mt-2">
            Rp {{ number_format($total, 0, ',', '.') }}
        </p>

    </div>

@endsection