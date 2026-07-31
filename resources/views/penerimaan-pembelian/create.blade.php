@extends('layouts.app')

@section('content')
<div class="p-4 space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <a href="{{ route('penerimaan-pembelian.index') }}" class="text-sm text-blue-600">← Kembali ke Penerimaan</a>
            <h1 class="text-2xl font-bold">Penerimaan Barang</h1>
            <p class="text-gray-500 text-sm">Catat barang yang benar-benar diterima dari supplier.</p>
        </div>

        @php
            $statusLabel = match($perencanaan->status) {
                'sebagian_diterima' => 'Sebagian Diterima',
                'dipesan' => 'Dipesan',
                default => ucfirst(str_replace('_', ' ', $perencanaan->status)),
            };
        @endphp
        <span class="text-sm font-semibold text-gray-600">Status: {{ $statusLabel }}</span>
    </div>

    {{-- ERROR --}}
    @if(session('error'))
        <div class="border border-red-300 bg-red-50 text-red-700 rounded p-3 text-sm">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="border border-red-300 bg-red-50 rounded p-4 text-sm text-red-700">
            <p class="font-semibold mb-1">Penerimaan belum dapat disimpan</p>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('penerimaan-pembelian.store', $perencanaan) }}" method="POST" id="formPenerimaan">
        @csrf

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">

            {{-- LEFT --}}
            <div class="xl:col-span-2 space-y-4">

                {{-- INFO PERENCANAAN --}}
                <div class="border rounded p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="font-bold">Informasi Perencanaan</h2>
                        <span class="bg-gray-100 rounded px-3 py-1 text-sm font-semibold">{{ $perencanaan->no_perencanaan }}</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                        <div>
                            <p class="text-xs text-gray-400 uppercase">Tanggal Rencana</p>
                            <p class="font-semibold mt-1">{{ \Carbon\Carbon::parse($perencanaan->tanggal_perencanaan)->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase">Supplier</p>
                            <p class="font-semibold mt-1">{{ $perencanaan->supplier->nama_supplier ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase">Barang Tersisa</p>
                            <p class="font-semibold mt-1">{{ $perencanaan->details->count() }} Varian</p>
                        </div>
                    </div>

                    @if($perencanaan->catatan)
                        <div class="mt-3 bg-gray-50 rounded p-3 text-sm">
                            <p class="text-xs text-gray-400 uppercase">Catatan</p>
                            <p class="mt-1">{{ $perencanaan->catatan }}</p>
                        </div>
                    @endif
                </div>

                {{-- DAFTAR BARANG --}}
                <div class="border rounded">
                    <div class="p-4 border-b">
                        <h2 class="font-bold">Barang yang Diterima</h2>
                        <p class="text-sm text-gray-500">Isi jumlah barang yang datang sekarang. Qty tidak boleh melebihi sisa.</p>
                    </div>

                    <div class="divide-y">
                        @foreach($perencanaan->details as $index => $detail)
                            @php
                                $sisa = max(0, $detail->qty_rencana - $detail->qty_diterima);
                                $hargaDefault = old("items.$index.harga_satuan", $detail->estimasi_harga ?? 0);
                            @endphp

                            <div class="p-4" data-item-row>
                                <input type="hidden" name="items[{{ $index }}][detail_perencanaan_id]" value="{{ $detail->id }}">

                                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-3">
                                    <div>
                                        <h3 class="font-semibold">{{ $detail->varian->barang->nama_barang ?? 'Barang' }}</h3>
                                        <p class="text-sm text-gray-500 mt-1">
                                            {{ $detail->varian->warna ?? '-' }} / {{ $detail->varian->ukuran ?? '-' }}
                                        </p>
                                        @if($detail->varian->sku)
                                            <span class="inline-block mt-1 bg-gray-100 rounded px-2 py-0.5 text-xs">SKU: {{ $detail->varian->sku }}</span>
                                        @endif
                                    </div>

                                    <div class="grid grid-cols-3 gap-2 text-center text-sm">
                                        <div class="bg-gray-50 rounded px-2 py-2">
                                            <p class="text-xs text-gray-400">Rencana</p>
                                            <p class="font-bold mt-1">{{ $detail->qty_rencana }}</p>
                                        </div>
                                        <div class="bg-green-50 rounded px-2 py-2">
                                            <p class="text-xs text-green-600">Diterima</p>
                                            <p class="font-bold text-green-700 mt-1">{{ $detail->qty_diterima }}</p>
                                        </div>
                                        <div class="bg-orange-50 rounded px-2 py-2">
                                            <p class="text-xs text-orange-600">Sisa</p>
                                            <p class="font-bold text-orange-700 mt-1">{{ $sisa }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-sm font-semibold mb-1">Diterima Sekarang</label>
                                        <input type="number" name="items[{{ $index }}][qty_diterima]" value="{{ old("items.$index.qty_diterima", 0) }}" min="0" max="{{ $sisa }}" data-qty class="w-full border rounded px-3 py-2 text-sm">
                                        <p class="text-xs text-gray-400 mt-1">Maksimal {{ $sisa }} unit</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold mb-1">Harga Satuan Aktual</label>
                                        <input type="number" name="items[{{ $index }}][harga_satuan]" value="{{ $hargaDefault }}" min="0" step="0.01" data-harga class="w-full border rounded px-3 py-2 text-sm">
                                        @if($detail->estimasi_harga)
                                            <p class="text-xs text-gray-400 mt-1">Estimasi: Rp{{ number_format($detail->estimasi_harga, 0, ',', '.') }}</p>
                                        @endif
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold mb-1">Diskon</label>
                                        <input type="number" name="items[{{ $index }}][diskon]" value="{{ old("items.$index.diskon", 0) }}" min="0" step="0.01" data-diskon class="w-full border rounded px-3 py-2 text-sm">
                                        <p class="text-xs text-gray-400 mt-1">Diskon total item ini</p>
                                    </div>
                                </div>

                                <div class="mt-3 flex items-center justify-between bg-gray-50 rounded px-3 py-2 text-sm">
                                    <span class="text-gray-500">Total Item</span>
                                    <span class="font-bold" data-subtotal>Rp0</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- RIGHT --}}
            <div class="space-y-4">
                <div class="sticky top-4 space-y-4">

                    {{-- INFO PENERIMAAN --}}
                    <div class="border rounded p-4">
                        <h2 class="font-bold">Informasi Penerimaan</h2>
                        <p class="text-sm text-gray-500 mb-3">Masukkan dokumen penerimaan dari supplier.</p>

                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-semibold mb-1">No. Faktur / Surat Jalan <span class="text-red-500">*</span></label>
                                <input type="text" name="no_faktur" value="PB-{{ now()->format('YmdHis') }}" placeholder="Contoh: INV-2026-001" required class="w-full border rounded px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-1">Tanggal Penerimaan <span class="text-red-500">*</span></label>
                                <input type="date" name="tanggal_pembelian" value="{{ old('tanggal_pembelian', now()->format('Y-m-d')) }}" required class="w-full border rounded px-3 py-2 text-sm">
                            </div>
                        </div>
                    </div>

                    {{-- RINGKASAN --}}
                    <div class="border rounded p-4">
                        <h2 class="font-bold mb-3">Ringkasan Penerimaan</h2>

                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Total Qty Diterima</span>
                                <span id="totalQty" class="font-semibold">0 unit</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Brutto</span>
                                <span id="totalBrutto" class="font-semibold">Rp0</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Diskon</span>
                                <span id="totalDiskon" class="font-semibold text-red-600">Rp0</span>
                            </div>
                            <div class="flex justify-between border-t pt-2">
                                <span class="font-semibold">Total Netto</span>
                                <span id="totalNetto" class="text-lg font-bold text-blue-600">Rp0</span>
                            </div>
                        </div>

                        <p class="text-xs text-orange-700 bg-orange-50 border border-orange-200 rounded p-2 mt-3">
                            ⚠️ Stok akan bertambah setelah penerimaan dikonfirmasi. Pastikan jumlah fisik barang sudah sesuai.
                        </p>

                        <button type="submit" id="btnSubmit" class="mt-3 w-full bg-blue-600 text-white rounded px-4 py-3 text-sm font-semibold disabled:bg-gray-300 disabled:cursor-not-allowed">
                            ✓ Konfirmasi Penerimaan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const rows = document.querySelectorAll('[data-item-row]');
    const totalQtyElement = document.getElementById('totalQty');
    const totalBruttoElement = document.getElementById('totalBrutto');
    const totalDiskonElement = document.getElementById('totalDiskon');
    const totalNettoElement = document.getElementById('totalNetto');
    const btnSubmit = document.getElementById('btnSubmit');

    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
    }

    function hitungTotal() {
        let totalQty = 0;
        let totalBrutto = 0;
        let totalDiskon = 0;

        rows.forEach(function (row) {
            const qtyInput = row.querySelector('[data-qty]');
            const hargaInput = row.querySelector('[data-harga]');
            const diskonInput = row.querySelector('[data-diskon]');
            const subtotalElement = row.querySelector('[data-subtotal]');

            let qty = parseInt(qtyInput.value) || 0;
            let harga = parseFloat(hargaInput.value) || 0;
            let diskon = parseFloat(diskonInput.value) || 0;

            const maxQty = parseInt(qtyInput.max) || 0;
            if (qty > maxQty) { qty = maxQty; qtyInput.value = maxQty; }
            if (qty < 0) { qty = 0; qtyInput.value = 0; }

            const brutto = qty * harga;
            const nettoItem = Math.max(0, brutto - diskon);

            subtotalElement.textContent = formatRupiah(nettoItem);

            totalQty += qty;
            totalBrutto += brutto;

            if (qty > 0) { totalDiskon += Math.min(diskon, brutto); }
        });

        const totalNetto = Math.max(0, totalBrutto - totalDiskon);

        totalQtyElement.textContent = totalQty + ' unit';
        totalBruttoElement.textContent = formatRupiah(totalBrutto);
        totalDiskonElement.textContent = formatRupiah(totalDiskon);
        totalNettoElement.textContent = formatRupiah(totalNetto);

        btnSubmit.disabled = totalQty <= 0;
    }

    rows.forEach(function (row) {
        const inputs = row.querySelectorAll('[data-qty], [data-harga], [data-diskon]');
        inputs.forEach(function (input) { input.addEventListener('input', hitungTotal); });
    });

    hitungTotal();
});
</script>
@endsection