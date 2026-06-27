<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use Illuminate\Http\Request;

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
        return view('penjualan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_nota' => 'required|unique:penjualan,no_nota',
            'tanggal_penjualan' => 'required',
            'channel' => 'required',
        ]);

        Penjualan::create([
            'no_nota' => $request->no_nota,
            'tanggal_penjualan' => $request->tanggal_penjualan,
            'channel' => $request->channel,
            'total' => 0,
            'user_id' => auth()->id(),
        ]);

        return redirect()
            ->route('penjualan.index')
            ->with(
                'success',
                'Penjualan berhasil ditambahkan'
            );
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
        ]);

        $penjualan->update([
            'no_nota' => $request->no_nota,
            'tanggal_penjualan' => $request->tanggal_penjualan,
            'channel' => $request->channel,
        ]);

        return redirect()
            ->route('penjualan.index')
            ->with(
                'success',
                'Penjualan berhasil diupdate'
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
}