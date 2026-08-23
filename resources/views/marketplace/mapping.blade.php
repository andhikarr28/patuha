@extends('layouts.app')

@section('content')
<div class="p-4 md:p-6 space-y-6">

    {{-- HEADER --}}
    <div>
        <a href="{{ route('marketplace.index') }}"
           class="text-sm text-blue-600 hover:underline">
            ← Kembali ke Marketplace
        </a>

        <div class="mt-2">
            <h1 class="text-2xl font-bold">Mapping SKU Shopee</h1>
            <p class="text-sm text-gray-500 mt-1">
                Hubungkan setiap varian Shopee dengan varian barang pada sistem lokal.
            </p>
        </div>
    </div>

    {{-- INFO --}}
    <div class="border border-blue-200 bg-blue-50 rounded-lg p-4 text-sm text-blue-800">
        <p class="font-semibold mb-1">Mengapa SKU perlu dimapping?</p>
        <p>
            Mapping menentukan varian lokal mana yang sesuai dengan varian Shopee,
            sehingga sistem dapat menyinkronkan stok dan memproses pesanan ke varian yang tepat.
        </p>
    </div>

    {{-- SUMMARY --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div class="border rounded-lg p-4 bg-white">
            <p class="text-sm text-gray-500">Total Varian Shopee</p>
            <p class="text-2xl font-bold mt-1">
                {{ $models->count() }}
            </p>
        </div>

        <div class="border rounded-lg p-4 bg-white">
            <p class="text-sm text-gray-500">Varian Lokal Tersedia</p>
            <p class="text-2xl font-bold mt-1">
                {{ $varians->count() }}
            </p>
        </div>

        <div class="border rounded-lg p-4 bg-white">
            @php
                $mappedCount = $models->filter(fn ($model) => $model->mapping)->count();
            @endphp

            <p class="text-sm text-gray-500">Sudah Mapping</p>
            <p class="text-2xl font-bold text-green-600 mt-1">
                {{ $mappedCount }} / {{ $models->count() }}
            </p>
        </div>
    </div>

    {{-- MAPPING LIST --}}
    <div class="border rounded-lg overflow-hidden bg-white">
        <div class="px-4 py-4 border-b">
            <h2 class="font-bold text-lg">Daftar Varian</h2>
            <p class="text-sm text-gray-500 mt-1">
                Pilih varian lokal yang sesuai dengan masing-masing SKU Shopee.
            </p>
        </div>

        @forelse($models as $model)

            @php
                $currentMapping = $model->mapping;
                $selectedVarianId = $currentMapping?->varian_id;
            @endphp

            <form
                action="{{ route('marketplace.mappings.store') }}"
                method="POST"
                class="border-b last:border-b-0 p-4 md:p-5"
            >
                @csrf

                <input
                    type="hidden"
                    name="marketplace_item_model_id"
                    value="{{ $model->id }}"
                >

                <div class="grid grid-cols-1 lg:grid-cols-[260px_1fr_auto] gap-4 lg:items-end">

                    {{-- SHOPEE --}}
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-semibold text-orange-500 uppercase">
                                Shopee
                            </span>

                            @if($currentMapping)
                                <span class="text-xs font-semibold text-green-600 bg-green-50 border border-green-200 rounded-full px-2 py-0.5">
                                    ✓ Sudah Mapping
                                </span>
                            @else
                                <span class="text-xs font-semibold text-gray-500 bg-gray-50 border rounded-full px-2 py-0.5">
                                    Belum Mapping
                                </span>
                            @endif
                        </div>

                        <p class="text-sm text-gray-500">
                            SKU Marketplace
                        </p>

                        <p class="font-semibold text-base">
                            {{ $model->model_sku ?: 'Tanpa SKU' }}
                        </p>
                    </div>

                    {{-- VARIAN LOKAL --}}
                    <div>
                        <label
                            for="varian-{{ $model->id }}"
                            class="text-xs font-semibold text-blue-600 mb-1.5 block"
                        >
                            Varian Sistem Lokal
                        </label>

                        <select
                            id="varian-{{ $model->id }}"
                            name="varian_id"
                            required
                            class="w-full border rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">
                                -- Pilih varian lokal --
                            </option>

                            @foreach($varians as $varian)
                                <option
                                    value="{{ $varian->id }}"
                                    {{ (string) $selectedVarianId === (string) $varian->id ? 'selected' : '' }}
                                >
                                    {{ $varian->sku ?: 'Tanpa SKU' }}
                                    —
                                    {{ $varian->barang?->nama_barang ?? 'Barang' }}
                                    —
                                    {{ $varian->warna ?: '-' }}/{{ $varian->ukuran ?: '-' }}
                                    —
                                    Stok: {{ $varian->stok }}
                                </option>
                            @endforeach
                        </select>

                        @if($currentMapping)
                            @php
                                $mappedVarian = $varians->firstWhere('id', $selectedVarianId);
                            @endphp

                            @if($mappedVarian)
                                <p class="text-xs text-green-600 mt-1.5">
                                    Saat ini terhubung ke SKU lokal:
                                    <span class="font-semibold">
                                        {{ $mappedVarian->sku ?: '-' }}
                                    </span>
                                </p>
                            @endif
                        @endif
                    </div>

                    {{-- ACTION --}}
                    <div>
                        <button
                            type="submit"
                            class="w-full lg:w-auto bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-5 py-2.5 text-sm font-semibold"
                        >
                            {{ $currentMapping ? '↻ Ubah Mapping' : '✓ Simpan Mapping' }}
                        </button>
                    </div>

                </div>
            </form>

        @empty

            <div class="text-center py-12 px-4">
                <p class="font-semibold">
                    Belum Ada Varian Shopee
                </p>

                <p class="text-sm text-gray-500 mt-1">
                    Sinkronkan produk dan variasi Shopee terlebih dahulu.
                </p>

                <a
                    href="{{ route('marketplace.index') }}"
                    class="inline-block mt-4 bg-blue-600 text-white rounded-lg px-4 py-2 text-sm font-semibold"
                >
                    Kembali ke Marketplace
                </a>
            </div>

        @endforelse
    </div>

    @if($models->isNotEmpty())
        <div class="flex justify-end">
            <a
                href="{{ route('marketplace.index') }}"
                class="w-full md:w-auto text-center bg-slate-900 hover:bg-slate-800 text-white rounded-lg px-5 py-2.5 text-sm font-semibold"
            >
                Selesai Mapping →
            </a>
        </div>
    @endif

</div>
@endsection