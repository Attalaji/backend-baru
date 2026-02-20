<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    // Force Laravel to use 'buku' instead of 'bukus'
    protected $table = 'buku'; 

    // Fields that can be filled by the API
    protected $fillable = [
        'kode_buku', 
        'judul', 
        'penulis', 
        'penerbit', 
        'kategori', 
        'tahun_terbit', 
        'stok', 
        'tersedia'
    ];
}