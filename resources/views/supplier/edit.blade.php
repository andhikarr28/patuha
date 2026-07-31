@extends('layouts.app')

@section('content')
<div class="p-4 max-w-lg">

    <h1 class="text-2xl font-bold mb-4">Edit Supplier</h1>

    <div class="border rounded p-4">
        <form action="{{ route('supplier.update', $supplier) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1">Nama Supplier</label>
                <input type="text" name="nama_supplier" value="{{ old('nama_supplier', $supplier->nama_supplier) }}" class="w-full border rounded px-3 py-2 text-sm" required>
                @error('nama_supplier')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1">No HP</label>
                <input type="text" name="no_hp" value="{{ old('no_hp', $supplier->no_hp) }}" class="w-full border rounded px-3 py-2 text-sm">
                @error('no_hp')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1">Alamat</label>
                <textarea name="alamat" rows="3" class="w-full border rounded px-3 py-2 text-sm">{{ old('alamat', $supplier->alamat) }}</textarea>
                @error('alamat')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 text-white rounded px-4 py-2 text-sm font-semibold">Update</button>
                <a href="{{ route('supplier.index') }}" class="border rounded px-4 py-2 text-sm font-semibold">Kembali</a>
            </div>
        </form>
    </div>

</div>
@endsection