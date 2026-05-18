<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use App\Models\Poli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PoliController extends Controller
{
    public function get()
    {
        $user = Auth::user();
        $polis = Poli::with(['dokters.jadwalPeriksa'])->get(); 
        $jadwals = JadwalPeriksa::with('dokter', 'dokter.poli')->get();

        return view('pasien.daftar', [
            'user'    => $user,
            'polis'   => $polis,
            'jadwals' => $jadwals,
        ]);
    }

    public function submit(Request $request)
    {
        $request->validate([
            'id_poli'   => 'required|exists:poli,id',
            'id_jadwal' => 'required|exists:jadwal_periksa,id',
            'keluhan'   => 'nullable|string',
            'id_pasien' => 'required|exists:users,id',
        ]);

        // ==========================================
        // VALIDASI: cek apakah pasien sudah daftar
        // di jadwal yang sama dan belum diperiksa
        // ==========================================
        $sudahDaftar = DaftarPoli::where('id_pasien', $request->id_pasien)
            ->where('id_jadwal', $request->id_jadwal)
            ->whereDoesntHave('periksas') // belum diperiksa
            ->exists();

        if ($sudahDaftar) {
            return redirect()->back()
                ->with('message', 'Anda sudah terdaftar di jadwal ini dan belum diperiksa.')
                ->with('type', 'error');
        }

        $jumlahSudahDaftar = DaftarPoli::where('id_jadwal', $request->id_jadwal)->count();

        DaftarPoli::create([
            'id_pasien'  => $request->id_pasien,
            'id_jadwal'  => $request->id_jadwal,
            'keluhan'    => $request->keluhan,
            'no_antrian' => $jumlahSudahDaftar + 1,
        ]);

        return redirect()->back()
            ->with('message', 'Berhasil Mendaftar ke Poli! Nomor antrian: ' . ($jumlahSudahDaftar + 1))
            ->with('type', 'success');
    }
}