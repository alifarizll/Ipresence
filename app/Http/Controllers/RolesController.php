<?php

namespace App\Http\Controllers;

use App\Models\roles;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $roles = roles::all();
        return response()->json($roles);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function show($id)
    {
        $roles = roles::find($id);
        if (!$roles) {
            return response()->json(['message' => 'Post not found'], 404);
        }
        return response()->json($roles);
    }
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $roles = roles::create($request->all());
        return response()->json($roles, 201);
    }

    /**
     * Display the specified resource.
     */
    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(roles $roles)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $roles = roles::find($id);
        if (!$roles) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $roles->name = $request->name;
        $roles->save();

        return response()->json($roles, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $roles = roles::find($id);
        if (!$roles) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $roles->delete();

        return response()->json(['message' => 'Post deleted'], 200);
    }
}
