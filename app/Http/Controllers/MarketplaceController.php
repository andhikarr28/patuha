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
use App\Services\ShopeeService;

class MarketplaceController extends Controller
{

    private ShopeeService $shopeeService;

    public function __construct(
        ShopeeService $shopeeService
    ) {
        $this->shopeeService =
            $shopeeService;
    }
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
        try {

            $items =
                MarketplaceItem::all();

            $jumlahVarian = 0;

            foreach ($items as $item) {

                $data =
                    $this->shopeeService
                        ->getModelList(
                            $item->external_product_id
                        );

                $models =
                    $data['response']['model']
                    ?? [];

                foreach ($models as $model) {

                    MarketplaceItemModel::updateOrCreate(
                        [
                            'model_id' =>
                                $model['model_id'],
                        ],
                        [
                            'marketplace_item_id' =>
                                $item->id,

                            'model_sku' =>
                                $model['model_sku']
                                ?? null,

                            'stok' =>
                                $model['stock_info_v2']
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

        } catch (\Throwable $e) {

            return back()->with(
                'error',
                $e->getMessage()
            );
        }
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
            'aktivitas' => 'Sync Stok',
            'arah_sync' => 'Shopee -> Lokal',
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
        try {

            $data =
                $this->shopeeService
                    ->getItemList();

            $items =
                $data['response']['item']
                ?? [];

            foreach ($items as $itemData) {

                $itemId =
                    $itemData['item_id']
                    ?? null;

                if (!$itemId) {
                    continue;
                }

                $this->saveItemDetail(
                    $itemId
                );
            }

            return back()->with(
                'success',
                count($items) .
                ' produk Shopee berhasil disinkronkan.'
            );

        } catch (\Throwable $e) {

            return back()->with(
                'error',
                $e->getMessage()
            );
        }
    }


    private function saveItemDetail(
        int|string $itemId
    ): void {

        $data =
            $this->shopeeService
                ->getItemBaseInfo(
                    $itemId
                );

        $item =
            $data['response']['item_list'][0]
            ?? null;

        if (!$item) {
            return;
        }

        MarketplaceItem::updateOrCreate(
            [
                'external_product_id' =>
                    $item['item_id'],
            ],
            [
                'marketplace_id' =>
                    1,

                'nama_produk' =>
                    $item['item_name']
                    ?? '-',

                'status' =>
                    $item['item_status']
                    ?? null,

                'berat' =>
                    $item['weight']
                    ?? 0,

                'kategori_id' =>
                    $item['category_id']
                    ?? null,
            ]
        );
    }

    private function getShopeeOrderList(): array
    {
        $data =
            $this->shopeeService
                ->getOrderList();

        return collect(
            $data['response']['order_list']
            ?? []
        )
            ->pluck('order_sn')
            ->filter()
            ->values()
            ->toArray();
    }

    private function getShopeeOrderDetails(
        array $orderSnList
    ): array {

        return $this->shopeeService
            ->getOrderDetails(
                $orderSnList
            );
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

            /*
            |--------------------------------------------------------------------------
            | Abaikan Order Cancelled
            |--------------------------------------------------------------------------
            */
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

    public function syncLocalStockToShopee()
    {
        try {

            /*
            |--------------------------------------------------------------------------
            | Ambil Varian Beserta Mapping Shopee
            |--------------------------------------------------------------------------
            */

            $varianList =
                VarianBarang::with([
                    'barang'
                ])
                    ->get();

            $jumlahBerhasil = 0;

            $jumlahDilewati = 0;

            $jumlahGagal = 0;

            $errors = [];


            /*
            |--------------------------------------------------------------------------
            | Proses Setiap Varian
            |--------------------------------------------------------------------------
            */

            foreach (
                $varianList as $varian
            ) {

                /*
                |--------------------------------------------------------------------------
                | Cari Mapping
                |--------------------------------------------------------------------------
                */

                $mapping =
                    MarketplaceMapping::where(
                        'varian_id',
                        $varian->id
                    )
                        ->first();

                if (!$mapping) {

                    $jumlahDilewati++;

                    continue;
                }


                /*
                |--------------------------------------------------------------------------
                | Cari Model Shopee
                |--------------------------------------------------------------------------
                */

                $itemModel =
                    MarketplaceItemModel::find(
                        $mapping
                            ->marketplace_item_model_id
                    );

                if (!$itemModel) {

                    $jumlahGagal++;

                    $errors[] =
                        "Varian {$varian->id}: " .
                        "Marketplace model tidak ditemukan.";

                    continue;
                }


                /*
                |--------------------------------------------------------------------------
                | Cari Produk Shopee
                |--------------------------------------------------------------------------
                */

                $item =
                    MarketplaceItem::find(
                        $itemModel
                            ->marketplace_item_id
                    );

                if (!$item) {

                    $jumlahGagal++;

                    $errors[] =
                        "Varian {$varian->id}: " .
                        "Marketplace item tidak ditemukan.";

                    continue;
                }


                /*
                |--------------------------------------------------------------------------
                | Data Sinkronisasi
                |--------------------------------------------------------------------------
                */

                $itemId =
                    (int) 
                    $item
                        ->external_product_id;

                $modelId =
                    (int) 
                    $itemModel
                        ->model_id;

                $stok =
                    max(
                        0,
                        (int) $varian->stok
                    );


                /*
                |--------------------------------------------------------------------------
                | Kirim Stok ke Shopee
                |--------------------------------------------------------------------------
                */

                try {

                    $response =
                        $this
                            ->shopeeService
                            ->updateStock(
                                $itemId,
                                $modelId,
                                $stok
                            );


                    /*
                    |--------------------------------------------------------------------------
                    | Cek Response Shopee
                    |--------------------------------------------------------------------------
                    */

                    if (
                        !empty(
                        $response['error']
                    )
                    ) {

                        throw new \RuntimeException(
                            $response['message']
                            ?? $response['error']
                        );
                    }


                    $jumlahBerhasil++;

                } catch (\Throwable $e) {

                    $jumlahGagal++;

                    $namaBarang =
                        $varian->barang
                                ?->nama_barang
                        ?? "Varian {$varian->id}";

                    $errors[] =
                        "{$namaBarang}: " .
                        $e->getMessage();
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Simpan Log Sinkronisasi
            |--------------------------------------------------------------------------
            */

            MarketplaceSyncLog::create([

                'marketplace_id' =>
                    1,

                'aktivitas' =>
                    'Sync Stok',

                'arah_sync' =>
                    'Lokal -> Shopee',

                'jumlah_produk' =>
                    MarketplaceItem::count(),

                'jumlah_varian' =>
                    $jumlahBerhasil,

                'sync_at' =>
                    now(),

            ]);


            /*
            |--------------------------------------------------------------------------
            | Buat Pesan Hasil
            |--------------------------------------------------------------------------
            */

            $message =
                "Sinkronisasi stok selesai. " .
                "Berhasil: {$jumlahBerhasil}, " .
                "Dilewati: {$jumlahDilewati}, " .
                "Gagal: {$jumlahGagal}.";


            if (
                $jumlahGagal > 0
            ) {

                return back()
                    ->with(
                        'warning',
                        $message
                    )
                    ->with(
                        'sync_errors',
                        $errors
                    );
            }


            return back()->with(
                'success',
                $message
            );

        } catch (\Throwable $e) {

            return back()->with(
                'error',
                'Sinkronisasi stok gagal: ' .
                $e->getMessage()
            );
        }
    }
}