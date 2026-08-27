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
use App\Services\ShopeeService;

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

        $token = MarketplaceToken::first();
        $tokenStatus = [
            'status' => 'tidak_tersedia',
            'label' => 'Token tidak tersedia',
            'detail' => 'Hubungkan akun Shopee untuk membuat access token.',
        ];

        if ($token) {
            $expiredAt = $token->expired_at
                ? \Carbon\Carbon::parse($token->expired_at)
                : null;

            $tokenStatus = $expiredAt && $expiredAt->lte(now())
                ? [
                    'status' => 'kedaluwarsa',
                    'label' => 'Access token kedaluwarsa',
                    'detail' => 'Sistem akan mencoba refresh otomatis saat proses Shopee dijalankan. Jika refresh gagal, hubungkan ulang akun Shopee.',
                ]
                : [
                    'status' => 'aktif',
                    'label' => 'Access token tersedia',
                    'detail' => $expiredAt
                        ? 'Berlaku sampai ' . $expiredAt->format('d M Y H:i') . ' WIB. Terakhir diperbarui ' . $token->updated_at->format('d M Y H:i') . ' WIB.'
                        : 'Masa berlaku belum tercatat; token akan diperiksa saat proses Shopee dijalankan.',
                ];
        }

        return view(
            'marketplace.index',
            compact(
                'jumlahProduk',
                'jumlahVarian',
                'jumlahMapping',
                'lastSync',
                'marketplace',
                'tokenStatus'
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

        $response = Http::timeout(30)->post(
            "https://openplatform.sandbox.test-stable.shopee.sg/api/v2/auth/token/get?partner_id={$partnerId}&timestamp={$timestamp}&sign={$sign}",
            [
                'code' => $request->code,
                'shop_id' => (int) $request->shop_id,
                'partner_id' => (int) $partnerId,
            ]
        );

        $body = $response->json();

        if (
            $response->failed() ||
            !empty($body['error']) ||
            empty($body['access_token']) ||
            empty($body['refresh_token']) ||
            empty($body['expire_in'])
        ) {
            Log::warning('Authorization Shopee gagal', [
                'status' => $response->status(),
                'response' => $body,
            ]);

            return redirect()
                ->route('marketplace.index')
                ->with('error', 'Authorization Shopee gagal. Silakan hubungkan ulang akun Shopee.');
        }

        MarketplaceToken::updateOrCreate(
            [
                'shop_id' => $request->shop_id
            ],
            [
                'access_token' =>
                    $body['access_token'],

                'refresh_token' =>
                    $body['refresh_token'],

                'expired_at' =>
                    now()->addSeconds(
                        $body['expire_in']
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
        $token = $this->getValidShopeeToken();

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
        $token = $this->getValidShopeeToken();

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
        $token = $this->getValidShopeeToken();

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
        $models = MarketplaceItemModel::with('mapping.varian.barang')
            ->orderBy('model_id')
            ->get();

        $varians = VarianBarang::with('barang')
            ->orderBy('sku')
            ->get();

        $jumlahSkuLokal = $varians
            ->filter(fn($varian) => trim((string) $varian->sku) !== '')
            ->groupBy(fn($varian) => $this->normaliseSku($varian->sku))
            ->map->count();

        foreach ($models as $model) {
            $model->mapping_status = $this->mappingStatus($model, $jumlahSkuLokal);
        }

        return view('marketplace.mapping', compact('models', 'varians'));
    }

    public function syncVariantsFromShopee()
    {
        try {
            $token = $this->getValidShopeeToken();
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        $items = MarketplaceItem::all();

        $partnerId = config('services.shopee.partner_id');
        $partnerKey = config('services.shopee.partner_key');

        $jumlahVarian = 0;
        $hasilMapping = [
            'AUTO MAPPED' => 0,
            'SUDAH MAPPING' => 0,
            'SKU TIDAK DITEMUKAN' => 0,
            'SKU KOSONG' => 0,
            'SKU AMBIGU' => 0,
        ];

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

                $itemModel = MarketplaceItemModel::updateOrCreate(
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
                $statusMapping = $this->autoMapMarketplaceModelBySku($itemModel);
                $hasilMapping[$statusMapping]++;
                $jumlahVarian++;
            }
        }

        return back()
            ->with(
                'success',
                $jumlahVarian . ' varian berhasil disinkronkan • ' .
                $hasilMapping['AUTO MAPPED'] . ' auto-mapping baru • ' .
                $hasilMapping['SUDAH MAPPING'] . ' sudah ter-mapping.'
            )
            ->with('sync_mapping_results', $hasilMapping);
    }

    public function storeMapping(Request $request)
    {
        $validated = $request->validate([
            'marketplace_item_model_id' => 'required|integer|exists:marketplace_item_models,id',
            'varian_id' => 'required|integer|exists:varian_barang,id',
        ]);

        MarketplaceMapping::updateOrCreate(
            [
                'marketplace_item_model_id' =>
                    $validated['marketplace_item_model_id']
            ],
            [
                'varian_id' =>
                    $validated['varian_id']
            ]
        );

        return back()
            ->with(
                'success',
                'Mapping berhasil disimpan'
            );
    }

    protected function autoMapMarketplaceModelBySku(MarketplaceItemModel $itemModel): string
    {
        if (MarketplaceMapping::where('marketplace_item_model_id', $itemModel->id)->exists()) {
            return 'SUDAH MAPPING';
        }

        $sku = trim((string) $itemModel->model_sku);

        if ($sku === '') {
            return 'SKU KOSONG';
        }

        $varians = VarianBarang::whereRaw(
            'UPPER(TRIM(sku)) = ?',
            [$this->normaliseSku($sku)]
        )->get();

        if ($varians->isEmpty()) {
            return 'SKU TIDAK DITEMUKAN';
        }

        if ($varians->count() > 1) {
            return 'SKU AMBIGU';
        }

        $mapping = MarketplaceMapping::firstOrCreate(
            ['marketplace_item_model_id' => $itemModel->id],
            ['varian_id' => $varians->first()->id]
        );

        return $mapping->wasRecentlyCreated
            ? 'AUTO MAPPED'
            : 'SUDAH MAPPING';
    }

    protected function mappingStatus(MarketplaceItemModel $model, $jumlahSkuLokal): string
    {
        if ($model->mapping) {
            return 'SUDAH MAPPING';
        }

        $sku = trim((string) $model->model_sku);

        if ($sku === '') {
            return 'SKU KOSONG';
        }

        $jumlahCocok = $jumlahSkuLokal[$this->normaliseSku($sku)] ?? 0;

        if ($jumlahCocok === 0) {
            return 'SKU TIDAK DITEMUKAN';
        }

        return $jumlahCocok > 1 ? 'SKU AMBIGU' : 'BELUM MAPPING';
    }

    protected function normaliseSku(?string $sku): string
    {
        return strtoupper(trim((string) $sku));
    }

    protected function getValidShopeeToken(): MarketplaceToken
    {
        return app(ShopeeService::class)->getToken();
    }

    public function syncMarketplaceStockToLocal()
    {
        try {
            $token = $this->getValidShopeeToken();
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        $partnerId = config('services.shopee.partner_id');
        $partnerKey = config('services.shopee.partner_key');
        $path = '/api/v2/product/get_model_list';
        $jumlahDiperbarui = 0;
        $jumlahGagal = 0;

        $modelsPerItem = MarketplaceItemModel::with([
            'marketplaceItem',
            'mapping.varian',
        ])->get()->groupBy('marketplace_item_id');

        foreach ($modelsPerItem as $models) {
            $item = $models->first()->marketplaceItem;

            if (!$item) {
                continue;
            }

            $timestamp = time();
            $baseString = $partnerId . $path . $timestamp . $token->access_token . (int) $token->shop_id;
            $sign = hash_hmac('sha256', $baseString, $partnerKey);

            $response = Http::timeout(10)
                ->retry(2, 200)
                ->get(
                    "https://openplatform.sandbox.test-stable.shopee.sg{$path}",
                    [
                        'partner_id' => $partnerId,
                        'timestamp' => $timestamp,
                        'access_token' => $token->access_token,
                        'shop_id' => (int) $token->shop_id,
                        'item_id' => (int) $item->external_product_id,
                        'sign' => $sign,
                    ]
                );

            $body = $response->json();

            if ($response->failed() || !empty($body['error']) || !isset($body['response']['model'])) {
                $jumlahGagal++;

                Log::warning('Sync stok Shopee ke lokal gagal', [
                    'item_id' => $item->external_product_id,
                    'status' => $response->status(),
                    'response' => $body,
                ]);

                continue;
            }

            $stokShopeePerModel = collect($body['response']['model'])
                ->keyBy(fn ($model) => (string) $model['model_id']);

            foreach ($models as $itemModel) {
                $mapping = $itemModel->mapping;
                $modelShopee = $stokShopeePerModel->get((string) $itemModel->model_id);

                if (!$mapping || !$mapping->varian || !$modelShopee) {
                    continue;
                }

                $stokShopee = (int) data_get(
                    $modelShopee,
                    'stock_info_v2.summary_info.total_available_stock',
                    -1
                );

                if ($stokShopee < 0) {
                    continue;
                }

                $itemModel->update(['stok' => $stokShopee]);
                $mapping->varian->update(['stok' => $stokShopee]);
                $jumlahDiperbarui++;
            }
        }

        $marketplace = Marketplace::first();

        if ($marketplace) {

            MarketplaceSyncLog::create([
                'marketplace_id' => $marketplace->id,
                'aktivitas' => 'Sync Stok',
                'arah_sync' => 'Shopee -> Lokal',
                'jumlah_produk' => MarketplaceItem::count(),
                'jumlah_varian' => $jumlahDiperbarui,
                'sync_at' => now(),
            ]);

            $marketplace->update([
                'last_sync' => now()
            ]);
        }

        if ($jumlahGagal > 0) {
            return back()->with(
                'error',
                "Sinkronisasi Shopee ke Lokal selesai: {$jumlahDiperbarui} varian diperbarui, {$jumlahGagal} produk gagal diambil. Periksa token Shopee atau log aplikasi."
            );
        }

        return back()->with(
            'success',
            "Sinkronisasi Shopee ke Lokal berhasil. {$jumlahDiperbarui} varian diperbarui."
        );
    }

    public function syncProducts()
    {
        try {
            $token = $this->getValidShopeeToken();
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

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

        $body = $response->json();

        if (
            $response->failed() ||
            !empty($body['error']) ||
            !isset($body['response']['item']) ||
            !is_array($body['response']['item'])
        ) {
            Log::warning('Sinkron produk Shopee gagal', [
                'status' => $response->status(),
                'response' => $body,
            ]);

            return back()->with(
                'error',
                'Produk Shopee tidak dapat diambil. Periksa token Shopee dan koneksi API, lalu coba lagi.'
            );
        }

        $items = $body['response']['item'];

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
        try {
            $token = $this->getValidShopeeToken();
        } catch (\RuntimeException $e) {
            Log::warning('Detail produk Shopee gagal diambil', [
                'item_id' => $itemId,
                'message' => $e->getMessage(),
            ]);

            return false;
        }

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

        $body = $response->json();
        $item = $body['response']['item_list'][0] ?? null;

        if ($response->failed() || !empty($body['error']) || !$item) {
            Log::warning('Detail produk Shopee gagal diambil', [
                'item_id' => $itemId,
                'status' => $response->status(),
                'response' => $body,
            ]);

            return false;
        }

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

        return true;
    }

    public function getShopeeOrderList()
    {
        $token = $this->getValidShopeeToken();

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
        $token = $this->getValidShopeeToken();

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
        return $this->importShopeeOrdersAtomically();
    }

    protected function importShopeeOrdersAtomically()
    {
        try {
            $orderSnList = $this->getShopeeOrderList();
            $data = $this->getShopeeOrderDetails($orderSnList);
        } catch (\Throwable $e) {
            Log::error('Pengambilan order Shopee gagal', ['message' => $e->getMessage()]);

            return back()->with('error', 'Order Shopee tidak dapat diambil. Periksa koneksi dan token Shopee.');
        }

        $hasil = [];

        foreach ($data['response']['order_list'] ?? [] as $order) {
            $orderSn = $order['order_sn'] ?? null;

            if (!$orderSn) {
                Log::warning('Order Shopee dilewati karena order_sn tidak tersedia.');
                continue;
            }

            if (in_array($order['order_status'] ?? null, ['CANCELLED', 'IN_CANCEL'], true)) {
                try {
                    $hasil[] = $this->handleCancelledShopeeOrder($orderSn);
                } catch (\Throwable $e) {
                    Log::warning('Pembatalan order Shopee gagal', [
                        'order_sn' => $orderSn,
                        'message' => $e->getMessage(),
                    ]);
                    $hasil[] = [
                        'order_sn' => $orderSn,
                        'status' => 'GAGAL',
                        'keterangan' => 'Pembatalan order tidak dapat diproses.',
                    ];
                }
                continue;
            }

            if (MarketplaceOrderLog::where('order_sn', $orderSn)->exists()) {
                $hasil[] = ['order_sn' => $orderSn, 'status' => 'SUDAH PERNAH SYNC'];
                continue;
            }

            try {
                $ringkasan = DB::transaction(function () use ($order, $orderSn) {
                    if (MarketplaceOrderLog::where('order_sn', $orderSn)->lockForUpdate()->exists()) {
                        throw new \RuntimeException('Order sudah pernah disinkronkan.');
                    }

                    $itemsValid = [];
                    $variansTerkunci = [];
                    $qtyPerVarian = [];

                    foreach ($order['item_list'] ?? [] as $item) {
                        $modelId = $item['model_id'] ?? null;
                        $qty = (int) ($item['model_quantity_purchased'] ?? 0);

                        if (!$modelId || $qty < 1) {
                            throw new \RuntimeException('Item order Shopee memiliki model atau qty yang tidak valid.');
                        }

                        $itemModel = MarketplaceItemModel::where('model_id', $modelId)->first();
                        if (!$itemModel) {
                            throw new \RuntimeException("Model Shopee {$modelId} tidak ditemukan.");
                        }

                        $mapping = MarketplaceMapping::where(
                            'marketplace_item_model_id',
                            $itemModel->id
                        )->first();
                        if (!$mapping) {
                            throw new \RuntimeException("Model Shopee {$modelId} belum memiliki mapping.");
                        }

                        if (!isset($variansTerkunci[$mapping->varian_id])) {
                            $variansTerkunci[$mapping->varian_id] = VarianBarang::with('barang')
                                ->whereKey($mapping->varian_id)
                                ->lockForUpdate()
                                ->first();
                        }

                        $varian = $variansTerkunci[$mapping->varian_id];
                        if (!$varian) {
                            throw new \RuntimeException("Varian lokal untuk model Shopee {$modelId} tidak ditemukan.");
                        }

                        $qtyPerVarian[$varian->id] = ($qtyPerVarian[$varian->id] ?? 0) + $qty;

                        if ((int) $varian->stok < $qtyPerVarian[$varian->id]) {
                            throw new \RuntimeException("Stok {$varian->sku} tidak mencukupi untuk order {$orderSn}.");
                        }

                        $itemsValid[] = [
                            'varian' => $varian,
                            'qty' => $qty,
                            'harga' => (float) ($item['model_discounted_price'] ?? 0),
                        ];
                    }

                    if (empty($itemsValid)) {
                        throw new \RuntimeException('Order Shopee tidak memiliki item yang dapat diproses.');
                    }

                    $penjualan = Penjualan::create([
                        'no_nota' => 'SHP-' . $orderSn,
                        'tanggal_penjualan' => now(),
                        'channel' => 'shopee',
                        'total' => 0,
                        'user_id' => auth()->id(),
                        'metode_pembayaran' => $order['payment_method'] ?? 'Shopee',
                    ]);

                    $totalPenjualan = 0;

                    foreach ($itemsValid as $item) {
                        $varian = $item['varian'];
                        $qty = $item['qty'];
                        $harga = $item['harga'];
                        $subtotal = $qty * $harga;
                        $stokSebelum = (int) $varian->stok;
                        $stokSesudah = $stokSebelum - $qty;

                        DetailPenjualan::create([
                            'penjualan_id' => $penjualan->id,
                            'varian_id' => $varian->id,
                            'qty' => $qty,
                            'harga' => $harga,
                            'subtotal' => $subtotal,
                        ]);

                        $varian->decrement('stok', $qty);
                        $varian->stok = $stokSesudah;

                        StokLog::create([
                            'varian_id' => $varian->id,
                            'tipe_transaksi' => 'penjualan',
                            'qty' => $qty,
                            'stok_sebelum' => $stokSebelum,
                            'stok_sesudah' => $stokSesudah,
                            'referensi' => 'SHP-' . $orderSn,
                        ]);

                        $totalPenjualan += $subtotal;
                    }

                    $penjualan->update(['total' => $totalPenjualan]);
                    MarketplaceOrderLog::create([
                        'order_sn' => $orderSn,
                        'status' => $order['order_status'] ?? 'UNKNOWN',
                        'synced_at' => now(),
                    ]);

                    return ['penjualan_id' => $penjualan->id, 'jumlah_item' => count($itemsValid)];
                });

                $hasil[] = [
                    'order_sn' => $orderSn,
                    'status' => 'BERHASIL',
                    'keterangan' => "{$ringkasan['jumlah_item']} item tersimpan pada transaksi #{$ringkasan['penjualan_id']}.",
                ];
            } catch (\Throwable $e) {
                Log::warning('Import order Shopee ditolak', [
                    'order_sn' => $orderSn,
                    'message' => $e->getMessage(),
                ]);
                $hasil[] = ['order_sn' => $orderSn, 'status' => 'GAGAL', 'keterangan' => $e->getMessage()];
            }
        }

        $berhasil = collect($hasil)->where('status', 'BERHASIL')->count();
        $gagal = collect($hasil)->where('status', 'GAGAL')->count();

        return back()
            ->with($gagal > 0 ? 'error' : 'success', "Sinkronisasi order selesai: {$berhasil} berhasil, {$gagal} gagal.")
            ->with('sync_order_results', $hasil);
    }

    protected function handleCancelledShopeeOrder(string $orderSn): array
    {
        return DB::transaction(function () use ($orderSn) {
            $orderLog = MarketplaceOrderLog::where('order_sn', $orderSn)
                ->lockForUpdate()
                ->first();

            if (!$orderLog) {
                MarketplaceOrderLog::create([
                    'order_sn' => $orderSn,
                    'status' => 'CANCELLED',
                    'synced_at' => now(),
                ]);

                return [
                    'order_sn' => $orderSn,
                    'status' => 'DILEWATI',
                    'keterangan' => 'Order dibatalkan sebelum pernah diimport.',
                ];
            }

            if ($orderLog->status === 'CANCELLED_REVERSED') {
                return [
                    'order_sn' => $orderSn,
                    'status' => 'DILEWATI',
                    'keterangan' => 'Pembatalan sudah pernah diproses.',
                ];
            }

            $penjualan = Penjualan::where('no_nota', 'SHP-' . $orderSn)
                ->lockForUpdate()
                ->first();

            if (!$penjualan) {
                $orderLog->update([
                    'status' => 'CANCELLED',
                    'synced_at' => now(),
                ]);

                return [
                    'order_sn' => $orderSn,
                    'status' => 'DILEWATI',
                    'keterangan' => 'Order tidak memiliki transaksi lokal.',
                ];
            }

            $jumlahDikembalikan = 0;

            foreach ($penjualan->detailPenjualan()->lockForUpdate()->get() as $detail) {
                $varian = VarianBarang::whereKey($detail->varian_id)
                    ->lockForUpdate()
                    ->firstOrFail();
                $stokSebelum = (int) $varian->stok;
                $qty = (int) $detail->qty;
                $stokSesudah = $stokSebelum + $qty;

                $varian->update(['stok' => $stokSesudah]);

                StokLog::create([
                    'varian_id' => $varian->id,
                    'tipe_transaksi' => 'penyesuaian',
                    'qty' => $qty,
                    'stok_sebelum' => $stokSebelum,
                    'stok_sesudah' => $stokSesudah,
                    'referensi' => 'CANCEL-SHP-' . $orderSn,
                ]);

                $jumlahDikembalikan += $qty;
            }

            $orderLog->update([
                'status' => 'CANCELLED_REVERSED',
                'synced_at' => now(),
            ]);

            return [
                'order_sn' => $orderSn,
                'status' => 'BERHASIL',
                'keterangan' => "Order dibatalkan; stok {$jumlahDikembalikan} unit dikembalikan.",
            ];
        });
    }

    public function syncLocalStockToShopee()
    {
        try {
            $token = $this->getValidShopeeToken();
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        $partnerId = config('services.shopee.partner_id');
        $partnerKey = config('services.shopee.partner_key');

        $path = "/api/v2/product/update_stock";

        $jumlahDiperbarui = 0;
        $jumlahGagal = 0;

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

            $response = Http::timeout(10)
                ->retry(2, 200)
                ->post($url, $payload);

            $body = $response->json();

            if ($response->failed() || !empty($body['error'])) {
                $jumlahGagal++;

                Log::warning('Sync stok lokal ke Shopee gagal', [
                    'varian_id' => $varian->id,
                    'item_id' => $itemId,
                    'model_id' => $modelId,
                    'status' => $response->status(),
                    'response' => $body,
                ]);

                continue;
            }

            $jumlahDiperbarui++;
        }

        $marketplace = Marketplace::first();

        if ($marketplace) {
            MarketplaceSyncLog::create([
                'marketplace_id' => $marketplace->id,
                'aktivitas' => 'Sync Stok',
                'arah_sync' => 'Lokal -> Shopee',
                'jumlah_produk' => MarketplaceItem::count(),
                'jumlah_varian' => $jumlahDiperbarui,
                'sync_at' => now(),
            ]);

            $marketplace->update(['last_sync' => now()]);
        }

        if ($jumlahGagal > 0) {
            return back()->with(
                'error',
                "Sinkronisasi Lokal ke Shopee selesai: {$jumlahDiperbarui} varian diperbarui, {$jumlahGagal} varian gagal dikirim. Periksa token Shopee atau log aplikasi."
            );
        }

        return back()->with(
            'success',
            "Sinkronisasi Lokal ke Shopee berhasil. {$jumlahDiperbarui} varian diperbarui."
        );
    }

    public function syncSingleStockToShopee(VarianBarang $varian)
    {
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

        $token = $this->getValidShopeeToken();

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
