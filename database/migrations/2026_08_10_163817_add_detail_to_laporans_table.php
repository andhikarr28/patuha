<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pembelian extends Model
{
    use HasFactory;

    protected $table = 'pembelian';

    protected $fillable = [
        'perencanaan_pembelian_id',
        'no_faktur',
        'tanggal_pembelian',
        'supplier_id',
        'status',
        'total_brutto',
        'total_diskon',
        'total_netto',
        'user_id',
    ];

    public function supplier()
    {
        return $this->belongsTo(
            Supplier::class,
            'supplier_id'
        );
    }

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    public function detailPembelian()
    {
        return $this->hasMany(
            DetailPembelian::class,
            'pembelian_id'
        );
    }

    public function perencanaan()
    {
        return $this->belongsTo(
            PerencanaanPembelian::class,
            'perencanaan_pembelian_id'
        );
    }
}