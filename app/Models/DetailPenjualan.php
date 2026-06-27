<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailPenjualan extends Model
{
    use HasFactory;

    protected $table = 'detail_penjualan';

    protected $fillable = [
        'penjualan_id',
        'varian_id',
        'qty',
        'harga',
        'subtotal',
    ];

    public function penjualan()
    {
        return $this->belongsTo(
            Penjualan::class,
            'penjualan_id'
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