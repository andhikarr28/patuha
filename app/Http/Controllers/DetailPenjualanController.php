<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\VarianBarang;
use App\Models\DetailPenjualan;
use Illuminate\Http\Request;

class DetailPenjualanController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->penjualan_id) {
            return redirect()
                ->route('penjualan.index');
        }

        $penjualan = Penjualan::findOrFail(
            $request->penjualan_id
        );

        $detail = DetailPenjualan::with([
            'varian.barang'
        ])
            ->where(
                'penjualan_id',
                $penjualan->id
            )
            ->get();

        return view(
            'detail-penjualan.index',
            compact(
                'detail',
                'penjualan'
            )
        );
    }

    public function create(Request $request)
    {
        $penjualan = Penjualan::findOrFail(
            $request->penjualan_id
        );

        $varian = VarianBarang::with('barang')
            ->get();

        $detail = DetailPenjualan::with(
            'varian.barang'
        )
            ->where(
                'penjualan_id',
                $penjualan->id
            )
            ->get();

        return view(
            'detail-penjualan.create',
            compact(
                'penjualan',
                'varian',
                'detail'
            )
        );
    }
    public function store(Request $request)
    {
        $request->validate([
            'penjualan_id' => 'required',
            'varian_id' => 'required',
            'qty' => 'required|integer|min:1',
        ]);

        $varian = VarianBarang::findOrFail(
            $request->varian_id
        );

        if ($varian->stok < $request->qty) {
            return back()
                ->with(
                    'error',
                    'Stok tidak mencukupi'
                );
        }

        $harga = $varian->harga_jual;

        $subtotal =
            $request->qty *
            $harga;

        DetailPenjualan::create([
            'penjualan_id' => $request->penjualan_id,
            'varian_id' => $request->varian_id,
            'qty' => $request->qty,
            'harga' => $harga,
            'subtotal' => $subtotal,
        ]);

        $varian->decrement(
            'stok',
            $request->qty
        );

        $this->updateTotal(
            $request->penjualan_id
        );

        return redirect()
            ->route(
                'detail-penjualan.index',
                [
                    'penjualan_id' =>
                        $request->penjualan_id
                ]
            )
            ->with(
                'success',
                'Detail penjualan berhasil ditambahkan'
            );
    }

    public function edit(
        DetailPenjualan $detail_penjualan
    ) {
        $penjualan = Penjualan::all();

        $varian = VarianBarang::with('barang')
            ->get();

        return view(
            'detail-penjualan.edit',
            [
                'detail' => $detail_penjualan,
                'penjualan' => $penjualan,
                'varian' => $varian
            ]
        );
    }

    public function update(
        Request $request,
        DetailPenjualan $detail_penjualan
    ) {
        return redirect()
            ->route(
                'detail-penjualan.index',
                ['penjualan_id' => $detail_penjualan->penjualan_id]
            )
            ->with(
                'error',
                'Detail transaksi penjualan yang sudah selesai tidak dapat diubah.'
            );

        $request->validate([
            'penjualan_id' => 'required',
            'varian_id' => 'required',
            'qty' => 'required|integer|min:1',
        ]);

        // simpan penjualan lama
        $penjualanLama =
            $detail_penjualan->penjualan_id;

        // kembalikan stok lama
        $varianLama =
            VarianBarang::findOrFail(
                $detail_penjualan->varian_id
            );

        $varianLama->increment(
            'stok',
            $detail_penjualan->qty
        );

        $varianBaru =
            VarianBarang::findOrFail(
                $request->varian_id
            );

        if ($varianBaru->stok < $request->qty) {
            return back()
                ->with(
                    'error',
                    'Stok tidak cukup'
                );
        }

        $harga = $varianBaru->harga_jual;

        $subtotal =
            $request->qty *
            $harga;

        $detail_penjualan->update([
            'penjualan_id' => $request->penjualan_id,
            'varian_id' => $request->varian_id,
            'qty' => $request->qty,
            'harga' => $harga,
            'subtotal' => $subtotal,
        ]);

        $varianBaru->decrement(
            'stok',
            $request->qty
        );

        $this->updateTotal(
            $penjualanLama
        );

        $this->updateTotal(
            $request->penjualan_id
        );

        return redirect()
            ->route(
                'detail-penjualan.index',
                [
                    'penjualan_id' =>
                        $request->penjualan_id
                ]
            )
            ->with(
                'success',
                'Detail penjualan berhasil diupdate'
            );
    }

    public function destroy(
        DetailPenjualan $detail_penjualan
    ) {
        $varian =
            VarianBarang::findOrFail(
                $detail_penjualan->varian_id
            );

        $varian->increment(
            'stok',
            $detail_penjualan->qty
        );

        $penjualanId =
            $detail_penjualan->penjualan_id;

        $detail_penjualan->delete();

        $this->updateTotal(
            $penjualanId
        );

        return redirect()
            ->route(
                'detail-penjualan.index',
                [
                    'penjualan_id' =>
                        $penjualanId
                ]
            )
            ->with(
                'success',
                'Detail penjualan berhasil dihapus'
            );
    }

    private function updateTotal(
        $penjualanId
    ) {
        $penjualan =
            Penjualan::findOrFail(
                $penjualanId
            );

        $total =
            $penjualan
                ->detailPenjualan()
                ->sum('subtotal');

        $penjualan->update([
            'total' => $total
        ]);
    }
}
