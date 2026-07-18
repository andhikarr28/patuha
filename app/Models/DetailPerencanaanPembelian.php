<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPerencanaanPembelian extends Model
{
    protected $table = 'detail_perencanaan_pembelian';

    protected $fillable = [
        'perencanaan_pembelian_id',
        'varian_id',
        'qty_rencana',
        'estimasi_harga',
        'qty_diterima',
    ];

    protected $casts = [
        'qty_rencana' => 'integer',
        'qty_diterima' => 'integer',
        'estimasi_harga' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Header Perencanaan
    |--------------------------------------------------------------------------
    */
    public function perencanaanPembelian()
    {
        return $this->belongsTo(
            PerencanaanPembelian::class,
            'perencanaan_pembelian_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Varian Barang
    |--------------------------------------------------------------------------
    */
    public function varian()
    {
        return $this->belongsTo(
            VarianBarang::class,
            'varian_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Sisa barang yang belum diterima
    |--------------------------------------------------------------------------
    */
    public function getSisaQtyAttribute()
    {
        return max(
            0,
            $this->qty_rencana - $this->qty_diterima
        );
    }
}