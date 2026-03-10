<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\BukuController; 
use App\Http\Controllers\Api\PeminjamanController; 
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\AdminAuthController; 

/*
|--------------------------------------------------------------------------
| API PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

// Auth Dasar
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/admin/register-secret', [AdminAuthController::class, 'register']);
Route::post('/admin/login', [AdminAuthController::class, 'login']);

// Kelola Buku (Public bisa lihat daftar buku)
Route::get('/buku', [BukuController::class, 'index']);

// Data Peminjaman Admin (Public access jika ingin bypass auth sementara, 
// tapi sebaiknya dipindah ke dalam middleware nanti untuk keamanan)
Route::get('/admin/peminjaman', [PeminjamanController::class, 'indexAdmin']);
Route::post('/admin/peminjaman/{id}/kembali', [PeminjamanController::class, 'kembalikan']);

// Kelola User (Public Access)
Route::get('/users', [UserController::class, 'index']); 
Route::post('/users', [UserController::class, 'store']); 
Route::put('/users/{id}', [UserController::class, 'update']); 
Route::delete('/users/{id}', [UserController::class, 'destroy']);

// Kelola Buku - Action (Public Access)
Route::post('/buku', [BukuController::class, 'store']);
Route::put('/buku/{id}', [BukuController::class, 'update']);
Route::delete('/buku/{id}', [BukuController::class, 'destroy']);

// Profil & Log (Public Access)
Route::get('/user/profile', [ProfileController::class, 'getProfile']);
Route::get('/user/activity-logs', [ProfileController::class, 'getActivityLogs']);

/*
|--------------------------------------------------------------------------
| API PROTECTED ROUTES (Hanya Bisa Diakses Jika Membawa Token)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    // Auth & Profile
    Route::get('/user-profile', [AuthController::class, 'profile']);
    Route::put('/user-update', [AuthController::class, 'updateProfile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/logout', [AuthController::class, 'logout']); 

    // --- FITUR PEMINJAMAN USER ---
    // Route untuk memproses peminjaman baru (Fungsi store)
    Route::post('/peminjaman', [PeminjamanController::class, 'store']);
    // Route untuk menampilkan riwayat & pinjaman aktif di dashboard user
    Route::get('/user/peminjaman', [PeminjamanController::class, 'userPeminjaman']);
});

/*
|--------------------------------------------------------------------------
| ADMIN GUARD ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth:admin')->prefix('admin')->group(function () {
    Route::get('/profile', function (Request $request) {
        return $request->user(); 
    });
    Route::post('/logout', [AdminAuthController::class, 'logout']);
});