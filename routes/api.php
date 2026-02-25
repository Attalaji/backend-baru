<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\BukuController; 
use App\Http\Controllers\Api\ProfileController;

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

// BUKU ROUTES (Kelola Buku)
Route::get('/buku', [BukuController::class, 'index']);
Route::post('/buku', [BukuController::class, 'store']);
Route::put('/buku/{id}', [BukuController::class, 'update']);
Route::delete('/buku/{id}', [BukuController::class, 'destroy']);

// FRONTEND MATCHING ROUTES (No token required for testing UI)
// Note: Move these inside the auth:sanctum group below once you implement login on the frontend!
Route::get('/user/profile', [ProfileController::class, 'getProfile']);
Route::get('/user/activity-logs', [ProfileController::class, 'getActivityLogs']);


// --- PROTECTED ROUTES (Requires Sanctum Token) ---
Route::middleware('auth:sanctum')->group(function () {
    
    // Profile Management (Original Routes kept for safety)
    Route::get('/user-profile', [AuthController::class, 'profile']);
    Route::put('/user-update', [AuthController::class, 'updateProfile']);
    Route::post('/logout', [AuthController::class, 'logout']); 

});