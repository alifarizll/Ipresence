<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Users;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mengambil semua user dengan relasi roles
        $users = Users::with('roles')->get();
        return response()->json($users, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createUser(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'nisn' => 'required|integer',
                'email' => 'required|email|unique:users,email',
                'username' => 'required|string',
                'role_id' => 'required|integer',
                'asal_sekolah' => 'required|string',
            ]);

            // Membuat user baru
            $user = Users::create([
                'nisn' => $validated['nisn'],
                'email' => $validated['email'],
                'username' => $validated['username'],
                'nama_lengkap' => $validated['nama_lengkap'] ?? null,
                'asal_sekolah' => $validated['asal_sekolah'],
                'tanggal_bergabung' => $validated['tanggal_bergabung'] ?? now(),
                'role_id' => $validated['role_id'],
                'usertype' => 'user',
                'img' => $validated['img'] ?? 'https://cdn-icons-png.flaticon.com/512/149/149071.png',
            ]);

            return response()->json(['message' => 'success', 'data' => $user], Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Missing or invalid field'], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nisn' => 'required|integer',
            'email' => 'required|email',
            'nama_lengkap' => 'required|string',
            'role_id' => 'required|integer',
            'tanggal_bergabung' => 'required|date',
            'asal_sekolah' => 'required|string',
            'img' => 'required|string',
        ]);

        try {
            $user = Users::create($validated);
            return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Tidak dapat input data', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = Users::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = Users::find($id);
        if (!$user) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $user->nisn = $request->nisn ?? $user->nisn;
        $user->email = $request->email ?? $user->email;
        $user->username = $request->username ?? $user->username;
        $user->nama_lengkap = $request->nama_lengkap ?? $user->nama_lengkap;
        $user->tanggal_bergabung = $request->tanggal_bergabung ?? $user->tanggal_bergabung;
        $user->asal_sekolah = $request->asal_sekolah ?? $user->asal_sekolah;
        $user->usertype = $request->usertype ?? $user->usertype;
        $user->role_id = $request->role_id ?? $user->role_id;
        $user->img = $request->img ?? $user->img;

        $user->save();

        return response()->json($user, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = Users::find($id);
        if (!$user) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'Post deleted'], 200);
    }
}
