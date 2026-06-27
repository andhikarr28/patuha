@extends('layouts.app')

@section('content')

<h1 class="text-3xl font-bold mb-6">

    Marketplace

</h1>

<div class="bg-white rounded-2xl shadow-lg p-6">

    <h2 class="text-xl font-bold mb-4">

        Shopee Integration

    </h2>

    <p class="text-gray-500 mb-6">

        Hubungkan sistem dengan Shopee Open Platform.

    </p>

    <a href="{{ route('shopee.auth') }}"
       class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-xl">

        Hubungkan Shopee

    </a>

</div>

@endsection