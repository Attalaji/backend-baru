<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Peminjaman extends Model
{
    use HasFactory;
    
    protected $table = 'peminjaman';
    protected $guarded = ['id'];

    // Relasi: Satu peminjaman milik satu user
    public function user()
    {
        return $table->belongsTo(User::class, 'user_id');
    }

    // Relasi: Satu peminjaman berisi satu buku
    public function buku()
    {
        return $this->belongsTo(Buku::class, 'buku_id');
    }

    // Accessor untuk menghitung status secara dinamis
    // Ini tidak disimpan di DB, tapi dihitung saat data diambil
    public function getStatusAttribute()
    {
        // Jika hari ini lebih besar dari jatuh tempo, maka terlambat
        return Carbon::now()->gt(Carbon::parse($this->jatuh_tempo)) ? 'Terlambat' : 'Aktif';
    }
}