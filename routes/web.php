<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\PoliController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dokter\JadwalPeriksaController;
use App\Http\Controllers\Dokter\PeriksaPasienController;
use App\Http\Controllers\Dokter\RiwayatPasienController;
use App\Http\Controllers\Admin\DokterController;
use App\Http\Controllers\Admin\PasienController;
use App\Http\Controllers\Admin\ObatController;
use App\Http\Controllers\Pasien\PoliController as PasienPoliController;

Route::get('/', function () {
    return view('welcome');
});

// --- Rute Otentikasi ---
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- Rute Admin ---
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    
    // Resource route untuk poli disatukan di dalam group admin ini
    Route::resource('polis', PoliController::class);
    Route::resource('dokter', App\Http\Controllers\Admin\DokterController::class)->names('dokter');
    Route::resource('pasien', App\Http\Controllers\Admin\PasienController::class)->names('pasien');
    Route::resource('obat', App\Http\Controllers\Admin\ObatController::class)->names('obat');
    Route::post('obat/{id}/tambah-stok', [ObatController::class, 'tambahStok'])->name('obat.tambahStok');
    Route::post('obat/{id}/kurangi-stok', [ObatController::class, 'kurangiStok'])->name('obat.kurangiStok');
});

// --- Rute Dokter ---
Route::middleware(['auth', 'role:dokter'])->prefix('dokter')->group(function () {

    Route::get('/dashboard', function () {
        return view('dokter.dashboard');
    })->name('dokter.dashboard');

    Route::resource('jadwal-periksa', JadwalPeriksaController::class);
    Route::get('/periksa-pasien', [PeriksaPasienController::class, 'index'])
        ->name('periksa-pasien.index');
    Route::post('/periksa-pasien', [PeriksaPasienController::class, 'store'])
        ->name('periksa-pasien.store');
    Route::get('/periksa-pasien/{id}', [PeriksaPasienController::class, 'create'])
        ->name('periksa-pasien.create');
    Route::get('/riwayat-pasien', [RiwayatPasienController::class, 'index'])
        ->name('riwayat-pasien.index');
    Route::get('/riwayat-pasien/{id}', [RiwayatPasienController::class, 'show'])
        ->name('riwayat-pasien.show');

});

// --- Rute Pasien ---
Route::middleware(['auth', 'role:pasien'])->prefix('pasien')->group(function () {
    Route::get('/dashboard', function () {
        return view('pasien.dashboard');
    })->name('pasien.dashboard');
    Route::get('/daftar', [App\Http\Controllers\Pasien\PoliController::class, 'get'])->name('pasien.daftar');
    Route::post('/daftar', [App\Http\Controllers\Pasien\PoliController::class, 'submit'])->name('pasien.daftar.submit');
});