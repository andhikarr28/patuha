@extends('layouts.app')

@section('content')

    <div class="flex justify-between items-center mb-6">

        <h1 class="text-3xl font-bold">
            Data Pembelian
        </h1>

        <a href="{{ route('pembelian.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-xl shadow">

            + Tambah Pembelian

        </a>

    </div>

    @if(session('success'))

        <div class="bg-green-100 border border-green-300 text-green-700 p-4 rounded-xl mb-5">

            {{ session('success') }}

        </div>

    @endif

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

        <table class="w-full">

            <thead class="bg-slate-100 text-slate-700">

                <tr>

                    <th class="px-6 py-4 text-left">
                        No Faktur
                    </th>

                    <th class="px-6 py-4 text-left">
                        Tanggal
                    </th>

                    <th class="px-6 py-4 text-left">
                        Supplier
                    </th>

                    <th class="px-6 py-4 text-right">
                        Total Netto
                    </th>

                    <th class="px-6 py-4 text-center">
                        Aksi
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($pembelian as $item)

                    <tr class="border-t hover:bg-slate-50 transition">

                        <td class="px-6 py-4 font-medium">
                            {{ $item->no_faktur }}
                        </td>

                        <td class="px-6 py-4">
                            {{ \Carbon\Carbon::parse($item->tanggal_pembelian)->format('d M Y') }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $item->supplier->nama_supplier }}
                        </td>

                        <td class="px-6 py-4 text-right font-semibold">
                            Rp {{ number_format($item->total_netto, 0, ',', '.') }}
                        </td>

                        <td class="px-6 py-4">

                            <div class="flex justify-center gap-2">

                                <a href="{{ route('detail-pembelian.index', ['pembelian_id' => $item->id]) }}"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">

                                    Detail

                                </a>

                                <a href="{{ route('pembelian.edit', $item) }}"
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg">

                                    Edit

                                </a>

                                <form action="{{ route('pembelian.destroy', $item) }}" method="POST">

                                    @csrf
                                    @method('DELETE')

                                    <button onclick="return confirm('Yakin hapus pembelian ini?')"
                                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">

                                        Hapus

                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="5" class="text-center py-10 text-slate-500">

                            Belum ada data pembelian

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

@endsection