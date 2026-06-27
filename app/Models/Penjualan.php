<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualan';

    protected $fillable = [
        'no_nota',
        'tanggal_penjualan',
        'channel',
        'total',
        'user_id',
    ];

    public function detailPenjualan()
    {
        return $this->hasMany(
            DetailPenjualan::class,
            'penjualan_id'
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}