<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poli extends Model
{
    use HasFactory;

    protected $table = 'poli';

    protected $fillable = [
        'nama_poli',
        'keterangan',
    ];

    public function jadwalPeriksas()
{
    return $this->hasManyThrough(
        JadwalPeriksa::class, // Model Target
        User::class,          // Model Perantara (Dokter)
        'id_poli',           // Foreign key di tabel users
        'id_dokter',         // Foreign key di tabel jadwal_periksa
        'id',                // Local key di tabel poli
        'id'                 // Local key di tabel users
    );
}

    public function dokters()
    {
        return $this->hasMany(User::class, 'id_poli');
    }
}
