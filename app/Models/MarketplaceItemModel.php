<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketplaceItemModel extends Model
{
    protected $table = 'marketplace_item_models';

    protected $fillable = [
        'marketplace_item_id',
        'model_id',
        'model_sku',
        'stok',
    ];

    public function marketplaceItem()
    {
        return $this->belongsTo(
            MarketplaceItem::class,
            'marketplace_item_id'
        );
    }
}