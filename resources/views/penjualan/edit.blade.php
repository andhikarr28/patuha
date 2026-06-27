@extends('layouts.app')

@section('content')

<div class="max-w-3xl mx-auto">

    <div class="bg-white p-8 rounded-2xl shadow-lg">

        <h1 class="text-3xl font-bold mb-6">

            Edit Penjualan

        </h1>

        <form action="{{ route('penjualan.update',$penjualan) }}"
              method="POST">

            @csrf
            @method('PUT')

            <div class="mb-4">

                <label>No Nota</label>

                <input
                    type="text"
                    name="no_nota"
                    value="{{ $penjualan->no_nota }}"
                    class="w-full border rounded-lg p-3">

            </div>

            <div class="mb-4">

                <label>Tanggal Penjualan</label>

                <input
                    type="date"
                    name="tanggal_penjualan"
                    value="{{ $penjualan->tanggal_penjualan }}"
                    class="w-full border rounded-lg p-3">

            </div>

            <div class="mb-4">

                <label>Channel</label>

                <select
                    name="channel"
                    class="w-full border rounded-lg p-3">

                    <option value="offline" {{ $penjualan->channel == 'offline' ? 'selected' : '' }}>Offline</option>

                    <option value="shopee" {{ $penjualan->channel == 'shopee' ? 'selected' : '' }}>Shopee</option>

                    <option value="tokopedia" {{ $penjualan->channel == 'tokopedia' ? 'selected' : '' }}>Tokopedia</option>

                    <option value="tiktok" {{ $penjualan->channel == 'tiktok' ? 'selected' : '' }}>TikTok</option>

                </select>

            </div>

            <button
                class="bg-blue-600 text-white px-5 py-3 rounded-xl">

                Update

            </button>

        </form>

    </div>

</div>

@endsection