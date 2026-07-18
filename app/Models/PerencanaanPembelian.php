<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerencanaanPembelian extends Model
{
    protected $table = 'perencanaan_pembelian';

    protected $fillable = [
        'no_perencanaan',
        'tanggal_perencanaan',
        'supplier_id',
        'status',
        'catatan',
        'user_id',
    ];

    protected $casts = [
        'tanggal_perencanaan' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Supplier
    |--------------------------------------------------------------------------
    */
    public function supplier()
    {
        return $this->belongsTo(
            Supplier::class,
            'supplier_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | User yang membuat perencanaan
    |--------------------------------------------------------------------------
    */
    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Detail barang yang direncanakan
    |--------------------------------------------------------------------------
    */
    public function details()
    {
        return $this->hasMany(
            DetailPerencanaanPembelian::class,
            'perencanaan_pembelian_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Penerimaan / Pembelian Aktual
    |--------------------------------------------------------------------------
    |
    | Satu perencanaan bisa memiliki beberapa penerimaan.
    |
    | Contoh:
    | Rencana 10 barang
    | - Penerimaan pertama 6
    | - Penerimaan kedua 4
    |
    */
    public function pembelian()
    {
        return $this->hasMany(
            Pembelian::class,
            'perencanaan_pembelian_id'
        );
    }
}