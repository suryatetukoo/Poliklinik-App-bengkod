<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // nama, email, password, role, alamat, no_hp, no_ktp
        $users = [
            [
                'nama' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin'),
                'role' => 'admin',
                'alamat' => 'Jl. Admin Sejahtera No. 1',
                'no_hp' => '081111111111',
                'no_ktp' => '1111111111111111',
            ],
            [
                'nama' => 'Dokter',
                'email' => 'dokter@gmail.com',
                'password' => Hash::make('dokter'),
                'role' => 'dokter',
                'alamat' => 'Jl. Dokter Sehat No. 2',
                'no_hp' => '082222222222',
                'no_ktp' => '2222222222222222',
            ],
            [
                'nama' => 'Pasien',
                'email' => 'pasien@gmail.com',
                'password' => Hash::make('pasien'),
                'role' => 'pasien',
                'alamat' => 'Jl. Pasien Sembuh No. 3',
                'no_hp' => '083333333333',
                'no_ktp' => '3333333333333333',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}