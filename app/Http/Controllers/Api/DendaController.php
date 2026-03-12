<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peminjaman;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // <--- Tambahkan ini untuk akses tabel denda

class DendaController extends Controller
{
    public function index()
    {
        $now = Carbon::now();

        // Ambil data peminjaman yang telat
        $peminjamanBermasalah = Peminjaman::with(['user', 'buku'])
            ->where(function($query) use ($now) {
                $query->whereNull('tgl_kembali')->where('jatuh_tempo', '<', $now->toDateString())
                      ->orWhereRaw('tgl_kembali > jatuh_tempo');
            })
            // Filter agar yang sudah dibayar (ada di tabel denda) tidak muncul lagi
            ->whereDoesntHave('denda') 
            ->get();

        $dataDenda = $peminjamanBermasalah->map(function ($loan) use ($now) {
            $jatuhTempo = Carbon::parse($loan->jatuh_tempo);
            $tglAkhir = $loan->tgl_kembali ? Carbon::parse($loan->tgl_kembali) : $now;

            // Hitung selisih hari
            $hariTerlambat = $jatuhTempo->diffInDays($tglAkhir, false);
            $jumlahHari = $hariTerlambat > 0 ? $hariTerlambat : 0;
            $totalDenda = $jumlahHari * 1000;

            return [
                'id' => $loan->id,
                'nim' => $loan->user->nim ?? '-',
                'nama' => $loan->user->name ?? 'User Terhapus',
                'buku' => $loan->buku->judul ?? 'Buku Terhapus',
                'pinjam' => $loan->tgl_pinjam,
                'tempo' => $loan->jatuh_tempo,
                'kembali' => $loan->tgl_kembali ?? 'Belum Kembali',
                'late' => $jumlahHari . " Hari",
                'denda' => $totalDenda,
                'status' => 'Belum Lunas'
            ];
        });

        return response()->json($dataDenda);
    }

    // --- FUNGSI BARU UNTUK PROSES BAYAR ---
    public function bayar(Request $request, $id)
    {
        try {
            $loan = Peminjaman::findOrFail($id);

            // Hitung denda final
            $jatuhTempo = Carbon::parse($loan->jatuh_tempo);
            $tglAkhir = $loan->tgl_kembali ? Carbon::parse($loan->tgl_kembali) : Carbon::now();
            $hariTerlambat = $jatuhTempo->diffInDays($tglAkhir, false);
            $jumlahHari = $hariTerlambat > 0 ? $hariTerlambat : 0;
            $totalDenda = $jumlahHari * 1000;

            // Masukkan data ke tabel denda (Insert ke PHPMyAdmin)
            DB::table('denda')->insert([
                'peminjaman_id' => $loan->id,
                'total_denda'   => $totalDenda,
                'status'        => 'Lunas',
                'tgl_bayar'     => now(),
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Pembayaran berhasil dicatat ke database!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}