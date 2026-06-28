@extends('layouts.app')

@section('content')

<div class="bg-white rounded-lg shadow p-6">

    <h1 class="text-2xl font-bold mb-4">
        Produk Shopee
    </h1>

    <table class="w-full border">

        <thead>
            <tr class="bg-gray-100">
                <th class="p-3 border">ID Shopee</th>
                <th class="p-3 border">Nama Produk</th>
                <th class="p-3 border">Status</th>
                <th class="p-3 border">Berat</th>
            </tr>
        </thead>

        <tbody>

            @foreach($products as $product)

            <tr>

                <td class="p-3 border">
                    {{ $product->external_product_id }}
                </td>

                <td class="p-3 border">
                    {{ $product->nama_produk }}
                </td>

                <td class="p-3 border">
                    {{ $product->status }}
                </td>

                <td class="p-3 border">
                    {{ $product->berat }}
                </td>

            </tr>

            @endforeach

        </tbody>

    </table>

</div>

@endsection