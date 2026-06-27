@extends('layouts.app')

@section('content')

<div class="flex justify-between items-center mb-6">

    <h1 class="text-3xl font-bold">
        Data Supplier
    </h1>

    <a href="{{ route('supplier.create') }}"
       class="bg-blue-600 text-white px-5 py-3 rounded-xl">

        + Tambah Supplier

    </a>

</div>

<div class="bg-white rounded-2xl shadow-lg overflow-hidden">

    <table class="w-full">

        <thead class="bg-slate-100">

            <tr>

                <th class="px-6 py-4 text-left">No</th>
                <th class="px-6 py-4 text-left">Nama Supplier</th>
                <th class="px-6 py-4 text-left">No HP</th>
                <th class="px-6 py-4 text-left">Alamat</th>
                <th class="px-6 py-4 text-center">Aksi</th>

            </tr>

        </thead>

        <tbody>

            @foreach($supplier as $item)

            <tr class="border-t">

                <td class="px-6 py-4">
                    {{ $loop->iteration }}
                </td>

                <td class="px-6 py-4">
                    {{ $item->nama_supplier }}
                </td>

                <td class="px-6 py-4">
                    {{ $item->no_hp }}
                </td>

                <td class="px-6 py-4">
                    {{ $item->alamat }}
                </td>

                <td class="px-6 py-4">

                    <div class="flex justify-center gap-2">

                        <a href="{{ route('supplier.edit',$item) }}"
                           class="bg-yellow-500 text-white px-4 py-2 rounded-lg">

                            Edit

                        </a>

                        <form action="{{ route('supplier.destroy',$item) }}"
                              method="POST">

                            @csrf
                            @method('DELETE')

                            <button
                                class="bg-red-600 text-white px-4 py-2 rounded-lg">

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