<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\MarketplaceToken;
use App\Models\MarketplaceItem;
use App\Models\VarianBarang;
use App\Models\MarketplaceItemModel;
use App\Models\MarketplaceMapping;
use App\Models\MarketplaceSyncLog;
use App\Models\Marketplace;
use App\Models\MarketplaceOrderLog;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\StokLog;

class MarketplaceController extends Controller
{
    public function index()
    {
        $jumlahProduk =
            MarketplaceItem::count();

        $jumlahVarian =
            MarketplaceItemModel::count();

        $jumlahMapping =
            MarketplaceMapping::count();

        $lastSync =
            MarketplaceSyncLog::latest()
                ->first();

        $marketplace =
            Marketplace::first();

        return view(
            'marketplace.index',
            compact(
                'jumlahProduk',
                'jumlahVarian',
                'jumlahMapping',
                'lastSync',
                'marketplace'
            )
        );
    }
    public function auth()
    {
        $partnerId = config(
            'services.shopee.partner_id'
        );

        $redirectUri = urlencode(
            config(
                'services.shopee.redirect_url'
            )
        );

        $url =
            "https://open.sandbox.test-stable.shopee.com/auth" .
            "?partner_id={$partnerId}" .
            "&auth_type=seller" .
            "&redirect_uri={$redirectUri}" .
            "&response_type=code";

        return redirect($url);
    }

    public function callback(Request $request)
    {
        $partnerId = config('services.shopee.partner_id');
        $partnerKey = config('services.shopee.partner_key');

        $path = "/api/v2/auth/token/get";

        $timestamp = time();

        $baseString =
            $partnerId .
            $path .
            $timestamp;

        $sign = hash_hmac(
            'sha256',
            $baseString,
            $partnerKey
        );

        $response = Http::post(
            "https://openplatform.sandbox.test-stable.shopee.sg/api/v2/auth/token/get?partner_id={$partnerId}&timestamp={$timestamp}&sign={$sign}",
            [
                'code' => $request->code,
                'shop_id' => (int) $request->shop_id,
                'partner_id' => (int) $partnerId,
            ]
        );

        MarketplaceToken::updateOrCreate(
            [
                'shop_id' => $request->shop_id
            ],
            [
                'access_token' =>
                    $response['access_token'],

                'refresh_token' =>
                    $response['refresh_token'],

                'expired_at' =>
                    now()->addSeconds(
                        $response['expire_in']
                    ),
            ]
        );

        dd('Token berhasil disimpan');
    }

    public function shopInfo()
    {
        $token = MarketplaceToken::first();

        $partnerId = config('services.shopee.partner_id');
        $partnerKey = config('services.shopee.partner_key');

        $path = "/api/v2/shop/get_shop_info";
        $timestamp = time();

        $baseString =
            $partnerId .
            $path .
            $timestamp .
            $token->access_token .
            (int) $token->shop_id;

        $sign = hash_hmac(
            'sha256',
            $baseString,
            $partnerKey
        );

        $response = Http::get(
            "https://openplatform.sandbox.test-stable.shopee.sg{$path}",
            [
                'partner_id' => $partnerId,
                'timestamp' => $timestamp,
                'access_token' => $token->access_token,
                'shop_id' => (int) $token->shop_id,
                'sign' => $sign,
            ]
        );

        dd(
            $response->status(),
            $response->json()
        );
    }

    public function getItems()
    {
        $token = MarketplaceToken::first();

        $partnerId = config('services.shopee.partner_id');
        $partnerKey = config('services.shopee.partner_key');

        $path = "/api/v2/product/get_item_list";

        $timestamp = time();

        $baseString =
            $partnerId .
            $path .
            $timestamp .
            $token->access_token .
            (int) $token->shop_id;

        $sign = hash_hmac(
            'sha256',
            $baseString,
            $partnerKey
        );

        $response = Http::get(
            "https://openplatform.sandbox.test-stable.shopee.sg{$path}",
            [
                'partner_id' => $partnerId,
                'timestamp' => $timestamp,
                'access_token' => $token->access_token,
                'shop_id' => (int) $token->shop_id,
                'sign' => $sign,

                'offset' => 0,
                'page_size' => 100,
                'item_status' => 'NORMAL',
            ]
        );

        dd(
            $response['response']
        );
    }

    public function itemInfo()
    {
        $token = MarketplaceToken::first();

        $partnerId = config('services.shopee.partner_id');
        $partnerKey = config('services.shopee.partner_key');

        $path = "/api/v2/product/get_item_base_info";

        $timestamp = time();

        $baseString =
            $partnerId .
            $path .
            $timestamp .
            $token->access_token .
            (int) $token->shop_id;

        $sign = hash_hmac(
            'sha256',
            $baseString,
            $partnerKey
        );

        $response = Http::get(
            "https://openplatform.sandbox.test-stable.shopee.sg{$path}",
            [
                'partner_id' => $partnerId,
                'timestamp' => $timestamp,
                'access_token' => $token->access_token,
                'shop_id' => (int) $token->shop_id,
                'item_id_list' => 802023572,
                'sign' => $sign,
            ]
        );

        $item =
            $response['response']['item_list'][0];

        MarketplaceItem::updateOrCreate(
            [
                'external_product_id' =>
                    $item['item_id']
            ],
            [
                'marketplace_id' => 1,

                'nama_produk' =>
                    $item['item_name'],

                'status' =>
                    $item['item_status'],

                'berat' =>
                    $item['weight'],

                'kategori_id' =>
                    $item['category_id'],
            ]
        );

        dd('Produk berhasil disimpan');
    }

    public function products()
    {
        $products = \App\Models\MarketplaceItem::all();

        return view(
            'marketplace.products',
            compact('products')
        );
    }

    public function mapping()
    {
        $models = MarketplaceItemModel::all();

        $varians = VarianBarang::all();

        return view(
            'marketplace.mapping',
            compact(
                'models',
                'varians'
            )
        );
    }

    public function getModels()
    {
        $token = MarketplaceToken::first();

        $items = MarketplaceItem::all();

        $partnerId = config('services.shopee.partner_id');
        $partnerKey = config('services.shopee.partner_key');

        foreach ($items as $item) {

            $path = "/api/v2/product/get_model_list";

            $timestamp = time();

            $baseString =
                $partnerId .
                $path .
                $timestamp .
                $token->access_token .
                $token->shop_id;

            $sign = hash_hmac(
                'sha256',
                $baseString,
                $partnerKey
            );

            $response = Http::get(
                "https://openplatform.sandbox.test-stable.shopee.sg{$path}",
                [
                    'partner_id' => $partnerId,
                    'timestamp' => $timestamp,
                    'access_token' => $token->access_token,
                    'shop_id' => $token->shop_id,
                    'item_id' => $item->external_product_id,
                    'sign' => $sign,
                ]
            );

            if (
                !isset($response['response']['model'])
            ) {
                continue;
            }

            foreach (
                $response['response']['model']
                as $model
            ) {

                MarketplaceItemModel::updateOrCreate(
                    [
                        'model_id' =>
                            $model['model_id']
                    ],
                    [
                        'marketplace_item_id' =>
                            $item->id,

                        'model_sku' =>
                            $model['model_sku'],

                        'stok' =>
                            $model['stock_info_v2']
                            ['summary_info']
                            ['total_available_stock']
                            ?? 0,
                    ]
                );
            }
        }

        dd('Semua model berhasil disimpan');
    }

    public function storeMapping(Request $request)
    {
        MarketplaceMapping::updateOrCreate(
            [
                'marketplace_item_model_id' =>
                    $request->marketplace_item_model_id
            ],
            [
                'varian_id' =>
                    $request->varian_id
            ]
        );

        return back()
            ->with(
                'success',
                'Mapping berhasil disimpan'
            );
    }

    public function syncStock()
    {
        $models = MarketplaceItemModel::all();

        foreach ($models as $model) {

            $mapping = MarketplaceMapping::where(
                'marketplace_item_model_id',
                $model->id
            )->first();

            if (!$mapping) {
                continue;
            }

            VarianBarang::where(
                'id',
                $mapping->varian_id
            )->update([
                        'stok' => $model->stok
                    ]);
        }

        MarketplaceSyncLog::create([
            'marketplace_id' => 1,
            'jumlah_produk' => MarketplaceItem::count(),
            'jumlah_varian' => MarketplaceItemModel::count(),
            'sync_at' => now(),
        ]);

        Marketplace::where('id', 1)
            ->update([
                'last_sync' => now()
            ]);

        return back()->with(
            'success',
            'Sinkronisasi stok berhasil'
        );
    }

    public function syncProducts()
    {
        $token = MarketplaceToken::first();

        $partnerId = config('services.shopee.partner_id');
        $partnerKey = config('services.shopee.partner_key');

        /*
        =====================
        GET ITEM LIST
        =====================
        */

        $path = "/api/v2/product/get_item_list";

        $timestamp = time();

        $baseString =
            $partnerId .
            $path .
            $timestamp .
            $token->access_token .
            (int) $token->shop_id;

        $sign = hash_hmac(
            'sha256',
            $baseString,
            $partnerKey
        );

        $response = Http::get(
            "https://openplatform.sandbox.test-stable.shopee.sg{$path}",
            [
                'partner_id' => $partnerId,
                'timestamp' => $timestamp,
                'access_token' => $token->access_token,
                'shop_id' => (int) $token->shop_id,
                'sign' => $sign,
                'offset' => 0,
                'page_size' => 100,
                'item_status' => 'NORMAL',
            ]
        );

        $items = $response['response']['item'];

        foreach ($items as $itemData) {

            $this->saveItemDetail(
                $itemData['item_id']
            );
        }

        return back()->with(
            'success',
            'Produk Shopee berhasil disinkronkan'
        );
    }


    private function saveItemDetail($itemId)
    {
        $token = MarketplaceToken::first();

        $partnerId = config('services.shopee.partner_id');
        $partnerKey = config('services.shopee.partner_key');

        $path = "/api/v2/product/get_item_base_info";

        $timestamp = time();

        $baseString =
            $partnerId .
            $path .
            $timestamp .
            $token->access_token .
            (int) $token->shop_id;

        $sign = hash_hmac(
            'sha256',
            $baseString,
            $partnerKey
        );

        $response = Http::get(
            "https://openplatform.sandbox.test-stable.shopee.sg{$path}",
            [
                'partner_id' => $partnerId,
                'timestamp' => $timestamp,
                'access_token' => $token->access_token,
                'shop_id' => (int) $token->shop_id,
                'item_id_list' => $itemId,
                'sign' => $sign,
            ]
        );

        $item =
            $response['response']['item_list'][0];

        MarketplaceItem::updateOrCreate(
            [
                'external_product_id' =>
                    $item['item_id']
            ],
            [
                'marketplace_id' => 1,
                'nama_produk' => $item['item_name'],
                'status' => $item['item_status'],
                'berat' => $item['weight'],
                'kategori_id' => $item['category_id'],
            ]
        );
    }

    public function getOrder()
    {
        $token = MarketplaceToken::first();

        $partnerId = config('services.shopee.partner_id');
        $partnerKey = config('services.shopee.partner_key');

        $path = "/api/v2/order/get_order_list";

        $timestamp = time();

        $baseString =
            $partnerId .
            $path .
            $timestamp .
            $token->access_token .
            (int) $token->shop_id;

        $sign = hash_hmac(
            'sha256',
            $baseString,
            $partnerKey
        );

        $response = Http::get(
            "https://openplatform.sandbox.test-stable.shopee.sg{$path}",
            [
                'partner_id' => $partnerId,
                'timestamp' => $timestamp,
                'access_token' => $token->access_token,
                'shop_id' => (int) $token->shop_id,
                'sign' => $sign,
                'time_range_field' => 'create_time',
                'time_from' => strtotime('-7 days'),
                'time_to' => time(),
                'page_size' => 10,
            ]
        );

        $data = $response->json();

        $orderSnList = collect($data['response']['order_list'])
            ->pluck('order_sn')
            ->toArray();

        return $orderSnList;
    }

    public function getOrderDetail(array $orderSnList)
    {
        $token = MarketplaceToken::first();

        $partnerId = config('services.shopee.partner_id');
        $partnerKey = config('services.shopee.partner_key');

        $path = "/api/v2/order/get_order_detail";

        $timestamp = time();

        $baseString =
            $partnerId .
            $path .
            $timestamp .
            $token->access_token .
            (int) $token->shop_id;

        $sign = hash_hmac(
            'sha256',
            $baseString,
            $partnerKey
        );

        $response = Http::get(
            "https://openplatform.sandbox.test-stable.shopee.sg{$path}",
            [
                'partner_id' => $partnerId,
                'timestamp' => $timestamp,
                'access_token' => $token->access_token,
                'shop_id' => (int) $token->shop_id,
                'sign' => $sign,
                'order_sn_list' => implode(',', $orderSnList),
                'response_optional_fields' =>
                    'item_list,total_amount,payment_method'
            ]
        );
        return $response->json();
    }



    public function syncOrder()
    {
        $orderSnList = $this->getOrder();

        $data = $this->getOrderDetail(
            $orderSnList
        );

        $hasil = [];

        foreach (
            $data['response']['order_list']
            as $order
        ) {

            $orderSn =
                $order['order_sn'];

            /*
            |--------------------------------------------------------------------------
            | Cek Sudah Pernah Sync
            |--------------------------------------------------------------------------
            */
            $alreadySynced =
                MarketplaceOrderLog::where(
                    'order_sn',
                    $orderSn
                )->exists();

            if ($alreadySynced) {

                $hasil[] = [
                    'order_sn' => $orderSn,
                    'status' => 'SUDAH PERNAH SYNC'
                ];

                continue;
            }

            DB::beginTransaction();

            try {

                /*
                |--------------------------------------------------------------------------
                | Buat Header Penjualan
                |--------------------------------------------------------------------------
                */
                $penjualan =
                    Penjualan::create([
                        'no_nota' =>
                            'SHP-' . $orderSn,

                        'tanggal_penjualan' =>
                            now(),

                        'channel' =>
                            'shopee',

                        'total' =>
                            0,

                        'user_id' =>
                            1,

                        'metode_pembayaran' =>
                            $order['payment_method']
                            ?? 'Shopee',
                    ]);

                $totalPenjualan = 0;

                foreach (
                    $order['item_list']
                    as $item
                ) {

                    $modelId =
                        $item['model_id'];

                    $itemModel =
                        MarketplaceItemModel::where(
                            'model_id',
                            $modelId
                        )->first();

                    if (!$itemModel) {
                        continue;
                    }

                    $mapping =
                        MarketplaceMapping::where(
                            'marketplace_item_model_id',
                            $itemModel->id
                        )->first();

                    if (!$mapping) {
                        continue;
                    }

                    $varian =
                        VarianBarang::with('barang')
                            ->find(
                                $mapping->varian_id
                            );

                    if (!$varian) {
                        continue;
                    }

                    $qty =
                        $item['model_quantity_purchased'];

                    $harga =
                        $item['model_discounted_price'];

                    $subtotal =
                        $qty * $harga;

                    $stokSebelum =
                        $varian->stok;

                    /*
                    |--------------------------------------------------------------------------
                    | Detail Penjualan
                    |--------------------------------------------------------------------------
                    */
                    DetailPenjualan::create([
                        'penjualan_id' =>
                            $penjualan->id,

                        'varian_id' =>
                            $varian->id,

                        'qty' =>
                            $qty,

                        'harga' =>
                            $harga,

                        'subtotal' =>
                            $subtotal,
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | Kurangi Stok
                    |--------------------------------------------------------------------------
                    */
                    $varian->decrement(
                        'stok',
                        $qty
                    );

                    $stokSesudah =
                        $stokSebelum - $qty;

                    /*
                    |--------------------------------------------------------------------------
                    | Stok Log
                    |--------------------------------------------------------------------------
                    */
                    StokLog::create([
                        'varian_id' =>
                            $varian->id,

                        'tipe_transaksi' =>
                            'penjualan',

                        'qty' =>
                            $qty,

                        'stok_sebelum' =>
                            $stokSebelum,

                        'stok_sesudah' =>
                            $stokSesudah,

                        'referensi' =>
                            'SHP-' . $orderSn,
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | Hitung Total
                    |--------------------------------------------------------------------------
                    */
                    $totalPenjualan +=
                        $subtotal;

                    $hasil[] = [
                        'order_sn' =>
                            $orderSn,

                        'nama_barang' =>
                            $varian
                                ->barang
                                ->nama_barang,

                        'qty' =>
                            $qty,

                        'harga' =>
                            $harga,

                        'subtotal' =>
                            $subtotal,

                        'stok_sebelum' =>
                            $stokSebelum,

                        'stok_sesudah' =>
                            $stokSesudah,
                    ];
                }

                /*
                |--------------------------------------------------------------------------
                | Update Total Header
                |--------------------------------------------------------------------------
                */
                $penjualan->update([
                    'total' =>
                        $totalPenjualan
                ]);

                /*
                |--------------------------------------------------------------------------
                | Marketplace Order Log
                |--------------------------------------------------------------------------
                */
                MarketplaceOrderLog::create([
                    'order_sn' =>
                        $orderSn,

                    'status' =>
                        $order['order_status'],

                    'synced_at' =>
                        now(),
                ]);

                DB::commit();

            } catch (\Exception $e) {

                DB::rollBack();

                $hasil[] = [
                    'order_sn' =>
                        $orderSn,

                    'error' =>
                        $e->getMessage()
                ];
            }
        }

        return response()->json(
            $hasil
        );
    }
}