@extends('layouts.app')

@section('content')

    <div class="bg-white p-6 rounded shadow">

        <div class="flex justify-between items-center mb-4">

            <h2 class="text-xl font-bold">
                Mapping SKU Shopee
            </h2>

            <form action="{{ route('marketplace.sync.stocks') }}" method="POST">
                @csrf

                <button class="bg-green-500 text-white px-4 py-2 rounded">
                    Sinkronisasi Stok
                </button>
            </form>

        </div>

        <table class="w-full border">

            <thead>
                <tr>
                    <th class="border p-2">
                        SKU Shopee
                    </th>

                    <th class="border p-2">
                        Varian Sistem
                    </th>

                    <th class="border p-2">
                        Aksi
                    </th>
                </tr>
            </thead>

            <tbody>

                @foreach($models as $model)

                    <tr>

                        <td class="border p-2">
                            {{ $model->model_sku }}
                        </td>

                        <td class="border p-2">

                            <form action="{{ route('marketplace.mappings.store') }}" method="POST">

                                @csrf

                                <input type="hidden" name="marketplace_item_model_id" value="{{ $model->id }}">

                                <select name="varian_id" class="border p-2">

                                    @foreach($varians as $varian)

                                        <option value="{{ $varian->id }}">
                                            {{ $varian->sku }}
                                        </option>

                                    @endforeach

                                </select>

                        </td>

                        <td class="border p-2">

                            <button class="bg-blue-500 text-white px-3 py-1 rounded">
                                Simpan
                            </button>

                            </form>

                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    </div>

@endsection