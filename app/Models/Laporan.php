<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    protected $fillable = [
        'kode_laporan',
        'periode_awal',
        'periode_akhir',
        'jumlah_transaksi_penjualan',
        'total_penjualan_toko',
        'total_penjualan_marketplace',
        'jumlah_transaksi_pembelian',
        'total_pembelian',
        'barang_terlaris',
        'barang_kurang_laku',
        'detail_transaksi_penjualan',
        'detail_transaksi_pembelian',
        'ringkasan_perencanaan',
        'jumlah_varian_stok_menipis',
        'estimasi_laba_kotor',
        'catatan_evaluasi',
        'keputusan_owner',
        'status',
        'dibuat_oleh',
        'dikirim_at',
        'ditinjau_at',
    ];

    protected $casts = [
        'periode_awal' => 'date',
        'periode_akhir' => 'date',
        'dikirim_at' => 'datetime',
        'ditinjau_at' => 'datetime',
        'barang_terlaris' => 'array',
        'barang_kurang_laku' => 'array',
        'detail_transaksi_penjualan' => 'array',
        'detail_transaksi_pembelian' => 'array',
        'ringkasan_perencanaan' => 'array',
        'estimasi_laba_kotor' => 'decimal:2',
    ];

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function getTotalPenjualanAttribute()
    {
        return $this->total_penjualan_toko + $this->total_penjualan_marketplace;
    }
}