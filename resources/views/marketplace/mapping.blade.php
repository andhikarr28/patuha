@extends('layouts.app')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

        <div>

            <a
                href="{{ route('marketplace.index') }}"
                class="text-sm text-slate-500 hover:text-blue-600
                       inline-flex items-center gap-2 mb-2">

                ← Kembali ke Marketplace

            </a>

            <h1 class="text-3xl font-bold text-slate-900">
                Mapping SKU Shopee
            </h1>

            <p class="text-slate-500 mt-1">
                Hubungkan setiap varian Shopee dengan varian barang pada sistem lokal.
            </p>

        </div>

    </div>


    {{-- INFO --}}
    <div class="bg-blue-50 border border-blue-200
                rounded-2xl p-5">

        <div class="flex items-start gap-4">

            <div class="w-11 h-11 rounded-xl
                        bg-blue-100
                        flex items-center justify-center
                        flex-shrink-0">

                🔗

            </div>

            <div>

                <h3 class="font-bold text-blue-900">
                    Mengapa SKU perlu dimapping?
                </h3>

                <p class="text-sm text-blue-700 mt-1 leading-relaxed">

                    Mapping digunakan untuk menentukan varian lokal mana yang
                    sesuai dengan varian Shopee. Setelah terhubung, sistem dapat
                    melakukan sinkronisasi stok dan memproses pesanan marketplace
                    ke varian yang tepat.

                </p>

            </div>

        </div>

    </div>


    {{-- SUMMARY --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

        <div class="bg-white border border-slate-200 rounded-2xl p-5">

            <p class="text-sm text-slate-500">
                Total Varian Shopee
            </p>

            <p class="text-2xl font-bold text-slate-900 mt-1">
                {{ $models->count() }}
            </p>

        </div>


        <div class="bg-white border border-slate-200 rounded-2xl p-5">

            <p class="text-sm text-slate-500">
                Varian Lokal Tersedia
            </p>

            <p class="text-2xl font-bold text-slate-900 mt-1">
                {{ $varians->count() }}
            </p>

        </div>


        <div class="bg-white border border-slate-200 rounded-2xl p-5">

            <p class="text-sm text-slate-500">
                Status
            </p>

            <p class="text-sm font-semibold text-green-600 mt-2">
                ● Siap Mapping
            </p>

        </div>

    </div>


    {{-- MAPPING LIST --}}
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">

        <div class="px-6 py-5 border-b border-slate-200">

            <h2 class="text-lg font-bold text-slate-900">
                Daftar Varian
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Pilih varian lokal yang sesuai dengan masing-masing SKU Shopee.
            </p>

        </div>


        @forelse($models as $model)

            <form
                action="{{ route('marketplace.mappings.store') }}"
                method="POST"
                class="border-b border-slate-100 last:border-b-0">

                @csrf

                <input
                    type="hidden"
                    name="marketplace_item_model_id"
                    value="{{ $model->id }}">


                <div class="p-6">

                    <div class="grid grid-cols-1
                                lg:grid-cols-[1fr_auto_1.5fr_auto]
                                gap-5 lg:items-center">


                        {{-- SHOPEE SKU --}}
                        <div>

                            <p class="text-xs font-semibold
                                      uppercase tracking-wide
                                      text-orange-500 mb-2">

                                Shopee

                            </p>

                            <div class="flex items-center gap-3">

                                <div class="w-11 h-11 rounded-xl
                                            bg-orange-50
                                            flex items-center justify-center
                                            flex-shrink-0">

                                    🛍️

                                </div>

                                <div>

                                    <p class="text-sm text-slate-500">
                                        SKU Marketplace
                                    </p>

                                    <p class="font-bold text-slate-900">
                                        {{ $model->model_sku ?: 'Tanpa SKU' }}
                                    </p>

                                </div>

                            </div>

                        </div>


                        {{-- ARROW --}}
                        <div class="hidden lg:flex
                                    w-10 h-10 rounded-full
                                    bg-slate-100
                                    items-center justify-center
                                    text-slate-500">

                            ⇄

                        </div>


                        {{-- LOCAL VARIANT --}}
                        <div>

                            <label class="block text-xs font-semibold
                                          uppercase tracking-wide
                                          text-blue-600 mb-2">

                                Varian Sistem Lokal

                            </label>

                            <select
                                name="varian_id"
                                required
                                class="w-full border border-slate-300
                                       rounded-xl px-4 py-3
                                       bg-white
                                       focus:ring-2 focus:ring-blue-500
                                       focus:border-blue-500">

                                <option value="">
                                    -- Pilih varian lokal --
                                </option>

                                @foreach($varians as $varian)

                                    <option value="{{ $varian->id }}">

                                        {{ $varian->barang?->nama_barang ?? 'Barang' }}

                                        —

                                        {{ $varian->warna ?: '-' }}

                                        /

                                        {{ $varian->ukuran ?: '-' }}

                                        —

                                        SKU: {{ $varian->sku ?: '-' }}

                                        —

                                        Stok: {{ $varian->stok }}

                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- ACTION --}}
                        <div>

                            <button
                                type="submit"
                                class="w-full lg:w-auto
                                       bg-blue-600 hover:bg-blue-700
                                       text-white font-semibold
                                       px-5 py-3 rounded-xl transition">

                                ✓ Simpan

                            </button>

                        </div>

                    </div>

                </div>

            </form>

        @empty

            <div class="py-16 text-center">

                <div class="text-4xl mb-3">
                    🔗
                </div>

                <h3 class="font-bold text-slate-900">
                    Belum Ada Varian Shopee
                </h3>

                <p class="text-sm text-slate-500 mt-1">
                    Sinkronkan produk dan variasi Shopee terlebih dahulu.
                </p>

                <a
                    href="{{ route('marketplace.index') }}"
                    class="inline-block mt-5
                           bg-blue-600 hover:bg-blue-700
                           text-white font-semibold
                           px-5 py-3 rounded-xl">

                    Kembali ke Marketplace

                </a>

            </div>

        @endforelse

    </div>


    {{-- FOOTER INFO --}}
    @if($models->isNotEmpty())

        <div class="flex justify-end">

            <a
                href="{{ route('marketplace.index') }}"
                class="bg-slate-900 hover:bg-slate-800
                       text-white font-semibold
                       px-5 py-3 rounded-xl transition">

                Selesai Mapping →

            </a>

        </div>

    @endif

</div>

@endsection