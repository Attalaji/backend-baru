<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Buku;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PeminjamanController extends Controller
{
    /**
     * UNTUK ADMIN: Menampilkan semua data pinjaman aktif dan riwayat
     */
    public function indexAdmin()
    {
        try {
            $active = Peminjaman::with(['user', 'buku'])
                ->whereNull('tgl_kembali')
                ->get();

            $history = Peminjaman::with(['user', 'buku'])
                ->whereNotNull('tgl_kembali')
                ->get();

            return response()->json([
                'active' => $active,
                'history' => $history
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * UNTUK USER: Menampilkan data pinjaman milik user yang sedang login
     */
    public function userPeminjaman()
    {
        try {
            $userId = auth()->id(); // Mengambil ID dari token Sanctum

            $active = Peminjaman::with('buku')
                ->where('user_id', $userId)
                ->whereNull('tgl_kembali')
                ->get();

            $history = Peminjaman::with('buku')
                ->where('user_id', $userId)
                ->whereNotNull('tgl_kembali')
                ->get();

            return response()->json([
                'active' => $active,
                'history' => $history
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * PROSES PINJAM BUKU (USER/SISWA)
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'buku_id' => 'required|exists:buku,id',
            ]);

            // 1. Ambil data buku untuk cek stok
            $buku = Buku::findOrFail($request->buku_id);
            
            if ($buku->tersedia <= 0) {
                return response()->json(['message' => 'Maaf, stok buku ini sedang habis.'], 422);
            }

            // 2. Buat data peminjaman baru
            $peminjaman = Peminjaman::create([
                'user_id'     => auth()->id(), 
                'buku_id'     => $request->buku_id,
                'tgl_pinjam'  => Carbon::now()->toDateString(),
                'jatuh_tempo' => Carbon::now()->addDays(5)->toDateString(), 
                'status'      => 'Aktif',
                'denda'       => 0,
            ]);

            // 3. Kurangi stok tersedia di tabel buku
            $buku->decrement('tersedia');

            return response()->json([
                'message' => 'Peminjaman berhasil dikonfirmasi!',
                'data' => $peminjaman
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * PROSES KEMBALIKAN BUKU (ADMIN)
     */
    public function kembalikan($id)
    {
        try {
            // 1. Cari data peminjaman
            $peminjaman = Peminjaman::findOrFail($id);

            // Cek jika buku sudah pernah dikembalikan sebelumnya
            if ($peminjaman->tgl_kembali !== null) {
                return response()->json(['message' => 'Buku ini sudah dikembalikan sebelumnya.'], 400);
            }

            // 2. Update data peminjaman (isi tanggal kembali)
            $peminjaman->update([
                'tgl_kembali' => Carbon::now()->toDateString(),
                'status' => 'Selesai'
            ]);

            // 3. Tambahkan kembali stok tersedia di tabel buku
            $buku = Buku::find($peminjaman->buku_id);
            if ($buku) {
                $buku->increment('tersedia');
            }

            return response()->json([
                'message' => 'Buku berhasil dikembalikan dan stok telah diperbarui.',
                'stok_sekarang' => $buku ? $buku->tersedia : 0
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}