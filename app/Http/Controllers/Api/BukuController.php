<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    /**
     * Get all books from the 'buku' table
     */
    public function index() {
        return response()->json(Buku::all(), 200);
    }

    /**
     * Store a new book with auto-generated code
     */
    public function store(Request $request) {
        $validated = $request->validate([
            'judul'        => 'required|string',
            'penulis'      => 'required|string',
            'kategori'     => 'required|string',
            'penerbit'     => 'required|string',
            'tahun_terbit' => 'required|integer',
            'stok'         => 'required|integer',
            'tersedia'     => 'required|integer',
        ]);

        // Logic for auto-generating Kode Buku
        $prefixes = [
            "Pemrograman"    => "PWL", 
            "Jaringan"       => "JK", 
            "Database"       => "DB", 
            "Multimedia"     => "MM", 
            "Sistem Operasi" => "SO"
        ];
        
        $prefix = $prefixes[$request->kategori] ?? "BUKU";
        
        // Find how many books already exist with this prefix
        $count = Buku::where('kode_buku', 'like', $prefix . '-%')->count();
        
        // Generate code like PWL-001
        $validated['kode_buku'] = $prefix . '-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        $buku = Buku::create($validated);
        return response()->json($buku, 201);
    }

    /**
     * Update an existing book
     */
    public function update(Request $request, $id) {
        $buku = Buku::findOrFail($id);
        $buku->update($request->all());
        return response()->json($buku, 200);
    }

    /**
     * Delete a book
     */
    public function destroy($id) {
        Buku::destroy($id);
        return response()->json(null, 204);
    }
}