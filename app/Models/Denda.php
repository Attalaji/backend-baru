<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Denda extends Model
{
    use HasFactory;

    // 1. Beritahu Laravel nama tabelnya (karena defaultnya 'dendas')
    protected $table = 'denda';

    // 2. Daftarkan kolom yang boleh diisi manual
    protected $fillable = [
        'peminjaman_id',
        'total_denda',
        'status',
        'tgl_bayar'
    ];

    // 3. Relasi Balik ke Peminjaman (Opsional tapi berguna)
    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
    }
    
}