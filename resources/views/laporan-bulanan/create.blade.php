@extends('layouts.app')

@section('content')
<div class="p-4 max-w-lg">

    <div class="mb-4">
        <h1 class="text-2xl font-bold">Buat Laporan Bulanan</h1>
        <p class="text-gray-500 text-sm">Pilih periode, sistem akan mengumpulkan data penjualan &amp; pembelian secara otomatis.</p>
    </div>

    @if($errors->any())
        <div class="mb-4 border border-red-300 bg-red-50 text-red-700 rounded p-4 text-sm">
            <p class="font-semibold mb-1">Laporan belum dapat dibuat.</p>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="border rounded p-4">
        <form action="{{ route('laporan-bulanan.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">Periode Awal</label>
                    <input type="date" name="periode_awal" value="{{ old('periode_awal', now()->startOfMonth()->format('Y-m-d')) }}" class="w-full border rounded px-3 py-2 text-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Periode Akhir</label>
                    <input type="date" name="periode_akhir" value="{{ old('periode_akhir', now()->endOfMonth()->format('Y-m-d')) }}" class="w-full border rounded px-3 py-2 text-sm" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1">Catatan Evaluasi</label>
                <textarea name="catatan_evaluasi" rows="4" placeholder="Tulis evaluasi Anda atas data penjualan &amp; pembelian pada periode ini..." class="w-full border rounded px-3 py-2 text-sm">{{ old('catatan_evaluasi') }}</textarea>
                <p class="text-xs text-gray-400 mt-1">Catatan ini akan dibaca owner sebagai konteks sebelum mengambil keputusan.</p>
            </div>

            <div class="border border-blue-200 bg-blue-50 rounded p-3 text-xs text-blue-700 mb-4">
                Laporan akan tersimpan sebagai <strong>draft</strong> terlebih dahulu. Anda bisa memeriksa datanya sebelum mengirim ke owner.
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 text-white rounded px-4 py-2 text-sm font-semibold">Buat Laporan (Draft)</button>
                <a href="{{ route('laporan-bulanan.index') }}" class="border rounded px-4 py-2 text-sm font-semibold">Batal</a>
            </div>
        </form>
    </div>

</div>
@endsection