<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\users;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;

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
    public function createUser(Request $request)
    {
        try{
        $validated = $request->validate([


            'nisn' => 'required|integer',
            'email' => 'required|email|unique:users,email',
            'nama_lengkap' => 'required|string',
            'role_id' => 'required|integer',
            'asal_sekolah' => 'required|string',


        ]);


        $users = users::create([
            
            'nisn' => $validated['nisn'],
            'email' => $validated['email'],
            'nama_lengkap' => $validated['nama_lengkap'],
            'asal_sekolah' => $validated['asal_sekolah'],
            'tanggal_bergabung' => $validated['tanggal_bergabung'] ?? now(),
            'role_id' => $validated['role_id'],
            'usertype' => 'user',
            
        
        ]);

        return response()->json(['message' => 'susccess' , 'data' => $users], Response::HTTP_CREATED);

        } catch (ValidationException) {
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
