@extends('layouts.app')

@section('content')

<div class="max-w-3xl mx-auto">

    <div class="bg-white p-8 rounded-2xl shadow-lg">

        <h1 class="text-3xl font-bold mb-6">

            Tambah Penjualan

        </h1>

        <form action="{{ route('penjualan.store') }}"
              method="POST">

            @csrf

            <div class="mb-4">

                <label>No Nota</label>

                <input
                    type="text"
                    name="no_nota"
                    class="w-full border rounded-lg p-3">

            </div>

            <div class="mb-4">

                <label>Tanggal Penjualan</label>

                <input
                    type="date"
                    name="tanggal_penjualan"
                    class="w-full border rounded-lg p-3">

            </div>

            <div class="mb-4">

                <label>Channel</label>

                <select
                    name="channel"
                    class="w-full border rounded-lg p-3">

                    <option value="offline">Offline</option>
                    <option value="shopee">Shopee</option>
                    <option value="tokopedia">Tokopedia</option>
                    <option value="tiktok">TikTok</option>

                </select>

            </div>

            <button
                class="bg-blue-600 text-white px-5 py-3 rounded-xl">

                Simpan

            </button>

        </form>

    </div>

</div>

@endsection