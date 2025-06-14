<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('divisis', function (Blueprint $table) {
        $table->id();
        $table->foreignId('organisasi_id')->constrained()->onDelete('cascade');
        $table->string('nama');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('divisis');
    }
};
