<?php

namespace App\Http\Controllers;

use App\Models\Task_type;
use App\Http\Requests\StoreTask_typeRequest;
use App\Http\Requests\UpdateTask_typeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;

class TaskTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $types = Task_type::all();
        return response()->json(["success" => true,'data' => $types], 404);
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
        $validator = Validator::make($request->all(), [
            'type_name' => 'required|string|max:255|min:1',
            'assignable_role' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json(["success" => false, 'error' => $validator->errors()], 422);
        }

        $taskType = Task_type::create([
            'type_name' => $request->type_name,
            'assignable_role' => $request->assignable_role,
        ]);

        return response()->json(["success" => true, 'message' => 'Task Type created successfully', 'data' => $taskType], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $taskType = Task_type::find($id);

        if (!$taskType) {
            return response()->json(["success" => false, 'message' => 'Task Type not found'], 404);
        }

        // Return the TaskType
        return response()->json(["success" => true, 'data' => $taskType], 200);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task_type $task_type)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'type_name' => 'required|string|max:255|min:1',
            'assignable_role' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json(["success" => false, 'error' => $validator->errors()], 422);
        }

        $taskType = Task_type::find($id);
        if (!$taskType) {
            return response()->json(["success" => false, 'error' => 'Task Type not found'], 404);
        }

        // Update the TaskType
        $taskType->update([
            'type_name' => $request->type_name,
            'assignable_role' => $request->assignable_role,
        ]);

        return response()->json(["success" => true, 'message' => 'Task Type updated successfully', 'data' => $taskType], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $taskType = Task_type::find($id);

        if (!$taskType) {
            return response()->json(["success" => false, 'message' => 'Task Type not found'], 404);
        }

        $taskType->delete();
        return response()->json(["success" => true, 'message' => 'Task Type deleted successfully'], 200);
    }
}
