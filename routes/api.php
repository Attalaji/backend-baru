<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\BukuController; 
use App\Http\Controllers\Api\PeminjamanController; 
// IMPORT ADMIN CONTROLLER
use App\Http\Controllers\Api\AdminAuthController; 

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --- PUBLIC ROUTES (No Token Required) ---

// Auth User
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// --- NEW: Public Admin Routes ---
Route::post('/admin/register-secret', [AdminAuthController::class, 'register']); // For Postman
Route::post('/admin/login', [AdminAuthController::class, 'login']);

// Users (Kelola User)
Route::get('/users', [UserController::class, 'index']); 
Route::post('/users', [UserController::class, 'store']); 
Route::put('/users/{id}', [UserController::class, 'update']); 
Route::delete('/users/{id}', [UserController::class, 'destroy']);

// 2. BUKU ROUTES (Kelola Buku)
Route::get('/buku', [BukuController::class, 'index']);
Route::post('/buku', [BukuController::class, 'store']);
Route::put('/buku/{id}', [BukuController::class, 'update']);
Route::delete('/buku/{id}', [BukuController::class, 'destroy']);


// --- PROTECTED USER ROUTES (Requires User Sanctum Token) ---
Route::middleware('auth:sanctum')->group(function () {
    
    // Profile Management
    Route::get('/user-profile', [AuthController::class, 'profile']);
    Route::put('/user-update', [AuthController::class, 'updateProfile']);
    
    // Security and Activity
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::get('/activity-logs', [UserController::class, 'getActivityLogs']);
    
    Route::post('/logout', [AuthController::class, 'logout']); 

    // Fitur Peminjaman
    Route::post('/peminjaman', [PeminjamanController::class, 'store']);
    
    // Admin Actions (Currently under user sanctum, consider moving to admin group below if strictly for admins)
    Route::get('/admin/peminjaman', [PeminjamanController::class, 'indexAdmin']);
    Route::post('/admin/peminjaman/{id}/kembali', [PeminjamanController::class, 'kembalikan']);
});

// --- NEW: PROTECTED ADMIN ROUTES (Requires Admin Sanctum Token) ---
// This uses the 'admin' guard we defined in config/auth.php
Route::middleware('auth:admin')->prefix('admin')->group(function () {
    Route::get('/profile', function (Request $request) {
        return $request->user(); // Returns current Admin data
    });
    
    Route::post('/logout', [AdminAuthController::class, 'logout']);

    // Move your administrative Peminjaman routes here if you want 
    // to ensure ONLY users from the 'admins' table can access them.
});