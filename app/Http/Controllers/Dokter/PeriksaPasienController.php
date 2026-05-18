<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\DetailPeriksa;
use App\Models\Obat;
use App\Models\Periksa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeriksaPasienController extends Controller
{
    public function index()
    {
        $dokterId = Auth::id();

        $daftarPasien = DaftarPoli::with(['pasien', 'jadwalPeriksa', 'periksas'])
            ->whereHas('jadwalPeriksa', function ($query) use ($dokterId) {
                $query->where('id_dokter', $dokterId);
            })
            ->orderBy('no_antrian')
            ->get();

        return view('dokter.periksa-pasien.index', compact('daftarPasien'));
    }

    public function create($id)
    {
        $obats = Obat::all();
        return view('dokter.periksa-pasien.create', compact('obats', 'id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'obat_json'      => 'required',
            'catatan'        => 'nullable|string',
            'biaya_periksa'  => 'required|integer',
        ]);

        $obatIds = json_decode($request->obat_json, true);

        // ==========================================
        // VALIDASI STOK SEMUA OBAT SEBELUM DISIMPAN
        // ==========================================
        foreach ($obatIds as $idObat) {
            $obat = Obat::findOrFail($idObat);

            if ($obat->stok <= 0) {
                return redirect()->back()
                    ->with('message', 'Stok obat "' . $obat->nama_obat . '" habis! Tidak bisa diresepkan.')
                    ->with('type', 'error');
            }
        }

        // Simpan data periksa
        $periksa = Periksa::create([
            'id_daftar_poli' => $request->id_daftar_poli,
            'tgl_periksa'    => now(),
            'catatan'        => $request->catatan,
            'biaya_periksa'  => $request->biaya_periksa + 150000,
        ]);

        // Simpan detail periksa & kurangi stok otomatis
        foreach ($obatIds as $idObat) {
            DetailPeriksa::create([
                'id_periksa' => $periksa->id,
                'id_obat'    => $idObat,
            ]);

            // ==========================================
            // OTOMATIS KURANGI STOK SAAT OBAT DIRESEPKAN
            // ==========================================
            $obat = Obat::findOrFail($idObat);
            $obat->stok -= 1;
            $obat->save();
        }

        return redirect()->route('periksa-pasien.index')
            ->with('success', 'Data periksa berhasil disimpan.');
    }
}