<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
// 1. IMPORT YOUR NEW BUKU CONTROLLER
use App\Http\Controllers\Api\BukuController; 

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --- PUBLIC ROUTES (No Token Required) ---

// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Users (Kelola User)
Route::get('/users', [UserController::class, 'index']); 
Route::post('/users', [UserController::class, 'store']); 
Route::put('/users/{id}', [UserController::class, 'update']); 
Route::delete('/users/{id}', [UserController::class, 'destroy']);

// 2. BUKU ROUTES (Kelola Buku)
// We keep these public so your Next.js frontend can access them easily during dev
Route::get('/buku', [BukuController::class, 'index']);
Route::post('/buku', [BukuController::class, 'store']);
Route::put('/buku/{id}', [BukuController::class, 'update']);
Route::delete('/buku/{id}', [BukuController::class, 'destroy']);


// --- PROTECTED ROUTES (Requires Sanctum Token) ---
Route::middleware('auth:sanctum')->group(function () {
    
    // Profile Management
    Route::get('/user-profile', [AuthController::class, 'profile']);
    Route::put('/user-update', [AuthController::class, 'updateProfile']);
    Route::post('/logout', [AuthController::class, 'logout']); 

});