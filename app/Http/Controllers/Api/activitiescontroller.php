<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\activities;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class activitiescontroller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $aktivitas = activities::with('task')->get();
        return response()->json($aktivitas);
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
        try {
            $validatedData = $request->validate([
                'id' => 'required|integer',
                'tasks_id' => 'required|integer',
                'nama_aktivitas' => 'required|string',
                'uraian' => 'required|string',
                'tanggal' => 'required|date',
                'status' => 'required|string',
                'users_id' => 'required|integer',
            ]);

            $aktivitas = Activities::create($validatedData);

            return response()->json(['message' => 'success create new activities', 'data' => $aktivitas], Response::HTTP_CREATED);

        } catch (ValidationException) {
            return response()->json(['message' => 'Missing or invalid field'], 404);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $aktivitas = Activities::with('task')->find($id);
            
        if (!$aktivitas) {
            return response()->json(['message' => 'activities not found'], 404);
        }
            return response()->json($aktivitas);
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
    public function update(Request $request, int $id)
    {
        $aktivitas = activities::find($id);

        if (!$aktivitas) {
            return response()->json(['message' => 'activities not found'], Response::HTTP_NOT_FOUND);
        }
    
        $validatedData = $request->validate([
            'tasks_id' => 'nullable|integer',
            'nama_aktivitas' => 'nullable|string|max:255',
            'uraian' => 'nullable|string|max:255',
            'tanggal' => 'nullable|date',  
            'users_id' => 'nullable|integer',
        ]);
    
        $updateData = array_filter($validatedData, function ($value) {
            return !is_null($value);
        });
    
        $aktivitas->update($updateData);
    
        return response()->json(['message' => 'update activities success'], Response::HTTP_OK);
    }

    public function updateStatus(Request $request, int $id)
    {
        $aktivitas = Activities::find($id);

        if (!$aktivitas) {
            return response()->json(['message' => 'activities not found'], Response::HTTP_NOT_FOUND);
        }

        // Validasi status jika diperlukan
        $request->validate([
            'status' => 'required|string|in:PROSES,SELESAI',
        ]);

        // Update status
        $aktivitas->status = $request->status;
        $aktivitas->save();

        return response()->json(['message' => 'Status updated successfully', 'data' => $aktivitas], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $aktivitas = activities::find($id);

        if (!$aktivitas) {
            return response()->json(['message' => 'activities not found'], Response::HTTP_NOT_FOUND);
        }

        $aktivitas->delete();
        return response()->json(['message' => 'activities deleted successfully']);
    }
}
