<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\PoliController; // Import PoliController ditambahkan di sini
use Illuminate\Support\Facades\Route;

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
});

// --- Rute Dokter ---
Route::middleware(['auth', 'role:dokter'])->prefix('dokter')->group(function () {
    Route::get('/dashboard', function () {
        return view('dokter.dashboard');
    })->name('dokter.dashboard');
});

// --- Rute Pasien ---
Route::middleware(['auth', 'role:pasien'])->prefix('pasien')->group(function () {
    Route::get('/dashboard', function () {
        return view('pasien.dashboard');
    })->name('pasien.dashboard');
});