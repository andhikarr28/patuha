@extends('layouts.app')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <div>

            <h1 class="text-3xl font-bold text-slate-900">
                Riwayat Pembelian
            </h1>

            <p class="text-slate-500 mt-1">
                Daftar pembelian yang telah diproses melalui penerimaan barang.
            </p>

        </div>


        <div class="flex gap-3">

            <a href="{{ route('perencanaan-pembelian.create') }}"
               class="inline-flex items-center gap-2
                      border border-slate-300
                      bg-white hover:bg-slate-50
                      text-slate-700 font-semibold
                      px-5 py-3 rounded-xl transition">

                📋 Buat Perencanaan

            </a>

            <a href="{{ route('penerimaan-pembelian.index') }}"
               class="inline-flex items-center gap-2
                      bg-blue-600 hover:bg-blue-700
                      text-white font-semibold
                      px-5 py-3 rounded-xl transition">

                📥 Penerimaan Barang

            </a>

        </div>

    </div>


    {{-- SUCCESS --}}
    @if(session('success'))

        <div class="bg-green-50 border border-green-200
                    text-green-700 px-5 py-4 rounded-xl">

            {{ session('success') }}

        </div>

    @endif


    {{-- TABLE --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">

        {{-- TABLE HEADER --}}
        <div class="px-6 py-5 border-b border-slate-200
                    flex items-center justify-between">

            <div>

                <h2 class="text-lg font-bold text-slate-900">
                    Transaksi Pembelian
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    Riwayat barang yang telah diterima dari supplier.
                </p>

            </div>

            <span class="bg-slate-100 text-slate-600
                         px-3 py-1.5 rounded-lg
                         text-sm font-medium">

                {{ $pembelian->count() }} Transaksi

            </span>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full">

                <thead class="bg-slate-50">

                    <tr class="text-xs uppercase tracking-wide text-slate-500">

                        <th class="px-6 py-4 text-left">
                            No. Faktur
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

                        <th class="px-6 py-4 text-right">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse($pembelian as $item)

                        <tr class="hover:bg-slate-50 transition">

                            {{-- FAKTUR --}}
                            <td class="px-6 py-5">

                                <div class="font-semibold text-slate-900">

                                    {{ $item->no_faktur }}

                                </div>

                                <div class="text-xs text-slate-400 mt-1">

                                    ID #{{ $item->id }}

                                </div>

                            </td>


                            {{-- TANGGAL --}}
                            <td class="px-6 py-5 text-slate-600">

                                {{ \Carbon\Carbon::parse(
                                    $item->tanggal_pembelian
                                )->format('d M Y') }}

                            </td>


                            {{-- SUPPLIER --}}
                            <td class="px-6 py-5">

                                <div class="flex items-center gap-3">

                                    <div class="w-9 h-9 rounded-lg
                                                bg-blue-50
                                                flex items-center justify-center">

                                        🚚

                                    </div>

                                    <span class="font-medium text-slate-800">

                                        {{ $item->supplier->nama_supplier ?? '-' }}

                                    </span>

                                </div>

                            </td>


                            {{-- TOTAL --}}
                            <td class="px-6 py-5 text-right">

                                <span class="font-bold text-slate-900">

                                    Rp {{ number_format(
                                        $item->total_netto,
                                        0,
                                        ',',
                                        '.'
                                    ) }}

                                </span>

                            </td>


                            {{-- AKSI --}}
                            <td class="px-6 py-5">

                                <div class="flex justify-end">

                                    <a href="{{ route(
                                        'detail-pembelian.index',
                                        ['pembelian_id' => $item->id]
                                    ) }}"
                                       class="inline-flex items-center gap-2
                                              border border-slate-300
                                              hover:border-blue-500
                                              hover:text-blue-600
                                              px-4 py-2 rounded-lg
                                              text-sm font-medium transition">

                                        👁 Detail

                                    </a>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5"
                                class="px-6 py-16 text-center">

                                <div class="text-4xl mb-3">
                                    📦
                                </div>

                                <h3 class="font-semibold text-slate-800">
                                    Belum Ada Riwayat Pembelian
                                </h3>

                                <p class="text-sm text-slate-500 mt-1">
                                    Pembelian akan muncul setelah proses
                                    penerimaan barang dilakukan.
                                </p>

                                <a href="{{ route('perencanaan-pembelian.create') }}"
                                   class="inline-flex mt-5
                                          bg-blue-600 hover:bg-blue-700
                                          text-white px-5 py-2.5
                                          rounded-xl font-semibold">

                                    Buat Perencanaan

                                </a>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection