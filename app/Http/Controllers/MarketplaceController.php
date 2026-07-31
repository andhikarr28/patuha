<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                'Shopee berhasil terhubung'
            );
    }

    public function getShopInfo()
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

    public function syncProductsFromShopee()
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

        return back()->with(
            'success',
            'Produk Shopee berhasil disinkronkan'
        );
    }

    public function getProductInfo()
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

        return back()->with(
            'success',
            'Detail produk berhasil disimpan'
        );
    }

    public function showProducts()
    {
        $products = \App\Models\MarketplaceItem::all();

        return view(
            'marketplace.products',
            compact('products')
        );
    }

    public function showMappings()
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

    public function syncVariantsFromShopee()
    {
        $token = MarketplaceToken::first();

        $items = MarketplaceItem::all();

        $partnerId = config('services.shopee.partner_id');
        $partnerKey = config('services.shopee.partner_key');

        $jumlahVarian = 0;

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

            foreach ($response['response']['model'] as $model) {

                MarketplaceItemModel::updateOrCreate(
                    [
                        'model_id' => $model['model_id']
                    ],
                    [
                        'marketplace_item_id' => $item->id,
                        'model_sku' => $model['model_sku'],
                        'stok' => $model['stock_info_v2']
                        ['summary_info']
                        ['total_available_stock']
                            ?? 0,
                    ]
                );
                $jumlahVarian++;
            }
        }

        return back()->with(
            'success',
            $jumlahVarian .
            ' variasi Shopee berhasil disinkronkan.'
        );
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

    public function syncMarketplaceStockToLocal()
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

    public function getShopeeOrderList()
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
                'page_size' => 100,
            ]
        );

        $data = $response->json();

        $orderSnList = collect($data['response']['order_list'])
            ->pluck('order_sn')
            ->toArray();

        return $orderSnList;
    }

    public function getShopeeOrderDetails(array $orderSnList)
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



    public function syncOrdersToLocal()
    {
        $orderSnList = $this->getShopeeOrderList();

        $data = $this->getShopeeOrderDetails(
            $orderSnList
        );

        $hasil = [];

        foreach (
            $data['response']['order_list']
            as $order
        ) {

            $orderSn =
                $order['order_sn'];

            if (
                in_array(
                    $order['order_status'],
                    [
                        'CANCELLED',
                        'IN_CANCEL'
                    ]
                )
            ) {

                $exists =
                    MarketplaceOrderLog::where(
                        'order_sn',
                        $orderSn
                    )->exists();

                if (!$exists) {

                    MarketplaceOrderLog::create([
                        'order_sn' =>
                            $orderSn,

                        'status' =>
                            'CANCELLED',

                        'synced_at' =>
                            now(),
                    ]);
                }

                $hasil[] = [
                    'order_sn' =>
                        $orderSn,

                    'status' =>
                        'CANCELLED',

                    'keterangan' =>
                        'ORDER DIABAIKAN'
                ];

                continue;
            }

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
                    $varian->decrement(
                        'stok',
                        $qty
                    );

                    $stokSesudah =
                        $stokSebelum - $qty;

                    StokLog::create([
                        'varian_id' =>
                            $varian->id,

                        'tipe_transaksi' =>
                            'penjualan_shopee',

                        'qty' =>
                            $qty,

                        'stok_sebelum' =>
                            $stokSebelum,

                        'stok_sesudah' =>
                            $stokSesudah,

                        'referensi' =>
                            'SHP-' . $orderSn,
                    ]);

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

                $penjualan->update([
                    'total' =>
                        $totalPenjualan
                ]);

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

    public function syncLocalStockToShopee()
    {
        $token = MarketplaceToken::first();

        $partnerId = config('services.shopee.partner_id');
        $partnerKey = config('services.shopee.partner_key');

        $path = "/api/v2/product/update_stock";

        $hasil = [];

        $varianList = VarianBarang::all();

        foreach ($varianList as $varian) {

            $mapping = MarketplaceMapping::where(
                'varian_id',
                $varian->id
            )->first();

            if (!$mapping) {
                continue;
            }

            $itemModel = MarketplaceItemModel::find(
                $mapping->marketplace_item_model_id
            );

            if (!$itemModel) {
                continue;
            }

            $item = MarketplaceItem::find(
                $itemModel->marketplace_item_id
            );

            if (!$item) {
                continue;
            }

            $itemId = (int) $item->external_product_id;

            $modelId = (int) $itemModel->model_id;

            $stok = (int) $varian->stok;

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

            $payload = [
                'item_id' => $itemId,

                'stock_list' => [
                    [
                        'model_id' => $modelId,

                        'seller_stock' => [
                            [
                                'stock' => $stok
                            ]
                        ]
                    ]
                ]
            ];

            $url =
                "https://openplatform.sandbox.test-stable.shopee.sg{$path}"
                . "?partner_id={$partnerId}"
                . "&timestamp={$timestamp}"
                . "&access_token={$token->access_token}"
                . "&shop_id={$token->shop_id}"
                . "&sign={$sign}";

            $response = Http::post(
                $url,
                $payload
            );

            $hasil[] = [
                'varian_id' => $varian->id,
                'nama_barang' => $varian->nama_varian ?? null,
                'item_id' => $itemId,
                'model_id' => $modelId,
                'stok_lokal' => $stok,
                'response' => $response->json(),
            ];
        }

        MarketplaceSyncLog::create([
            'marketplace_id' => 1,
            'aktivitas' => 'Sync Stok',
            'arah_sync' => 'Lokal -> Shopee',
            'jumlah_produk' => MarketplaceItem::count(),
            'jumlah_varian' => count($hasil),
            'sync_at' => now(),

        ]);

        return back()->with(
            'success',
            'Sinkronisasi stok berhasil.'
        );
    }

    public function syncSingleStockToShopee(VarianBarang $varian)
    {
        $token = MarketplaceToken::first();

        $mapping = MarketplaceMapping::where('varian_id', $varian->id)->first();

        if (!$mapping) {
            return;
        }

        $itemModel = MarketplaceItemModel::find($mapping->marketplace_item_model_id);

        if (!$itemModel) {
            return;
        }

        $item = MarketplaceItem::find($itemModel->marketplace_item_id);

        if (!$item) {
            return;
        }

        $partnerId = config('services.shopee.partner_id');
        $partnerKey = config('services.shopee.partner_key');
        $path = "/api/v2/product/update_stock";
        $timestamp = time();

        $baseString =
            $partnerId .
            $path .
            $timestamp .
            $token->access_token .
            (int) $token->shop_id;

        $sign = hash_hmac('sha256', $baseString, $partnerKey);

        $itemId = (int) $item->external_product_id;
        $modelId = (int) $itemModel->model_id;
        $stok = (int) $varian->stok;

        $payload = [
            'item_id' => $itemId,
            'stock_list' => [
                [
                    'model_id' => $modelId,
                    'seller_stock' => [
                        ['stock' => $stok]
                    ]
                ]
            ]
        ];

        $url =
            "https://openplatform.sandbox.test-stable.shopee.sg{$path}"
            . "?partner_id={$partnerId}"
            . "&timestamp={$timestamp}"
            . "&access_token={$token->access_token}"
            . "&shop_id={$token->shop_id}"
            . "&sign={$sign}";

        $response = Http::timeout(10)
            ->retry(2, 200)
            ->post($url, $payload);

        $body = $response->json();

        Log::info('Realtime Sync Shopee', [
            'varian_id' => $varian->id,
            'payload' => $payload,
            'status' => $response->status(),
            'response' => $body,
        ]);

        // Shopee sering balikin HTTP 200 walau gagal, errornya ada di body.
        if ($response->failed() || !empty($body['error'])) {
            throw new \RuntimeException(
                'Shopee sync gagal untuk varian_id ' . $varian->id .
                ': ' . ($body['message'] ?? $body['error'] ?? 'unknown error')
            );
        }
    }
}