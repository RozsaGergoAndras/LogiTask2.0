<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Services\TaskDistributionService;
use Auth;
use Request;

class TaskController extends Controller
{
    protected $taskService;

    // Inject the service into the constructor
    public function __construct(TaskDistributionService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $task = Task::where('worker', $user->id)->
                        whereNot('state', 2)
                        ->first();
        if($task == null){
            return response()->json(["success" => false,'error' => 'No assigned task for worker!'], 404);
        }
        return response()->json(["success" => true, 'task' => $task], 200);
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
    public function store(StoreTaskRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $task = Task::find($id);
        if($task == null){
            return response()->json(["success" => false,'error' => 'Requested task not found!'], 404);
        }
        if($task->assigner() != Auth::user() && $task->Worker() && Auth::user()->role()->role_name != 'Manager'){
            return response()->json(["success" => false,'error' => 'Unauthorized Access!'], 401);
        }
        return response()->json(["success" => true, 'task' => $task], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $task = Task::find($id);

        //return errors
        if (!$task) {
            return response()->json(["success" => false, 'error' => 'Task not found'], 404);
        }
        if($task->assigner() != Auth::user() && $task->Worker() && Auth::user()->role()->role_name != 'Manager'){
            return response()->json(["success" => false,'error' => 'Unauthorized Access!'], 401);
        }

        // Validate the request data
        /*$validated = $request->validate([
            'title' => 'required|string|max:255', // example validation rules
            'description' => 'nullable|string',   // modify according to your model
            'status' => 'required|in:pending,completed', // example of status field
        ]);*/
        if($task->state == 0)
        {
            $task->state = 1;
            $task->state1date = now();
        }else
        if($task->state == 1)
        {
            $task->state = 2;
            $task->state2date = now();
            //free resource
            $worker = $task->worker();
            if($worker->user_state != 1){
                $worker->user_state = 2;
                $worker->update();
            }
            //trigger task redist.
            $this->taskService->HandleTaskDistribution();
        }else
        if($task->state == 2)
        {
            return response()->json(["success" => false,'error' => 'Task was already compleated!'], 424);
        }

        // Update the task with the validated data
        //$task->update($validated);
        $task->update();

        // Return the updated task in the response
        return response()->json(['task' => $task], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        //
    }
}
