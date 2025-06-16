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
        if (!User::where('email', 'super@admin.com')->exists()) {
            User::create([
                'name' => 'Super Admin',
                'email' => 'super@admin.com',
                'password' => Hash::make('12345678'),
                'role' => 'super_admin',
            ]);
        }

        // Admin Organisasi
        if (!User::where('email', 'ukm@admin.com')->exists()) {
            User::create([
                'name' => 'Admin UKM Olahraga',
                'email' => 'ukm@admin.com',
                'password' => Hash::make('12345678'),
                'role' => 'admin_organisasi',
                'jurusan_id' => 1, // pastikan ID jurusan ini ada
            ]);
        }

        // User Mahasiswa
        if (!User::where('email', 'rizqo@user.com')->exists()) {
            User::create([
                'name' => 'Rizqo',
                'email' => 'rizqo@user.com',
                'password' => Hash::make('12345678'),
                'role' => 'mahasiswa',
                'jurusan_id' => 1, // pastikan ID jurusan ini ada
            ]);
        }
    }
}
