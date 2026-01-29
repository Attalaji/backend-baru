<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 1. PUBLIC ROUTES
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// 2. PROTECTED ROUTES (Requires Login/Sanctum Token)
Route::middleware('auth:sanctum')->group(function () {
    
    // Get detailed profile (NIM, Prodi, etc.)
    // We point this to your AuthController profile method
    Route::get('/user-profile', [AuthController::class, 'profile']);
    
    // Update profile data
    // We use PUT because your frontend Axios call uses .put()
    Route::put('/user-update', [AuthController::class, 'updateProfile']);

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']); 
});