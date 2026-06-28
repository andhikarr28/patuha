<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketplaceSyncLog extends Model
{
    protected $table = 'marketplace_sync_logs';

    protected $fillable = [
        'marketplace_id',
        'jumlah_produk',
        'jumlah_varian',
        'sync_at',
    ];

    protected $casts = [
        'sync_at' => 'datetime',
    ];

    public function marketplace()
    {
        return $this->belongsTo(
            Marketplace::class
        );
    }
}