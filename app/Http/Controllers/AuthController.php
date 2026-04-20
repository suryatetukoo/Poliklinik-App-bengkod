<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ==========================================
    // FUNGSI UNTUK MENAMPILKAN HALAMAN (GET)
    // ==========================================

    public function showLogin()
    {
        // Pastikan kamu punya file resources/views/auth/login.blade.php
        return view('auth.login'); 
    }

    public function showRegister()
    {
        // Pastikan kamu punya file resources/views/auth/register.blade.php
        return view('auth.register');
    }

    // ==========================================
    // FUNGSI UNTUK MEMPROSES DATA (POST)
    // ==========================================

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->role == 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role == 'dokter') {
                return redirect()->route('dokter.dashboard');
            } else {
                return redirect()->route('pasien.dashboard');
            }
        }

        return back()->withErrors(['email' => 'Email atau Password Salah !']);
    }

    public function register(Request $request)
{
    $request->validate([
        'nama'     => 'required|string|max:255',
        'alamat'   => 'required|string',
        'no_ktp'   => 'required|numeric|unique:users,no_ktp',
        'no_hp'    => 'required|numeric',
        'email'    => 'required|email|unique:users,email',
        'password' => 'required|min:6'
    ]);

    $lastPasien = User::where('role', 'pasien')->orderBy('id', 'desc')->first();
    $lastId = $lastPasien ? $lastPasien->id + 1 : 1;
    $no_rm = date('Ym') . '-' . str_pad($lastId, 3, '0', STR_PAD_LEFT);

    User::create([
        'nama'     => $request->nama,
        'alamat'   => $request->alamat,
        'no_ktp'   => $request->no_ktp,
        'no_hp'    => $request->no_hp,
        'no_rm'    => $no_rm,
        'role'     => 'pasien',
        'email'    => $request->email,
        'password' => Hash::make($request->password),
    ]);

    return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login.');
}


    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login'); 
    }
}