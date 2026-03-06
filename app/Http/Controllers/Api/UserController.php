<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon; // For date formatting

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::all(), 200);
    }

    public function store(Request $request)
    {
        $user = User::create([
            'name'     => $request->name,
            'nim'      => $request->nim,
            'prodi'    => $request->prodi,
            'kelas'    => $request->kelas,
            'email'    => $request->email,
            'no_hp'    => $request->no_hp,
            'status'   => $request->status ?? 'Aktif',
            'password' => Hash::make('password123'),
        ]);
        return response()->json(['success' => true, 'user' => $user], 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'name'   => $request->name,
            'nim'    => $request->nim,
            'prodi'  => $request->prodi,
            'kelas'  => $request->kelas,
            'email'  => $request->email,
            'no_hp'  => $request->no_hp,
            'status' => $request->status,
        ]);
        return response()->json(['success' => true, 'user' => $user]);
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    /**
     * NEW: Method to handle the Activity Log Sidebar in Profile
     */
    public function getActivityLogs(Request $request)
    {
        // For now, we return these hardcoded logs. 
        // Later, we can make this dynamic by creating a logs table.
        return response()->json([
            [
                'id' => 1,
                'description' => 'Berhasil login ke sistem',
                'timestamp' => Carbon::now()->format('d M Y, H:i'),
                'icon_type' => 'login'
            ],
            [
                'id' => 2,
                'description' => 'Memperbarui informasi profil',
                'timestamp' => Carbon::now()->subMinutes(15)->format('d M Y, H:i'),
                'icon_type' => 'profile'
            ],
            [
                'id' => 3,
                'description' => 'Mengubah password keamanan',
                'timestamp' => Carbon::now()->subDay()->format('d M Y, H:i'),
                'icon_type' => 'password'
            ]
        ]);
    }
}