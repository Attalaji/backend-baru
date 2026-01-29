<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Handle User Registration
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'nim'      => 'required|string|unique:users',
            'prodi'    => 'required|string',
            'kelas'    => 'required|string',
            'email'    => 'required|string|email|max:255|unique:users',
            'no_hp'    => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'nim'      => $request->nim,
            'prodi'    => $request->prodi,
            'kelas'    => $request->kelas,
            'email'    => $request->email,
            'no_hp'    => $request->no_hp,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success'      => true,
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user
        ], 201);
    }

    /**
     * Handle User Login
     */
    public function login(Request $request)
    {
        $request->validate([
            'nim'      => 'required|string',
            'password' => 'required',
        ]);

        $user = User::where('nim', $request->nim)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'NIM atau Password salah.'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success'      => true,
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user
        ]);
    }

    /**
     * Get Authenticated User Profile
     */
    public function profile(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Update Authenticated User Profile
     * This fixes the "Gagal memperbarui profil" error
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        // Validation - ensures NIM and Email remain unique except for the current user
        $validator = Validator::make($request->all(), [
            'name'  => 'required|string|max:255',
            'nim'   => 'required|string|unique:users,nim,' . $user->id,
            'prodi' => 'required|string',
            'kelas' => 'required|string',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'no_hp' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        // Update user data
        $user->update([
            'name'  => $request->name,
            'nim'   => $request->nim,
            'prodi' => $request->prodi,
            'kelas' => $request->kelas,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui!',
            'user'    => $user
        ]);
    }

    /**
     * Handle User Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }
}