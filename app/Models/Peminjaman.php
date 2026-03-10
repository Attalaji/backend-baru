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

    // Gunakan $this, bukan $table!
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class, 'buku_id');
    }

    // Accessor untuk mempermudah React membaca status
    protected $appends = ['status_label'];

    public function getStatusLabelAttribute()
    {
        if ($this->tgl_kembali) {
            return $this->denda > 0 ? 'Terlambat' : 'Tepat Waktu';
        }
        return Carbon::now()->gt(Carbon::parse($this->jatuh_tempo)) ? 'Terlambat' : 'Aktif';
    }
}