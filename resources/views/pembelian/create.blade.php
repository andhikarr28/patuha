@extends('layouts.app')

@section('content')

    <div class="max-w-7xl mx-auto">

        <form id="formPembelian" action="{{ route('pembelian.store') }}" method="POST">

            @csrf

            <div class="grid grid-cols-12 gap-6">

                {{-- KIRI --}}
                <div class="col-span-8">

                    <div class="bg-white p-6 rounded-2xl shadow-lg">

                        <h1 class="text-3xl font-bold mb-6">
                            POS Pembelian
                        </h1>

                        <div class="grid md:grid-cols-2 gap-4 mb-6">

                            <div>

                                <label class="block mb-2">
                                    No Nota
                                </label>

                                <input type="text" name="no_faktur" value="PB-{{ now()->format('YmdHis') }}"
                                    class="w-full border rounded-lg p-3">

                            </div>

                            <div>

                                <label class="block mb-2">
                                    Tanggal
                                </label>

                                <input type="date" name="tanggal_pembelian" value="{{ date('Y-m-d') }}"
                                    class="w-full border rounded-lg p-3">

                            </div>

                            <div class="col-span-12">

                                <label class="block mb-2">
                                    Supplier
                                </label>

                                <select name="supplier_id" class="w-full border rounded-lg p-3">

                                    @foreach($supplier as $item)

                                        <option value="{{ $item->id }}">
                                            {{ $item->nama_supplier }}
                                        </option>

                                    @endforeach

                                </select>

                            </div>

                        </div>

                        <hr class="my-6">

                        <h2 class="text-xl font-bold mb-4">
                            Tambah Barang
                        </h2>

                        <div class="grid grid-cols-12 gap-3">

                            <div class="col-span-12">

                                <select id="varian" class="w-full border rounded-lg p-3">

                                    @foreach($varian as $item)

                                        <option value="{{ $item->id }}" data-harga="{{ $item->harga_beli ?? 0 }}"
                                            data-nama="{{ $item->barang->nama_barang }} - {{ $item->warna }} - {{ $item->ukuran }}">

                                            {{ $item->barang->nama_barang }}
                                            -
                                            {{ $item->warna }}
                                            -
                                            {{ $item->ukuran }}
                                            (Stok {{ $item->stok }})

                                        </option>

                                    @endforeach

                                </select>

                            </div>

                            <div class="col-span-2">
                                <input type="number" id="qty" value="1" min="1" placeholder="Qty"
                                    class="w-full border rounded-lg p-3">
                            </div>

                            <div class="col-span-4">
                                <input type="number" id="harga_beli" placeholder="Harga Beli"
                                    class="w-full border rounded-lg p-3">
                            </div>

                            <div class="col-span-2">
                                <input type="number" id="diskon" value="0" placeholder="Diskon"
                                    class="w-full border rounded-lg p-3">
                            </div>

                            <div class="col-span-4">
                                <button type="button" id="btnTambah" class="w-full bg-green-600 text-white rounded-lg p-3">

                                    Tambah

                                </button>
                            </div>

                        </div>

                    </div>

                </div>

                {{-- KANAN --}}
                <div class="col-span-4">

                    <div class="bg-white p-6 rounded-2xl shadow-lg">

                        <h2 class="text-xl font-bold mb-4">
                            Keranjang
                        </h2>

                        <div id="cartItems">

                            <p class="text-gray-500">
                                Belum ada barang
                            </p>

                        </div>

                        <hr class="my-5">

                        <div class="flex justify-between text-xl font-bold">

                            <span>Total</span>

                            <span id="grandTotal">
                                Rp 0
                            </span>

                        </div>

                        <button type="submit" class="w-full mt-6 bg-blue-600 text-white p-4 rounded-xl">

                            Simpan Pembelian

                        </button>

                    </div>

                </div>

            </div>

        </form>

    </div>

    <script>

        let cart = [];
        function updateHargaBeli() {

            let select =
                document.getElementById('varian');

            let option =
                select.options[
                select.selectedIndex
                ];

            document.getElementById(
                'harga_beli'
            ).value =
                option.dataset.harga || 0;
        }

        updateHargaBeli();

        document
            .getElementById('varian')
            .addEventListener(
                'change',
                updateHargaBeli
            );

        const btnTambah =
            document.getElementById('btnTambah');

        const cartItems =
            document.getElementById('cartItems');

        const grandTotal =
            document.getElementById('grandTotal');

        const form =
            document.getElementById(
                'formPembelian'
            );

        btnTambah.addEventListener('click', function () {

            let select =
                document.getElementById('varian');

            let qty =
                parseInt(
                    document.getElementById('qty').value
                );

            let option =
                select.options[
                select.selectedIndex
                ];

            let varianId =
                option.value;

            let harga =
                parseInt(
                    option.dataset.harga
                );

            let nama =
                option.dataset.nama;

            let hargaBeli =
                parseInt(
                    document.getElementById('harga_beli').value
                );

            let diskon =
                parseInt(
                    document.getElementById('diskon').value
                ) || 0;

            cart.push({
                varian_id: varianId,
                nama: nama,
                qty: qty,
                harga_beli: hargaBeli,
                diskon: diskon,
                subtotal: (hargaBeli * qty) - diskon
            });

            renderCart();
        });

        function renderCart() {
            window.cart = cart;
            console.log(cart);

            let html = '';

            let total = 0;

            document
                .querySelectorAll('.cart-hidden')
                .forEach(el => el.remove());

            cart.forEach(function (item, index) {

                total += item.subtotal;

                html += `
                                                            <div class="border-b py-3">

                                                                <div class="font-semibold">
                                                                    ${item.nama}
                                                                </div>

                                                                <div>
                                                                    ${item.qty} x Rp ${item.harga_beli.toLocaleString()}
                                                                </div>

                                                                <div class="font-bold">
                                                                    Rp ${item.subtotal.toLocaleString()}
                                                                </div>

                                                                <button
                                                                    type="button"
                                                                    onclick="hapusItem(${index})"
                                                                    class="text-red-600 text-sm mt-1">

                                                                    Hapus

                                                                </button>

                                                            </div>
                                                        `;

                form.insertAdjacentHTML(
                    'beforeend',
                    `
                                                            <input
                                                                class="cart-hidden"
                                                                type="hidden"
                                                                name="cart[${index}][varian_id]"
                                                                value="${item.varian_id}">

                                                            <input
                                                                class="cart-hidden"
                                                                type="hidden"
                                                                name="cart[${index}][qty]"
                                                                value="${item.qty}">

                                                            <input
                                                                class="cart-hidden"
                                                                type="hidden"
                                                                name="cart[${index}][harga_beli]"
                                                                value="${item.harga_beli}">

                                                            <input
                                                                class="cart-hidden"
                                                                type="hidden"
                                                                name="cart[${index}][diskon]"
                                                                value="${item.diskon}">

                                                            `
                );

            });

            cartItems.innerHTML = html;

            grandTotal.innerHTML =
                'Rp ' +
                total.toLocaleString();
        }

        function hapusItem(index) {
            cart.splice(index, 1);

            renderCart();
        }

    </script>

@endsection