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
    Schema::create('buku', function (Blueprint $table) {
        $table->id();
        $table->string('kode_buku')->unique(); // PWL-001
        $table->string('judul');
        $table->string('penulis');
        $table->string('penerbit');
        $table->string('kategori');
        $table->integer('tahun_terbit');
        $table->integer('stok')->default(0);     // Total owned
        $table->integer('tersedia')->default(0); // Current count on shelf
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku');
    }
};
