<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use Illuminate\Http\Request;
use App\Models\VarianBarang;
use App\Models\DetailPenjualan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class PenjualanController extends Controller
{
    public function index()
    {
        $penjualan = Penjualan::latest()->get();

        return view(
            'penjualan.index',
            compact('penjualan')
        );
    }

    public function create()
    {
        $varian = VarianBarang::with('barang')
            ->get();

        return view(
            'penjualan.create',
            compact('varian')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_nota' => 'required|unique:penjualan,no_nota',
            'tanggal_penjualan' => 'required',
            'channel' => 'required',
            'metode_pembayaran' => 'required',
            'cart' => 'required|array|min:1',
            'cart.*.varian_id' => 'required|integer|exists:varian_barang,id',
            'cart.*.qty' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        $varianBerubah = [];

        try {
            $penjualan = Penjualan::create([
                'no_nota' => $request->no_nota,
                'tanggal_penjualan' => $request->tanggal_penjualan,
                'channel' => $request->channel,
                'metode_pembayaran' => $request->metode_pembayaran,
                'total' => 0,
                'user_id' => auth()->id(),
            ]);

            $grandTotal = 0;

            foreach ($request->cart as $item) {

                $varian = VarianBarang::whereKey($item['varian_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($varian->stok < $item['qty']) {
                    DB::rollBack();

                    return back()->with(
                        'error',
                        'Stok ' . $varian->barang->nama_barang . ' tidak mencukupi'
                    );
                }

                $subtotal = $varian->harga_jual * $item['qty'];

                DetailPenjualan::create([
                    'penjualan_id' => $penjualan->id,
                    'varian_id' => $varian->id,
                    'qty' => $item['qty'],
                    'harga' => $varian->harga_jual,
                    'subtotal' => $subtotal,
                ]);

                $varian->decrement('stok', $item['qty']);
                $varian->refresh();
                $varianBerubah[$varian->id] = $varian;

                $grandTotal += $subtotal;
            }

            $penjualan->update(['total' => $grandTotal]);

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        // Sync ke Shopee dilakukan SETELAH commit sukses, di luar transaksi DB.
        // Kalau ditaruh di dalam transaction, request HTTP ke Shopee bisa bikin
        // koneksi/lock DB nyangkut lama, dan external call tetap gak bisa di-rollback.
        $marketplace = app(MarketplaceController::class);

        foreach ($varianBerubah as $varian) {
            try {
                $marketplace->syncSingleStockToShopee($varian);
            } catch (\Throwable $e) {
                Log::error('Realtime Sync Shopee Gagal', [
                    'varian_id' => $varian->id,
                    'message' => $e->getMessage()
                ]);
            }
        }

        return redirect()
            ->route('penjualan.struk', $penjualan->id)
            ->with('success', 'Transaksi berhasil disimpan');
    }

    public function show(Penjualan $penjualan)
    {
        //
    }

    public function edit(Penjualan $penjualan)
    {
        return view(
            'penjualan.edit',
            compact('penjualan')
        );
    }

    public function update(
        Request $request,
        Penjualan $penjualan
    ) {
        $request->validate([
            'no_nota' => 'required',
            'tanggal_penjualan' => 'required',
            'channel' => 'required',
            'metode_pembayaran' => 'required',
        ]);

        $penjualan->update([
            'no_nota' => $request->no_nota,
            'tanggal_penjualan' => $request->tanggal_penjualan,
            'channel' => $request->channel,
            'metode_pembayaran' => $request->metode_pembayaran,
        ]);

        return redirect()->route(
            'detail-penjualan.create',
            [
                'penjualan_id' => $penjualan->id
            ]
        );
    }

    public function destroy(
        Penjualan $penjualan
    ) {
        $penjualan->delete();

        return redirect()
            ->route('penjualan.index')
            ->with(
                'success',
                'Penjualan berhasil dihapus'
            );
    }

    public function struk($id)
    {
        $penjualan = Penjualan::with('user')->findOrFail($id);

        $detail = DetailPenjualan::with('varian.barang')
            ->where('penjualan_id', $penjualan->id)
            ->get();

        return view('penjualan.struk', compact('penjualan', 'detail'));
    }
}
