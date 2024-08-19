<?php

namespace App\Http\Controllers;  

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\users;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;


class AuthController extends Controller
{
    public function loginUser(Request $request)
{
    $this->validate($request, [
        'username' => 'required|string',
        'email' => 'required|email',
    ]);

    $maxAttempts = 5;
    $decayMinutes = 1;

    if (RateLimiter::tooManyAttempts($request->ip(), $maxAttempts)) {
        return response()->json(['error' => 'Terlalu banyak percobaan login. Coba lagi nanti.'], 429);
    }

    // Mencari pengguna berdasarkan nama_lengkap dan email
    $user = users::where('username', $request->username)
                ->where('email', $request->email)
                ->first();

    if (!$user) {
        RateLimiter::hit($request->ip(), $decayMinutes);
        Log::warning('Failed login attempt', ['username' => $request->username, 'ip' => $request->ip()]);
        return response()->json(['error' => 'email atau nama lengkap salah'], 401);
    }
    
    RateLimiter::clear($request->ip());
    Log::info('Successful login', ['username' => $request->username, 'ip' => $request->ip()]);

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