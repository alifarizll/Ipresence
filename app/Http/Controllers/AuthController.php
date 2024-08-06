<?php

namespace App\Http\Controllers;  

use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\users;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string',
            'email' => 'required|string|email',
        ]);

        $user = users::where('nama_lengkap', $request->nama_lengkap)
                    ->where('email', $request->email)
                    ->first();


        if (!$user) {
            return response()->json(['error' => 'email atau nama lengkap salah'], 401);
        }


        return response()->json(['massage' => 'Successfully logged in']);
    }

}
