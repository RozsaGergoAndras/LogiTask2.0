<?php

namespace App\Services;
use App\Models\Packages;
use App\Models\Productionbatches;
use App\Models\Task;
use App\Models\Taskcontent;
use App\Models\Tasks;
use App\Models\User;

class TaskDistributionService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    

    public function HandleTaskDistribution()
    {
        //worker nélküli elvégzetlen taskok
        $tasks = Task::whereNull('deleted_at')
             ->whereNull('worker')
             ->get();
        if ($tasks->isEmpty()) {
            // No tasks found
            //return response()->json(['message' => 'No tasks found'], 404);
            return;
        }

        foreach ($tasks as $task) {
            $workers = $this->GetAllocatableWorkers($task->task_type());
            if(!$workers->isEmpty()){
                $task->worker = $workers[0]->id;
                $task->update();
                $workers[0]->user_state = 3;
                $workers[0]->update();
            }
        }

    }

    public function GetAllocatableWorkers($tasktype){
        //$assignableWorkers = User::where('role', $tasktype->assignableRole)->get();
        $assignableWorkers = User::where('role', $tasktype->assignableRole)
                         ->where('user_state', 2)
                         ->get();
        return $assignableWorkers;
    }
}
