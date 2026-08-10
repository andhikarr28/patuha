<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';

    protected $fillable = [
        'supplier_id',
        'kategori_id',
        'nama_barang',
        'artikel',
        'kode_seri',
        'brand',
        'spesifikasi',
        'foto',
        'kode_barang',
    ];
    public function kategori()
    {
        return $this->belongsTo(
            Kategori::class,
            'kategori_id'
        );
    }

    public function varians()
    {
        return $this->hasMany(
            VarianBarang::class,
            'barang_id'
        );
    }

    public function supplier()
    {
        return $this->belongsTo(
            Supplier::class
        );
    }
}
