<?php

namespace App\Services;

use App\Models\MarketplaceToken;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ShopeeService
{
    private string $partnerId;

    private string $partnerKey;

    private string $baseUrl;

    public function __construct()
    {
        $this->partnerId = (string) config(
            'services.shopee.partner_id'
        );

        $this->partnerKey = (string) config(
            'services.shopee.partner_key'
        );

        $this->baseUrl =
            'https://openplatform.sandbox.test-stable.shopee.sg';

        if (
            empty($this->partnerId) ||
            empty($this->partnerKey)
        ) {
            throw new RuntimeException(
                'Konfigurasi Shopee belum lengkap.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Get Token
    |--------------------------------------------------------------------------
    |
    | Mengambil token Shopee yang tersimpan di database.
    |
    */

    public function getToken(): MarketplaceToken
    {
        $token = MarketplaceToken::first();

        if (!$token) {
            throw new RuntimeException(
                'Marketplace belum terhubung dengan Shopee.'
            );
        }

        return $token;
    }

    /*
    |--------------------------------------------------------------------------
    | Generate Sign
    |--------------------------------------------------------------------------
    */

    public function generateSign(
        string $path,
        int $timestamp,
        ?string $accessToken = null,
        int|string|null $shopId = null
    ): string {

        $baseString =
            $this->partnerId .
            $path .
            $timestamp;

        /*
        | Untuk endpoint selain auth,
        | signature membutuhkan:
        |
        | partner_id
        | path
        | timestamp
        | access_token
        | shop_id
        */

        if (
            $accessToken !== null &&
            $shopId !== null
        ) {

            $baseString .=
                $accessToken .
                (int) $shopId;
        }

        return hash_hmac(
            'sha256',
            $baseString,
            $this->partnerKey
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Auth Sign
    |--------------------------------------------------------------------------
    */

    public function generateAuthSign(
        string $path,
        int $timestamp
    ): string {

        return $this->generateSign(
            $path,
            $timestamp
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Authenticated GET
    |--------------------------------------------------------------------------
    */

    public function get(
        string $path,
        array $parameters = []
    ): Response {

        $token = $this->getToken();

        $timestamp = time();

        $sign = $this->generateSign(
            $path,
            $timestamp,
            $token->access_token,
            $token->shop_id
        );

        $query = array_merge(
            [
                'partner_id' =>
                    (int) $this->partnerId,

                'timestamp' =>
                    $timestamp,

                'access_token' =>
                    $token->access_token,

                'shop_id' =>
                    (int) $token->shop_id,

                'sign' =>
                    $sign,
            ],
            $parameters
        );

        $response = Http::timeout(30)
            ->get(
                $this->baseUrl . $path,
                $query
            );

        $this->validateResponse(
            $response
        );

        return $response;
    }

    /*
    |--------------------------------------------------------------------------
    | Authenticated POST
    |--------------------------------------------------------------------------
    */

    public function post(
        string $path,
        array $payload = []
    ): Response {

        $token = $this->getToken();

        $timestamp = time();

        $sign = $this->generateSign(
            $path,
            $timestamp,
            $token->access_token,
            $token->shop_id
        );

        $url =
            $this->baseUrl .
            $path .
            '?' .
            http_build_query([
                'partner_id' =>
                    (int) $this->partnerId,

                'timestamp' =>
                    $timestamp,

                'access_token' =>
                    $token->access_token,

                'shop_id' =>
                    (int) $token->shop_id,

                'sign' =>
                    $sign,
            ]);

        $response = Http::timeout(30)
            ->post(
                $url,
                $payload
            );

        $this->validateResponse(
            $response
        );

        return $response;
    }

    /*
    |--------------------------------------------------------------------------
    | Get Item List
    |--------------------------------------------------------------------------
    */

    public function getItemList(): array
    {
        $response = $this->get(
            '/api/v2/product/get_item_list',
            [
                'offset' => 0,

                'page_size' => 100,

                'item_status' =>
                    'NORMAL',
            ]
        );

        return $response->json();
    }

    /*
    |--------------------------------------------------------------------------
    | Get Item Base Info
    |--------------------------------------------------------------------------
    */

    public function getItemBaseInfo(
        int|string $itemId
    ): array {

        $response = $this->get(
            '/api/v2/product/get_item_base_info',
            [
                'item_id_list' =>
                    $itemId,
            ]
        );

        return $response->json();
    }

    /*
    |--------------------------------------------------------------------------
    | Get Model List
    |--------------------------------------------------------------------------
    */

    public function getModelList(
        int|string $itemId
    ): array {

        $response = $this->get(
            '/api/v2/product/get_model_list',
            [
                'item_id' =>
                    $itemId,
            ]
        );

        return $response->json();
    }

    /*
    |--------------------------------------------------------------------------
    | Get Order List
    |--------------------------------------------------------------------------
    */

    public function getOrderList(
        int $days = 7
    ): array {

        $response = $this->get(
            '/api/v2/order/get_order_list',
            [
                'time_range_field' =>
                    'create_time',

                'time_from' =>
                    now()
                        ->subDays($days)
                        ->timestamp,

                'time_to' =>
                    now()->timestamp,

                'page_size' =>
                    100,
            ]
        );

        return $response->json();
    }

    /*
    |--------------------------------------------------------------------------
    | Get Order Detail
    |--------------------------------------------------------------------------
    */

    public function getOrderDetails(
        array $orderSnList
    ): array {

        if (empty($orderSnList)) {
            return [
                'response' => [
                    'order_list' => []
                ]
            ];
        }

        $response = $this->get(
            '/api/v2/order/get_order_detail',
            [
                'order_sn_list' =>
                    implode(
                        ',',
                        $orderSnList
                    ),

                'response_optional_fields' =>
                    'item_list,total_amount,payment_method',
            ]
        );

        return $response->json();
    }

    /*
    |--------------------------------------------------------------------------
    | Update Stock
    |--------------------------------------------------------------------------
    */

    public function updateStock(
        int $itemId,
        int $modelId,
        int $stock
    ): array {

        $response = $this->post(
            '/api/v2/product/update_stock',
            [
                'item_id' =>
                    $itemId,

                'stock_list' => [
                    [
                        'model_id' =>
                            $modelId,

                        'seller_stock' => [
                            [
                                'stock' =>
                                    $stock,
                            ]
                        ],
                    ]
                ],
            ]
        );

        return $response->json();
    }

    /*
    |--------------------------------------------------------------------------
    | Validate Shopee Response
    |--------------------------------------------------------------------------
    */

    private function validateResponse(
        Response $response
    ): void {

        if ($response->failed()) {

            throw new RuntimeException(
                'Request Shopee gagal. HTTP ' .
                $response->status()
            );
        }

        $data = $response->json();

        if (!is_array($data)) {

            throw new RuntimeException(
                'Response Shopee tidak valid.'
            );
        }

        if (!empty($data['error'])) {

            $message =
                $data['message']
                ?? $data['error'];

            throw new RuntimeException(
                'Shopee API: ' .
                $message
            );
        }
    }
}