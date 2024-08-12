<?php

namespace App\Http\Controllers;  

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\users;
use Tymon\JWTAuth\Facades\JWTAuth;


class AuthController extends Controller
{
    public function loginUser(Request $request)
{
    $request->validate([
        'username' => 'required|string',
        'email' => 'required|email',
    ]);

    // Mencari pengguna berdasarkan nama_lengkap dan email
    $user = users::where('username', $request->username)
                ->where('email', $request->email)
                ->first();

    if (!$user) {
        return response()->json(['error' => 'email atau nama lengkap salah'], 401);
    }

    // Mengirim respons berdasarkan jenis pengguna
    if ($user->usertype === 'admin') {
        return response()->json(['message' => 'Successfully logged in', 'user' => $user, 'role' => 'admin']);
    } else if ($user->usertype === 'user') {
        return response()->json(['message' => 'Successfully logged in', 'user' => $user, 'role' => 'user']);
    } else {
        return response()->json(['error' => 'Jenis pengguna tidak dikenali'], 401);
    }
}

}
