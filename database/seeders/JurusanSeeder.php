<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jurusan;

class JurusanSeeder extends Seeder
{
    public function run(): void
    {
        Jurusan::create(['nama' => 'Teknik Informatika']); // ID 1
        Jurusan::create(['nama' => 'Teknik Elektro']);      // ID 2
    }
}

