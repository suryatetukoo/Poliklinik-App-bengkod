<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use Illuminate\Http\Request;

class ObatController extends Controller
{
    public function index()
    {
        $obats = Obat::all();
        return view('admin.obat.index', compact('obats'));
    }

    public function create()
    {
        return view('admin.obat.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_obat' => 'required|string',
            'kemasan'   => 'required|string',
            'harga'     => 'required|integer',
            'stok'      => 'required|integer|min:0',  
        ]);

        Obat::create([
            'nama_obat' => $request->nama_obat,
            'kemasan'   => $request->kemasan,
            'harga'     => $request->harga,
            'stok'      => $request->stok,             // ← TAMBAHAN
        ]);

        return redirect()->route('obat.index')
            ->with('message', 'Data Obat Berhasil dibuat')
            ->with('type', 'success');
    }

    public function edit(string $id)
    {
        $obat = Obat::findOrFail($id);
        return view('admin.obat.edit', compact('obat'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_obat' => 'required|string',
            'kemasan'   => 'nullable|string',
            'harga'     => 'required|integer',
            'stok'      => 'required|integer|min:0',   // ← TAMBAHAN
        ]);

        $obat = Obat::findOrFail($id);
        $obat->update([
            'nama_obat' => $request->nama_obat,
            'kemasan'   => $request->kemasan,
            'harga'     => $request->harga,
            'stok'      => $request->stok,             // ← TAMBAHAN
        ]);

        return redirect()->route('obat.index')
            ->with('message', 'Data Obat Berhasil di edit')
            ->with('type', 'success');
    }

    public function destroy(string $id)
    {
        $obat = Obat::findOrFail($id);
        $obat->delete();

        return redirect()->route('obat.index')
            ->with('message', 'Data Obat Berhasil di Hapus')
            ->with('type', 'success');
    }

    // ==========================================
    // TAMBAH STOK MANUAL OLEH ADMIN
    // ==========================================
    public function tambahStok(Request $request, string $id)
    {
        $request->validate([
            'jumlah' => 'required|integer|min:1',
        ]);

        $obat = Obat::findOrFail($id);
        $obat->stok += $request->jumlah;
        $obat->save();

        return redirect()->route('obat.index')
            ->with('message', 'Stok obat berhasil ditambah sebanyak ' . $request->jumlah)
            ->with('type', 'success');
    }

    // ==========================================
    // KURANGI STOK MANUAL OLEH ADMIN
    // ==========================================
    public function kurangiStok(Request $request, string $id)
    {
        $request->validate([
            'jumlah' => 'required|integer|min:1',
        ]);

        $obat = Obat::findOrFail($id);

        if ($obat->stok < $request->jumlah) {
            return redirect()->back()
                ->with('message', 'Gagal! Stok tidak mencukupi. Stok tersedia: ' . $obat->stok)
                ->with('type', 'error');
        }

        $obat->stok -= $request->jumlah;
        $obat->save();

        return redirect()->route('obat.index')
            ->with('message', 'Stok obat berhasil dikurangi sebanyak ' . $request->jumlah)
            ->with('type', 'success');
    }
}