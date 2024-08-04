<?php

namespace App\Http\Controllers;    // ini cuma buat belajar

use Illuminate\Http\Request;
use App\Models\users;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|min:8',            
        ]);

        $users = users::create([
            'email' => $request->email,
            'password' => $request->password,
            'role_id' => 3,
            'nisn' => 0000000000,
            'username' => 'undefined',
            'nama_lengkap' => 'undefined',
            'asal_sekolah' => 'undefined',
            'tanggal_bergabung' => '2001-01-01',
            'id' => 20,

        ]);

        $user = users::where('email', $request->email)->first();

        if (! $user || ! $user->password) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return response()->json(['message' => 'Logged in'] , 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out'], 200);
    }
}
