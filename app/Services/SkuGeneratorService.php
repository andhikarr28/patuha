<?php

namespace App\Services;

use App\Models\Barang;

class SkuGeneratorService
{
    /**
     * Generate SKU otomatis dari kode_barang + warna + ukuran.
     * Contoh: kode_barang "JH-CON-AAR-1", warna "Biru", ukuran "XL"
     *         -> "JH-CON-AAR-1-BIR-XL"
     */
    public function generate(Barang $barang, string $warna, string $ukuran): string
    {
        $kodeBarang = $barang->kode_barang ?: ('BRG' . $barang->id);
        $kodeWarna  = KodeHelper::singkat($warna);
        $kodeUkuran = strtoupper(str_replace(' ', '', trim($ukuran)));

        return strtoupper("{$kodeBarang}-{$kodeWarna}-{$kodeUkuran}");
    }
}