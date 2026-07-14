<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketplaceOrderLog extends Model
{
    protected $fillable = [
        'order_sn',
        'status',
        'synced_at',
    ];
}
