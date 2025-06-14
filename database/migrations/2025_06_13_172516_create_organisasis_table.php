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
    Schema::create('organisasis', function (Blueprint $table) {
        $table->id();
        $table->string('nama');
        $table->foreignId('jurusan_id')->constrained('jurusans')->onDelete('cascade');
        $table->foreignId('admin_user_id')->constrained('users')->onDelete('cascade');
        $table->text('deskripsi')->nullable();
        $table->string('logo')->nullable();
        $table->text('visi')->nullable();
        $table->text('misi')->nullable();
        $table->text('syarat')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organisasis');
    }
};
