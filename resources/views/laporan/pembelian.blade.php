@extends('layouts.app')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

        <div>

            <h1 class="text-3xl font-bold text-slate-900">
                Laporan Pembelian
            </h1>

            <p class="text-slate-500 mt-1">
                Rekap pembelian berdasarkan barang yang telah diterima dari supplier.
            </p>

        </div>

        <a
            href="{{ route('laporan.pembelian.pdf', [
                'tanggal_awal' => $tanggalAwal,
                'tanggal_akhir' => $tanggalAkhir
            ]) }}"
            target="_blank"
            class="inline-flex items-center justify-center gap-2
                   bg-red-600 hover:bg-red-700
                   text-white font-semibold
                   px-5 py-3 rounded-xl transition">

            📄 Cetak PDF

        </a>

    </div>


    {{-- FILTER --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-6">

        <div class="mb-5">

            <h2 class="text-lg font-bold text-slate-900">
                Filter Periode
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Pilih rentang tanggal pembelian yang ingin ditampilkan.
            </p>

        </div>

        <form method="GET">

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">

                <div>

                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Tanggal Awal
                    </label>

                    <input
                        type="date"
                        name="tanggal_awal"
                        value="{{ $tanggalAwal }}"
                        class="w-full border border-slate-300
                               rounded-xl px-4 py-3
                               focus:ring-2 focus:ring-blue-500
                               focus:border-blue-500">

                </div>


                <div>

                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Tanggal Akhir
                    </label>

                    <input
                        type="date"
                        name="tanggal_akhir"
                        value="{{ $tanggalAkhir }}"
                        class="w-full border border-slate-300
                               rounded-xl px-4 py-3
                               focus:ring-2 focus:ring-blue-500
                               focus:border-blue-500">

                </div>


                <div class="flex items-end">

                    <button
                        type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700
                               text-white font-semibold
                               px-5 py-3 rounded-xl transition">

                        🔍 Tampilkan

                    </button>

                </div>


                <div class="flex items-end">

                    <a
                        href="{{ route('laporan.pembelian') }}"
                        class="w-full text-center
                               bg-slate-100 hover:bg-slate-200
                               text-slate-700 font-semibold
                               px-5 py-3 rounded-xl transition">

                        Reset Bulan Ini

                    </a>

                </div>

            </div>

        </form>

    </div>


    {{-- SUMMARY --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

        <div class="bg-white border border-slate-200 rounded-2xl p-6">

            <div class="flex items-start justify-between">

                <div>

                    <p class="text-sm font-medium text-slate-500">
                        Total Transaksi
                    </p>

                    <p class="text-3xl font-bold text-slate-900 mt-2">
                        {{ number_format($jumlahTransaksi) }}
                    </p>

                    <p class="text-sm text-slate-400 mt-1">
                        pembelian
                    </p>

                </div>

                <div class="w-12 h-12 bg-blue-50 rounded-xl
                            flex items-center justify-center text-xl">
                    📦
                </div>

            </div>

        </div>


        <div class="bg-white border border-slate-200 rounded-2xl p-6">

            <div class="flex items-start justify-between">

                <div>

                    <p class="text-sm font-medium text-slate-500">
                        Total Pembelian
                    </p>

                    <p class="text-2xl font-bold text-blue-600 mt-2">
                        Rp {{ number_format($total, 0, ',', '.') }}
                    </p>

                    <p class="text-sm text-slate-400 mt-1">
                        periode terpilih
                    </p>

                </div>

                <div class="w-12 h-12 bg-blue-50 rounded-xl
                            flex items-center justify-center text-xl">
                    🛒
                </div>

            </div>

        </div>


        <div class="bg-white border border-slate-200 rounded-2xl p-6">

            <div class="flex items-start justify-between">

                <div>

                    <p class="text-sm font-medium text-slate-500">
                        Rata-rata Pembelian
                    </p>

                    <p class="text-2xl font-bold text-slate-900 mt-2">
                        Rp {{ number_format($rataRata, 0, ',', '.') }}
                    </p>

                    <p class="text-sm text-slate-400 mt-1">
                        per transaksi
                    </p>

                </div>

                <div class="w-12 h-12 bg-amber-50 rounded-xl
                            flex items-center justify-center text-xl">
                    📊
                </div>

            </div>

        </div>

    </div>


    {{-- TABLE --}}
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">

        <div class="px-6 py-5 border-b border-slate-200
                    flex flex-col md:flex-row
                    md:items-center md:justify-between gap-2">

            <div>

                <h2 class="text-lg font-bold text-slate-900">
                    Data Pembelian
                </h2>

                <p class="text-sm text-slate-500 mt-1">

                    {{ \Carbon\Carbon::parse($tanggalAwal)->format('d M Y') }}

                    —

                    {{ \Carbon\Carbon::parse($tanggalAkhir)->format('d M Y') }}

                </p>

            </div>

            <span class="bg-slate-100 text-slate-600
                         text-sm font-semibold
                         px-4 py-2 rounded-xl">

                {{ $jumlahTransaksi }} Pembelian

            </span>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full">

                <thead class="bg-slate-50">

                    <tr class="text-sm text-slate-500">

                        <th class="px-6 py-4 text-left font-semibold">
                            No.
                        </th>

                        <th class="px-6 py-4 text-left font-semibold">
                            Faktur
                        </th>

                        <th class="px-6 py-4 text-left font-semibold">
                            Tanggal
                        </th>

                        <th class="px-6 py-4 text-left font-semibold">
                            Supplier
                        </th>

                        <th class="px-6 py-4 text-right font-semibold">
                            Netto
                        </th>

                        <th class="px-6 py-4 text-right font-semibold">
                            Aksi
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-slate-100">

                    @forelse($pembelian as $item)

                        <tr class="hover:bg-slate-50 transition">

                            <td class="px-6 py-4 text-slate-500">
                                {{ $loop->iteration }}
                            </td>

                            <td class="px-6 py-4">

                                <span class="font-semibold text-slate-900">
                                    {{ $item->no_faktur }}
                                </span>

                            </td>

                            <td class="px-6 py-4 text-slate-600">

                                {{ \Carbon\Carbon::parse($item->tanggal_pembelian)
                                    ->format('d M Y') }}

                            </td>

                            <td class="px-6 py-4">

                                <div class="flex items-center gap-2">

                                    <div class="w-9 h-9 rounded-lg
                                                bg-slate-100
                                                flex items-center justify-center">
                                        🚚
                                    </div>

                                    <span class="font-medium text-slate-700">

                                        {{ $item->supplier?->nama_supplier
                                            ?? '-' }}

                                    </span>

                                </div>

                            </td>

                            <td class="px-6 py-4 text-right
                                       font-bold text-slate-900">

                                Rp {{ number_format(
                                    $item->total_netto,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>

                            <td class="px-6 py-4 text-right">

                                <a
                                    href="{{ route(
                                        'penerimaan-pembelian.show',
                                        $item
                                    ) }}"
                                    class="inline-flex
                                           px-4 py-2
                                           bg-slate-100 hover:bg-slate-200
                                           text-slate-700
                                           text-sm font-semibold
                                           rounded-lg transition">

                                    Detail

                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="6" class="px-6 py-16 text-center">

                                <div class="text-4xl mb-3">
                                    📦
                                </div>

                                <h3 class="font-bold text-slate-900">
                                    Belum Ada Pembelian
                                </h3>

                                <p class="text-sm text-slate-500 mt-1">
                                    Tidak ada transaksi pembelian pada periode ini.
                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($pembelian->isNotEmpty())

            <div class="bg-slate-900 text-white
                        px-6 py-5
                        flex flex-col sm:flex-row
                        sm:items-center sm:justify-between gap-2">

                <div>

                    <p class="text-sm text-slate-300">
                        Total Pembelian Periode Ini
                    </p>

                    <p class="text-xs text-slate-400 mt-1">
                        {{ $jumlahTransaksi }} transaksi pembelian
                    </p>

                </div>

                <p class="text-2xl font-bold">
                    Rp {{ number_format($total, 0, ',', '.') }}
                </p>

            </div>

        @endif

    </div>

</div>

@endsection