<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\users;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = users::with("roles")->get();
        return response()->json($users, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            'id' => 'required|integer',
            'nisn' => 'required|integer',
            'username' => 'required|string',
            'email' => 'required|string',
            'nama_lengkap' => 'required|string',
            'password' => 'required|string',
            'role_id' => 'required|integer',
            'tanggal_bergabung' => 'required|date',
            'asal_sekolah' => 'required|string',
            'roles' => 'required|string',

        ]);


        $users = users::create($request->all());
        return response()->json($users, 201);

        try {
            $users = users::create($validated);
            return response()->json(['message' => 'User created successfully', 'user' => $users], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Tidak dapat input data', 'error' => $e->getMessage()], 500);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $users = users::find($id);
        if (!$users) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json($users);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = users::find($id);
        if (!$user) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $user->nisn = $request->nisn;
        $user->username = $request->username;
        $user->nama_lengkap = $request->nama_lengkap;
        $user->tanggal_bergabung = $request->tanggal_bergabung;
        $user->asal_sekolah = $request->asal_sekolah;
        $user->roles = $request->roles;
        
        $user->save();

        return response()->json($user, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = users::find($id);
        if (!$user) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'Post deleted'], 200);
    }
}
