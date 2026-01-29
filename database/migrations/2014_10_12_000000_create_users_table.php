<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nim')->unique(); // Added for NIM
            $table->string('name');          // Matches 'Nama Lengkap'
            $table->string('prodi');         // Added for Program Studi
            $table->string('kelas');         // Added for Kelas
            $table->string('email')->unique();
            $table->string('no_hp');         // Added for Nomor HP
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};