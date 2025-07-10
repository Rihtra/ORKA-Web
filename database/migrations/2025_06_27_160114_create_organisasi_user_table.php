<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        
          Schema::create('organisasi_user', function (Blueprint $table) {
              $table->id();
              $table->foreignId('organisasi_id')->constrained()->onDelete('cascade');
              $table->foreignId('user_id')->constrained()->onDelete('cascade');
              $table->enum('role', [
                  'Ketua',
                  'Wakil',
                  'Bendahara 1',
                  'Bendahara 2',
                  'Sekretaris 1',
                  'Sekretaris 2',
                  'Anggota'
              ])->default('Anggota');
              $table->timestamps();
          });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organisasi_user');
    }
};
