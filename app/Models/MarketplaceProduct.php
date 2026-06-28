<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketplaceProduct extends Model
{
    protected $table = 'marketplace_product';

    protected $fillable = [
        'varian_id',
        'marketplace_id',
        'external_product_id',
        'external_sku',
    ];
}