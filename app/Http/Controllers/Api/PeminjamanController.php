<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Buku;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PeminjamanController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'buku_id' => 'required|exists:buku,id',
        ]);

        $user = Auth::user(); 
        $buku = Buku::findOrFail($request->buku_id);

        // 1. Check 'tersedia' instead of 'stok'
        if ($buku->tersedia < 1) {
             return response()->json(['message' => 'Maaf, buku ini sedang tidak tersedia di rak.'], 400);
        }

        $sedangDipinjam = Peminjaman::where('user_id', $user->id)
                            ->where('buku_id', $buku->id)
                            ->exists();
                            
        if($sedangDipinjam) {
             return response()->json(['message' => 'Anda sedang meminjam buku ini'], 400);
        }

        $tglPinjam = Carbon::now();
        $jatuhTempo = Carbon::now()->addDays(5); 

        Peminjaman::create([
            'user_id' => $user->id,
            'buku_id' => $buku->id,
            'tgl_pinjam' => $tglPinjam->toDateString(),
            'jatuh_tempo' => $jatuhTempo->toDateString(),
        ]);

        // 2. ONLY DECREASE 'tersedia' (stok stays the same)
        $buku->decrement('tersedia');

        return response()->json([
            'message' => 'Buku berhasil dipinjam',
            'jatuh_tempo' => $jatuhTempo->format('Y-m-d')
        ], 201);
    }

    public function destroy($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $buku = Buku::find($peminjaman->buku_id);
        
        if($buku) {
             // 3. ONLY INCREASE 'tersedia' (stok stays the same)
             $buku->increment('tersedia');
        }

        $peminjaman->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Buku berhasil dikembalikan'
        ]);
    }
}