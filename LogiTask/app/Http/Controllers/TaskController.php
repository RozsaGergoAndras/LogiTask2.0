<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\User;
use App\Models\Task_Content;
use App\Services\TaskDistributionService;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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
        //$user = Auth::user();
        $user = auth('sanctum')->user();
        $task = Task::where('worker', $user->id)->
                        whereNot('state', 2)
                        ->first();
        if($task == null){
            return response()->json(["success" => false,'error' => 'No assigned task for worker!'], 404);
        }
        $task->assigner = $task->assigner();
        $task->worker = $task->worker();
        $taskContents = Task_Content::where('task_id', $task->id)->get();
        return response()->json(["success" => true, 'task' => $task, 'taskContents'=> $taskContents], 200);
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
            // Validate the incoming request data
            $validated = $request->validate([
                'assigner' => 'required|exists:users,id',
                'worker' => 'nullable|exists:users,id',
                //'state' => 'required|integer',
                'task_type' => 'required|exists:task_types,id',
                'description' => 'required|string|max:255',
            ]);
        } catch (ValidationException $e) {
            return response()->json(["success" => false,'error' => $e->getMessage()], 404);
        }
        

        // Create a new task using the validated data
        try {
            $task = Task::create([
                'assigner' => $validated['assigner'],
                'worker' => $validated['worker'],
                'state' => 0,//'state' => $validated['state'],
                'state0date' => now(),
                'state1date' => null,
                'state2date' => null,
                'task_type' => $validated['task_type'],
                'description' => $validated['description'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success'=> false,'error'=> $e->getMessage()],404);
        }
        

        // Return a success response
        return response()->json([
            'message' => 'Task created successfully!',
            'task' => $task
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $task = Task::find($id);
        $user = $user = auth('sanctum')->user();
        if($task == null){
            return response()->json(["success" => false,'error' => 'Requested task not found!'], 404);
        }
        if($task->assigner != $user->id && $task->worker != $user->id && $user->role != 1){
            return response()->json(["success" => false,'error' => 'Unauthorized Access!'], 401);
        }
        
        if($task->Assigner != null){
            $task->assigner = $task->Assigner->name;
        }        
        if($task->Worker != null){
            $task->worker = $task->Worker->name;
        }
           
        $taskContents = Task_Content::where('task_id', $task->id)->get();
        return response()->json(["success" => true, 'task' => $task, 'taskContents'=> $taskContents], 200);
    }

    public function GetTasksAsAuthor(Request $request){

        try {
            $request->validate([
                'begin_date'=> 'required|date',
                'end_date'=> 'required|date|after:begin_date',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['success'=> false,'error'=> $e->getMessage()],400);
        }
        

        $user = auth('sanctum')->user();
        $tasks = Task::where('assigner', $user->id) // Filter by the user's ID as the assinger
                 //->whereNot('state', 2)
                 ->whereBetween('created_at', [$request->begin_date, $request->end_date]) // Filter by date range
                 ->get();

        if($tasks == null){
            return response()->json(["success" => false,'error' => 'No assigned task for worker!'], 404);
        }
        return response()->json(["success" => true, 'tasks' => $tasks], 200);
    }

    public function GetTasksAsWorker(Request $request){

        try {
            $request->validate([
                'begin_date'=> 'required|date',
                'end_date'=> 'required|date|after:begin_date',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['success'=> false,'error'=> $e->getMessage()],400);
        }
        

        $user = auth('sanctum')->user();
        $tasks = Task::where('worker', $user->id) // Filter by the user's ID as the assinger
                 //->whereNot('state', 2)
                 ->whereBetween('created_at', [$request->begin_date, $request->end_date]) // Filter by date range
                 ->get();

        if($tasks == null){
            return response()->json(["success" => false,'error' => 'No assigned task for worker!'], 404);
        }
        return response()->json(["success" => true, 'tasks' => $tasks], 200);
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
        $user = $user = auth('sanctum')->user();
        //return errors
        //return response()->json(["success" => false,'user'=> $user], 401);
        if (!$task) {
            return response()->json(["success" => false, 'error' => 'Task not found'], 404);
        }
        if($task->assigner != $user->id && $task->worker != $user->id && $user->role != 1){
            return response()->json(["success" => false,'error' => 'Unauthorized Access!'], 401);
        }

        try {
            $request->validate([
                'result' => 'nullable|string|max:25'
            ]);
        } catch (ValidationException $e) {
            return response()->json(["success" => false,'error' => $e->getMessage()], 400);
        }
        

        if($task->state == 0)
        {
            $task->state = 1;
            $task->state1date = now();
        }else
        if($task->state == 1)
        {
            $task->state = 2;
            $task->state2date = now();
            $task->result = $request->result;

            //free resource
            $workerid = $task->worker;
            $worker = User::find($workerid);
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
        return response()->json(["success" => true, 'task' => $task], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $task = Task::find($id);
        $user = $user = auth('sanctum')->user();
        //return errors
        //return response()->json(["success" => false,'user'=> $user], 401);
        if (!$task) {
            return response()->json(["success" => false, 'error' => 'Task not found'], 404);
        }
        if($task->assigner != $user->id && $user->role != 1){
            return response()->json(["success" => false,'error' => 'Unauthorized Access!'], 401);
        }

        $task->delete();
        return response()->json(['success'=> true,'message'=> 'Task deleted'],200);
    }
}
