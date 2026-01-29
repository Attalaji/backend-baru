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
        // This returns the user data (including nim, prodi, etc.) 
        // for the person currently logged in via Sanctum.
        return response()->json($request->user());
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        // Validate the data coming from your Next.js "Edit Profil" button
        $request->validate([
            'name'  => 'required|string|max:255',
            'no_hp' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        // Update the database
        $user->update($request->only(['name', 'email', 'no_hp']));

        return response()->json([
            'message' => 'Profile updated successfully!',
            'user'    => $user
        ]);
    }
}