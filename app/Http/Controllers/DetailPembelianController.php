<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\VarianBarang;
use App\Models\DetailPembelian;
use Illuminate\Http\Request;

class DetailPembelianController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->pembelian_id) {

            return redirect()
                ->route('pembelian.index');
        }

        $pembelian = Pembelian::findOrFail(
            $request->pembelian_id
        );

        $detail = DetailPembelian::with([
            'varian.barang'
        ])
            ->where(
                'pembelian_id',
                $pembelian->id
            )
            ->get();

        return view(
            'detail-pembelian.index',
            compact(
                'detail',
                'pembelian'
            )
        );
    }

    public function create()
    {
        $pembelian = Pembelian::all();

        $varian = VarianBarang::with('barang')->get();

        return view(
            'detail-pembelian.create',
            compact(
                'pembelian',
                'varian'
            )
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'pembelian_id' => 'required',
            'varian_id' => 'required',
            'qty' => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric',
            'diskon' => 'nullable|numeric',
        ]);

        $subtotal =
            ($request->qty * $request->harga_satuan)
            - ($request->diskon ?? 0);

        DetailPembelian::create([
            'pembelian_id' => $request->pembelian_id,
            'varian_id' => $request->varian_id,
            'qty' => $request->qty,
            'harga_satuan' => $request->harga_satuan,
            'diskon' => $request->diskon ?? 0,
            'subtotal' => $subtotal,
        ]);

        $this->updateTotalPembelian(
            $request->pembelian_id
        );

        return redirect()
            ->route(
                'detail-pembelian.index',
                [
                    'pembelian_id' => $request->pembelian_id
                ]
            )
            ->with(
                'success',
                'Detail pembelian berhasil ditambahkan'
            );
    }

    public function edit(
        DetailPembelian $detail_pembelian
    ) {
        $pembelian = Pembelian::all();

        $varian = VarianBarang::with('barang')->get();

        return view(
            'detail-pembelian.edit',
            [
                'detail' => $detail_pembelian,
                'pembelian' => $pembelian,
                'varian' => $varian
            ]
        );
    }

    public function update(
        Request $request,
        DetailPembelian $detail_pembelian
    ) {
        $request->validate([
            'pembelian_id' => 'required',
            'varian_id' => 'required',
            'qty' => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric',
            'diskon' => 'nullable|numeric',
        ]);

        $pembelianLama =
            $detail_pembelian->pembelian_id;

        $subtotal =
            ($request->qty * $request->harga_satuan)
            - ($request->diskon ?? 0);

        $detail_pembelian->update([
            'pembelian_id' => $request->pembelian_id,
            'varian_id' => $request->varian_id,
            'qty' => $request->qty,
            'harga_satuan' => $request->harga_satuan,
            'diskon' => $request->diskon ?? 0,
            'subtotal' => $subtotal,
        ]);

        $this->updateTotalPembelian(
            $pembelianLama
        );

        $this->updateTotalPembelian(
            $request->pembelian_id
        );

        return redirect()
            ->route(
                'detail-pembelian.index',
                [
                    'pembelian_id' => $request->pembelian_id
                ]
            )
            ->with(
                'success',
                'Detail pembelian berhasil diupdate'
            );
    }

    public function destroy(
        DetailPembelian $detail_pembelian
    ) {
        $pembelianId =
            $detail_pembelian->pembelian_id;

        $detail_pembelian->delete();

        $this->updateTotalPembelian(
            $pembelianId
        );

        return redirect()
            ->route(
                'detail-pembelian.index',
                [
                    'pembelian_id' => $pembelianId
                ]
            )
            ->with(
                'success',
                'Detail pembelian berhasil dihapus'
            );
    }

    private function updateTotalPembelian(
        $pembelianId
    ) {
        $pembelian = Pembelian::findOrFail(
            $pembelianId
        );

        $totalBrutto =
            $pembelian
                ->detailPembelian()
                ->sum('subtotal');

        $totalDiskon =
            $pembelian
                ->detailPembelian()
                ->sum('diskon');

        $totalNetto =
            $totalBrutto - $totalDiskon;

        $pembelian->update([
            'total_brutto' => $totalBrutto,
            'total_diskon' => $totalDiskon,
            'total_netto' => $totalNetto
        ]);
    }
}
