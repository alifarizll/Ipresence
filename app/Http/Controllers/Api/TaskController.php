<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::all();
        return response()->json($tasks);
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
        try{
            $validatedData = $request->validate([

            'nama' => 'required|string',
            'deskripsi' => 'required|string',
        ]);

        $task = Task::create($validatedData);

        return response()->json(['message' => 'success add new tasks','data' => $task], Response::HTTP_CREATED);
        
        } catch (ValidationException) {
            return response()->json(['message' => 'Missing or invalid field'], 404);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($task);
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
        $task = Task::find($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], Response::HTTP_NOT_FOUND);
        }

        $validatedData = $request->validate([
            'nama' => 'nullable|string',
            'deskripsi' => 'nullable|string',
        ]);

        $updateData = array_filter($validatedData, function ($value) {
            return !is_null($value);
        });

        $task->update($updateData);

        return response()->json(['message' => 'success update']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $task = Task::find($id); 

        if (!$task) {
            return response()->json(['message' => 'Task not found'], Response::HTTP_NOT_FOUND);
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }
}
