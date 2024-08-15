<?php

namespace App\Http\Controllers;  

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\users;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;


class AuthController extends Controller
{
    public function loginUser(Request $request)
{
    $request->validate([
        'username' => 'required|string',
        'email' => 'required|email',
    ]);

    $user = users::where('username', $request->username)
                ->where('email', $request->email)
                ->first();

    if (!$user) {
        Log::warning('Failed login attempt', ['username' => $request->username, 'ip' => $request->ip()]);

        return response()->json(['error' => 'Username atau Email anda salah salah'], 401);
    }

    Log::info('Successful login', ['username' => $request->username, 'ip' => $request->ip()]);

    try {
        // Buat token JWT
        $token = JWTAuth::fromUser($user);
    } catch (JWTException $e) {
        return response()->json(['error' => 'Tidak dapat membuat token'], 500);
    }

    // Mengirim respons berdasarkan jenis pengguna
    return response()->json([
        'message' => 'Successfully logged in',
        'user' => $user,
        'role' => $user->usertype,
        'token' => $token
    ]);
}

    public function logoutUser(Request $request)
    {
        try {
            // Invalidate the token
            JWTAuth::invalidate(JWTAuth::getToken());
            Log::info('Successful logout', ['username' => $request->username, 'ip' => $request->ip()]);
            return response()->json(['message' => 'Successfully logged out']);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['error' => 'Failed to logout, please try again'], 500);
        }
    }

}
