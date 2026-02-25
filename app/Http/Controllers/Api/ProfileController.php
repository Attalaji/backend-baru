<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Get the authenticated user's profile data.
     */
    public function getProfile(Request $request)
    {
        // For testing the frontend UI without a token, we return a mock user if not logged in.
        // Once your login system works, change this back to: return response()->json($request->user());
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'id' => 1,
                'name' => 'Demo Student',
                'email' => 'student@frestea.com',
                'nim' => '20260001',
                'prodi' => 'Teknik Informatika',
                'kelas' => 'TI-3A',
                'no_hp' => '081234567890'
            ]);
        }

        return response()->json($user);
    }

    /**
     * Get the user's activity logs.
     * Currently returns mock data so the frontend UI does not crash.
     */
    public function getActivityLogs(Request $request)
    {
        // Mock data matching your frontend ActivityLog interface
        $logs = [
            [
                'id' => 1,
                'activity' => 'Meminjam Buku',
                'date' => now()->subDays(1)->format('Y-m-d H:i'),
                'details' => 'Meminjam buku "Belajar Laravel 10"'
            ],
            [
                'id' => 2,
                'activity' => 'Login',
                'date' => now()->subDays(2)->format('Y-m-d H:i'),
                'details' => 'Berhasil login ke sistem'
            ]
        ];

        return response()->json($logs);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name'  => 'required|string|max:255',
            'no_hp' => 'required|string',
            'email' => 'required|email|unique:users,email,' . ($user ? $user->id : ''),
        ]);

        if ($user) {
            $user->update($request->only(['name', 'email', 'no_hp']));
        }

        return response()->json([
            'message' => 'Profile updated successfully!',
            'user'    => $user
        ]);
    }
}