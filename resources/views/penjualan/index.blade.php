@extends('layouts.app')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <div>
            <h1 class="text-3xl font-bold text-slate-900">
                Penjualan
            </h1>

            <p class="text-slate-500 mt-1">
                Kelola transaksi penjualan toko dan marketplace.
            </p>
        </div>

        <a href="{{ route('penjualan.create') }}"
           class="inline-flex items-center justify-center gap-2
                  bg-blue-600 hover:bg-blue-700
                  text-white font-semibold
                  px-5 py-3 rounded-xl transition">

            <span>＋</span>
            Transaksi Baru

        </a>

    </div>


    {{-- SUCCESS --}}
    @if(session('success'))

        <div class="bg-green-50 border border-green-200
                    text-green-700 px-5 py-4 rounded-xl">

            {{ session('success') }}

        </div>

    @endif


    {{-- TABLE CARD --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">

        {{-- CARD HEADER --}}
        <div class="px-6 py-5 border-b border-slate-200
                    flex items-center justify-between">

            <div>

                <h2 class="text-lg font-bold text-slate-900">
                    Riwayat Transaksi
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    Daftar seluruh transaksi penjualan yang tercatat.
                </p>

            </div>

            <span class="bg-slate-100 text-slate-600
                         px-3 py-1.5 rounded-lg text-sm font-medium">

                {{ $penjualan->count() }} Transaksi

            </span>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full">

                <thead class="bg-slate-50">

                    <tr class="text-xs uppercase tracking-wide text-slate-500">

                        <th class="px-6 py-4 text-left">
                            No. Nota
                        </th>

                        <th class="px-6 py-4 text-left">
                            Tanggal
                        </th>

                        <th class="px-6 py-4 text-left">
                            Channel
                        </th>

                        <th class="px-6 py-4 text-right">
                            Total
                        </th>

                        <th class="px-6 py-4 text-right">
                            Aksi
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-slate-100">

                    @forelse($penjualan as $item)

                        <tr class="hover:bg-slate-50 transition">

                            {{-- NOTA --}}
                            <td class="px-6 py-5">

                                <div class="font-semibold text-slate-900">
                                    {{ $item->no_nota }}
                                </div>

                                <div class="text-xs text-slate-400 mt-1">
                                    ID #{{ $item->id }}
                                </div>

                            </td>


                            {{-- TANGGAL --}}
                            <td class="px-6 py-5 text-slate-600">

                                {{ \Carbon\Carbon::parse(
                                    $item->tanggal_penjualan
                                )->format('d M Y') }}

                            </td>


                            {{-- CHANNEL --}}
                            <td class="px-6 py-5">

                                @if(strtolower($item->channel) === 'shopee')

                                    <span class="inline-flex items-center
                                                 bg-orange-50 text-orange-700
                                                 px-3 py-1 rounded-full
                                                 text-sm font-medium">

                                        🛍️ Shopee

                                    </span>

                                @else

                                    <span class="inline-flex items-center
                                                 bg-blue-50 text-blue-700
                                                 px-3 py-1 rounded-full
                                                 text-sm font-medium">

                                        🏪 {{ ucfirst($item->channel) }}

                                    </span>

                                @endif

                            </td>


                            {{-- TOTAL --}}
                            <td class="px-6 py-5 text-right">

                                <span class="font-bold text-slate-900">

                                    Rp {{ number_format(
                                        $item->total,
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
                                        'detail-penjualan.index',
                                        ['penjualan_id' => $item->id]
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
                                    🧾
                                </div>

                                <h3 class="font-semibold text-slate-800">
                                    Belum Ada Transaksi
                                </h3>

                                <p class="text-sm text-slate-500 mt-1">
                                    Transaksi penjualan akan muncul di sini.
                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection