<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketplaceMapping extends Model
{
    protected $fillable = [
        'marketplace_item_model_id',
        'varian_id',
    ];

    public function marketplaceItemModel()
    {
        return $this->belongsTo(
            MarketplaceItemModel::class
        );
    }

    public function varian()
    {
        return $this->belongsTo(
            VarianBarang::class,
            'varian_id'
        );
    }
}