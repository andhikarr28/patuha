<?php

namespace App\Services;

use App\Models\Barang;

class KodeBarangGeneratorService
{
    /**
     * Gabungkan kode kategori + singkatan brand + singkatan artikel + kode_seri
     * (kode_seri diinput manual oleh admin, bukan dihitung otomatis).
     * Contoh: kategori "WB", brand "Boggie", artikel "Ostrich", kode_seri "23"
     *         -> "WB-BOG-OST-23"
     */
    public function generate(string $kodeKategori, ?string $brand, ?string $artikel, string $kodeSeri): string
    {
        $kodeKategori = $kodeKategori ?: 'KTG';
        $kodeBrand    = KodeHelper::singkat($brand ?: '-');
        $kodeArtikel  = KodeHelper::singkat($artikel ?: '-');
        $kodeSeri     = trim($kodeSeri);

        return strtoupper("{$kodeKategori}-{$kodeBrand}-{$kodeArtikel}-{$kodeSeri}");
    }

    /**
     * Cek apakah kode_barang hasil generate sudah dipakai barang lain.
     * $kecualiId dipakai saat update, supaya barang yang sedang diedit
     * tidak dianggap bentrok dengan dirinya sendiri.
     */
    public function sudahDipakai(string $kodeBarang, ?int $kecualiId = null): bool
    {
        return Barang::where('kode_barang', $kodeBarang)
            ->when($kecualiId, fn ($q) => $q->where('id', '!=', $kecualiId))
            ->exists();
    }
}