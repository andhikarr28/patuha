@extends('layouts.app')

@section('content')
<div class="p-4 space-y-4">

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">Data Supplier</h1>
        <a href="{{ route('supplier.create') }}" class="bg-blue-600 text-white rounded px-4 py-2 text-sm font-semibold">+ Tambah Supplier</a>
    </div>

    @if(session('success'))
        <div class="border border-green-300 bg-green-50 text-green-700 rounded p-3 text-sm">{{ session('success') }}</div>
    @endif

    <div class="border rounded">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="px-4 py-2">No</th>
                    <th class="px-4 py-2">Nama Supplier</th>
                    <th class="px-4 py-2">No HP</th>
                    <th class="px-4 py-2">Alamat</th>
                    <th class="px-4 py-2 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($supplier as $item)
                    <tr class="border-b">
                        <td class="px-4 py-3">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3">{{ $item->nama_supplier }}</td>
                        <td class="px-4 py-3">{{ $item->no_hp }}</td>
                        <td class="px-4 py-3">{{ $item->alamat }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('supplier.edit', $item) }}" class="bg-yellow-500 text-white rounded px-3 py-1 text-xs">Edit</a>
                                <form action="{{ route('supplier.destroy', $item) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus supplier ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 text-white rounded px-3 py-1 text-xs">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-8 text-gray-400">Belum ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection