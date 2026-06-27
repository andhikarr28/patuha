@extends('layouts.app')

@section('content')

    <div class="flex justify-between items-center mb-6">

        <h1 class="text-3xl font-bold">
            Data Kategori
        </h1>

        <a href="{{ route('kategori.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg">

            + Tambah

        </a>

    </div>

    @if(session('success'))

        <div class="bg-green-100 text-green-700 p-3 rounded mb-5">
            {{ session('success') }}
        </div>

    @endif

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

        <table class="min-w-full">

            <thead class="bg-slate-100 text-slate-700 uppercase text-sm">

                <tr>

                    <th class="px-6 py-4 text-left">
                        No
                    </th>

                    <th class="px-6 py-4 text-left">
                        Nama Kategori
                    </th>

                    <th class="px-6 py-4 text-left">
                        Dibuat
                    </th>

                    <th class="px-6 py-4 text-center">
                        Aksi
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($kategori as $item)

                    <tr class="border-b hover:bg-slate-50">

                        <td class="px-6 py-4">

                            {{ $loop->iteration }}

                        </td>

                        <td class="px-6 py-4">

                            {{ $item->nama_kategori }}

                        </td>

                        <td class="px-6 py-4">

                            {{ $item->created_at->format('d M Y') }}

                        </td>

                        <td class="px-6 py-4">

                            <div class="flex justify-center gap-2">

                                <a href="{{ route('kategori.edit', $item) }}"
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg">

                                    ✏ Edit

                                </a>

                                <form action="{{ route('kategori.destroy', $item) }}" method="POST">

                                    @csrf
                                    @method('DELETE')

                                    <button onclick="return confirm('Yakin ingin menghapus kategori ini?')"
                                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">

                                        🗑 Hapus

                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="4" class="text-center py-10">

                            Belum ada data

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

@endsection