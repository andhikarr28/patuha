<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VarianBarang extends Model
{
    use HasFactory;

    protected $table = 'varian_barang';

    protected $fillable = [
        'barang_id',
        'warna',
        'ukuran',
        'sku',
        'harga_jual',
        'stok',
        'stok_minimum',
    ];

    public function barang()
    {
        return $this->belongsTo(
            Barang::class,
            'barang_id'
        );
    }

    public function detailPembelian()
    {
        return $this->hasMany(DetailPembelian::class, 'varian_id');
    }

    public function detailPenjualan()
{
    return $this->hasMany(
        DetailPenjualan::class,
        'varian_id'
    );
}
}