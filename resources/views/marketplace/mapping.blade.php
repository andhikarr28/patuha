@extends('layouts.app')

@section('content')
<div class="p-4 space-y-6">

    {{-- HEADER --}}
    <div>
        <a href="{{ route('marketplace.index') }}" class="text-sm text-blue-600">← Kembali ke Marketplace</a>
        <h1 class="text-2xl font-bold">Mapping SKU Shopee</h1>
        <p class="text-gray-500 text-sm">Hubungkan setiap varian Shopee dengan varian barang pada sistem lokal.</p>
    </div>

    {{-- INFO --}}
    <div class="border border-blue-200 bg-blue-50 rounded p-4 text-sm text-blue-800">
        <p class="font-semibold mb-1">Mengapa SKU perlu dimapping?</p>
        <p>Mapping menentukan varian lokal mana yang sesuai dengan varian Shopee, sehingga sistem bisa sinkron stok dan memproses pesanan ke varian yang tepat.</p>
    </div>

    {{-- SUMMARY --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div class="border rounded p-3">
            <p class="text-sm text-gray-500">Total Varian Shopee</p>
            <p class="text-xl font-bold">{{ $models->count() }}</p>
        </div>
        <div class="border rounded p-3">
            <p class="text-sm text-gray-500">Varian Lokal Tersedia</p>
            <p class="text-xl font-bold">{{ $varians->count() }}</p>
        </div>
        <div class="border rounded p-3">
            <p class="text-sm text-gray-500">Status</p>
            <p class="font-semibold text-green-600">● Siap Mapping</p>
        </div>
    </div>

    {{-- MAPPING LIST --}}
    <div class="border rounded">
        <div class="px-4 py-3 border-b">
            <h2 class="font-bold">Daftar Varian</h2>
            <p class="text-sm text-gray-500">Pilih varian lokal yang sesuai dengan masing-masing SKU Shopee.</p>
        </div>

        @forelse($models as $model)
            <form action="{{ route('marketplace.mappings.store') }}" method="POST" class="border-b last:border-b-0 p-4">
                @csrf
                <input type="hidden" name="marketplace_item_model_id" value="{{ $model->id }}">

                <div class="grid grid-cols-1 lg:grid-cols-[1fr_1.5fr_auto] gap-3 lg:items-center">

                    <div>
                        <p class="text-xs font-semibold text-orange-500 mb-1">SHOPEE</p>
                        <p class="text-sm text-gray-500">SKU Marketplace</p>
                        <p class="font-semibold">{{ $model->model_sku ?: 'Tanpa SKU' }}</p>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-blue-600 mb-1 block">Varian Sistem Lokal</label>
                        <select name="varian_id" required class="w-full border rounded px-3 py-2 text-sm">
                            <option value="">-- Pilih varian lokal --</option>
                            @foreach($varians as $varian)
                                <option value="{{ $varian->id }}">
                                    {{ $varian->barang?->nama_barang ?? 'Barang' }} — {{ $varian->warna ?: '-' }}/{{ $varian->ukuran ?: '-' }} — SKU: {{ $varian->sku ?: '-' }} — Stok: {{ $varian->stok }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <button type="submit" class="w-full lg:w-auto bg-blue-600 text-white rounded px-4 py-2 text-sm font-semibold">✓ Simpan</button>
                    </div>

                </div>
            </form>
        @empty
            <div class="text-center py-10 px-4">
                <p class="font-semibold">Belum Ada Varian Shopee</p>
                <p class="text-sm text-gray-500 mt-1">Sinkronkan produk dan variasi Shopee terlebih dahulu.</p>
                <a href="{{ route('marketplace.index') }}" class="inline-block mt-3 bg-blue-600 text-white rounded px-4 py-2 text-sm font-semibold">Kembali ke Marketplace</a>
            </div>
        @endforelse
    </div>

    @if($models->isNotEmpty())
        <div class="flex justify-end">
            <a href="{{ route('marketplace.index') }}" class="bg-slate-900 text-white rounded px-4 py-2 text-sm font-semibold">Selesai Mapping →</a>
        </div>
    @endif

</div>
@endsection