@extends('layouts.app')

@section('content')
<div class="p-4 max-w-lg">

    <h1 class="text-2xl font-bold mb-4">Edit Penjualan</h1>

    <div class="border rounded p-4">
        <form action="{{ route('penjualan.update', $penjualan) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1">No Nota</label>
                <input type="text" name="no_nota" value="{{ old('no_nota', $penjualan->no_nota) }}" class="w-full border rounded px-3 py-2 text-sm">
                @error('no_nota')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1">Tanggal Penjualan</label>
                <input type="date" name="tanggal_penjualan" value="{{ old('tanggal_penjualan', $penjualan->tanggal_penjualan) }}" class="w-full border rounded px-3 py-2 text-sm">
                @error('tanggal_penjualan')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1">Channel</label>
                <select name="channel" class="w-full border rounded px-3 py-2 text-sm">
                    <option value="offline" {{ $penjualan->channel == 'offline' ? 'selected' : '' }}>Offline</option>
                    <option value="shopee" {{ $penjualan->channel == 'shopee' ? 'selected' : '' }}>Shopee</option>
                    <option value="tokopedia" {{ $penjualan->channel == 'tokopedia' ? 'selected' : '' }}>Tokopedia</option>
                    <option value="tiktok" {{ $penjualan->channel == 'tiktok' ? 'selected' : '' }}>TikTok</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 text-white rounded px-4 py-2 text-sm font-semibold">Update</button>
                <a href="{{ route('penjualan.index') }}" class="border rounded px-4 py-2 text-sm font-semibold">Kembali</a>
            </div>
        </form>
    </div>

</div>
@endsection