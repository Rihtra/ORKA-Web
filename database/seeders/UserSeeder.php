<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'super@admin.com',
            'password' => Hash::make('12345678'), // ganti kalau mau aman
            'role' => 'super_admin',
        ]);

        // Admin Organisasi
        User::create([
            'name' => 'Admin UKM Olahraga',
            'email' => 'ukm@admin.com',
            'password' => Hash::make('12345678'),
            'role' => 'admin_organisasi',
            'jurusan_id' => 1, // pastikan ID jurusan ini ada
        ]);
    }
}

