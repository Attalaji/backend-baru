<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            // Foreign Key ke tabel users (untuk NIM, Nama, Kelas, Telp)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // Foreign Key ke tabel buku (untuk No Buku, Judul)
            $table->foreignId('buku_id')->constrained('buku')->onDelete('cascade');
            $table->date('tgl_pinjam');
            $table->date('jatuh_tempo'); // H+5 dari tgl_pinjam
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('peminjaman');
    }
};
