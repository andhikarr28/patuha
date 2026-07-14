<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokLog extends Model
{
    protected $table = 'stok_log';

    protected $fillable = [
        'varian_id',
        'tipe_transaksi',
        'qty',
        'stok_sebelum',
        'stok_sesudah',
        'referensi',
    ];
}