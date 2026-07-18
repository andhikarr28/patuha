<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketplaceItem extends Model
{
    protected $fillable = [
        'marketplace_id',
        'external_product_id',
        'nama_produk',
        'status',
        'berat',
        'kategori_id',
    ];

    public function models()
    {
        return $this->hasMany(
            MarketplaceItemModel::class,
            'marketplace_item_id'
        );
    }
}