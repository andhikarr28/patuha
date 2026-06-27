@extends('layouts.app')

@section('content')

    <div class="flex justify-between items-center mb-6">

        <h1 class="text-3xl font-bold">
            Data Barang
        </h1>

        <a href="{{ route('barang.create') }}" class="bg-blue-600 text-white px-5 py-3 rounded-xl">

            + Tambah Barang

        </a>

    </div>

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

        <table class="w-full">

            <thead class="bg-slate-100">
                <tr>
                    <th class="px-6 py-4 text-left">No</th>
                    <th class="px-6 py-4 text-left">Nama Barang</th>
                    <th class="px-6 py-4 text-left">Kategori</th>
                    <th class="px-6 py-4 text-left">Brand</th>
                    <th class="px-6 py-4 text-left">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @foreach($barang as $item)
                    <tr class="border-t hover:bg-slate-50 transition">
                        <td class="px-6 py-4">{{ $loop->iteration }}</td>

                        <td class="px-6 py-4 font-medium">
                            {{ $item->nama_barang }}
                        </td>

                        <td class="px-6 py-4">
                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm">
                                {{ $item->kategori->nama_kategori }}
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            {{ $item->brand }}
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <a href="{{ route('barang.edit', $item) }}"
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg">
                                    Edit
                                </a>

                                <form action="{{ route('barang.destroy', $item) }}" method="POST">
                                    @csrf
                                    @method('DELETE')

                                    <button onclick="return confirm('Yakin ingin menghapus barang ini?')"
                                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>

    </div>

@endsection