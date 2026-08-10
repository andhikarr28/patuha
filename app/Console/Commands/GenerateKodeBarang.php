<?php

namespace App\Console\Commands;

use App\Models\Barang;
use App\Services\KodeBarangGeneratorService;
use Illuminate\Console\Command;

class GenerateKodeBarang extends Command
{
    protected $signature = 'barang:generate-kode';

    protected $description = 'Generate kode_barang otomatis untuk barang lama yang belum punya kode_barang';

    public function handle(KodeBarangGeneratorService $service)
    {
        $barangTanpaKode = Barang::whereNull('kode_barang')
            ->orWhere('kode_barang', '')
            ->with('kategori')
            ->get();

        if ($barangTanpaKode->isEmpty()) {
            $this->info('Semua barang sudah punya kode_barang. Tidak ada yang perlu diproses.');
            return;
        }

        $this->info("Ditemukan {$barangTanpaKode->count()} barang tanpa kode_barang. Memproses...");

        $berhasil = 0;
        $dilewati = 0;

        foreach ($barangTanpaKode as $barang) {

            if (!$barang->kategori || empty($barang->kategori->kode)) {
                $this->warn("Dilewati: '{$barang->nama_barang}' (id: {$barang->id}) — kategori belum punya kode.");
                $dilewati++;
                continue;
            }

            $hasil = $service->generate(
                $barang->kategori->id,
                $barang->kategori->kode,
                $barang->brand,
                $barang->artikel
            );

            $barang->update([
                'kode_seri'   => $hasil['kode_seri'],
                'kode_barang' => $hasil['kode_barang'],
            ]);

            $this->line("✔ '{$barang->nama_barang}' -> {$hasil['kode_barang']}");
            $berhasil++;
        }

        $this->info("Selesai. Berhasil: {$berhasil}, Dilewati: {$dilewati}.");
    }
}