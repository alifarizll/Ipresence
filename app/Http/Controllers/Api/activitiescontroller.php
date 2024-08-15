<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activities;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class ActivitiesController extends Controller
{
    public function index(Request $request)
    {
        $query = Activities::query();
    
        if ($request->has('users_id')) {
            $query->where('users_id', $request->users_id);
        }
    
        $aktivitas = $query->with(['user'])->get();
    
        if ($aktivitas->isEmpty()) {
            Log::info('No activities found for users_id: ' . $request->users_id);// log history
        }
    
        return response()->json($aktivitas, 200);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nama_aktivitas' => 'required|string',
                'uraian' => 'required|string',
                'tanggal' => 'required|date'
            ]);

            $aktivitas = Activities::create([
                'nama_aktivitas' => $validatedData['nama_aktivitas'],
                'uraian' => $validatedData['uraian'],
                'tanggal' => $validatedData['tanggal'] ?? now(),
            ]);

            return response()->json(['message' => 'success create new activities', 'data' => $aktivitas], Response::HTTP_CREATED);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Missing or invalid field', 'errors' => $e->errors()], 422);
        }
    }

    public function show(int $id)
    {
        $aktivitas = Activities::with('user')->find($id);
            
        if (!$aktivitas) {
            return response()->json(['message' => 'activities not found'], 404);
        }
        
        return response()->json($aktivitas);
    }

    public function update(Request $request, int $id)
    {
        $aktivitas = Activities::find($id);

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
    
        $validatedData['tanggal'] = $validatedData['tanggal'] ?? Carbon::today()->toDateString();
    
        $aktivitas->update($validatedData);
    
        return response()->json(['message' => 'update activities success'], Response::HTTP_OK);
    }

    public function updateStatus(Request $request, int $id)
    {
        $aktivitas = Activities::find($id);

        if (!$aktivitas) {
            return response()->json(['message' => 'activities not found'], Response::HTTP_NOT_FOUND);
        }

        $request->validate([
            'status' => 'required|string|in:PROSES,SELESAI',
        ]);

        $aktivitas->status = $request->status;
        $aktivitas->save();

        return response()->json(['message' => 'Status updated successfully', 'data' => $aktivitas], Response::HTTP_OK);
    }

    public function destroy(int $id)
    {
        $aktivitas = Activities::find($id);

        if (!$aktivitas) {
            return response()->json(['message' => 'activities not found'], Response::HTTP_NOT_FOUND);
        }

        $aktivitas->delete();
        return response()->json(['message' => 'activities deleted successfully']);
    }
}
