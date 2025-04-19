<?php

namespace App\Services;
use App\Models\Packages;
use App\Models\Productionbatches;
use App\Models\Task;
use App\Models\Taskcontent;
use App\Models\Tasks;
use App\Models\User;
use App\Models\Task_type;
use Carbon\Carbon;
use DB;

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

        $taskDistributionMode = env('TASK_DISTRIBUTON', 'FIRST_FREE');

        foreach ($tasks as $task) {
            $workers =[];
            switch ($taskDistributionMode) {
                case 'FIRST_FREE':
                    $workers = $this->GetAllocatableWorkers($task->task_type);
                    break;
                case 'TASK_COUNT_BALANCED':
                    $workers = $this->GetAllocatableWorkersBalancedSql($task->task_type);
                    break;
                case 'TASK_TIME_BALANCED':
                    $workers = $this->GetAllocatableWorkersTimeBalanced($task->task_type);
                    break;
                
                default: #FIRST_FREE
                    $workers = $this->GetAllocatableWorkers($task->task_type);
                    break;
            }

            
            if(!$workers->isEmpty()){
                $task->worker = $workers[0]->id;
                $task->update();
                $workers[0]->user_state = 3;
                $workers[0]->update();
            }
        }

    }

    public function GetAllocatableWorkers($tasktype){
        $tasktype = Task_type::where('id', $tasktype)->first();
        //$assignableWorkers = User::where('role', $tasktype->assignableRole)->get();
        $assignableWorkers = User::where('role', $tasktype->assignable_role)
                         ->where('user_state', 2)
                         ->get();
        return $assignableWorkers;
    }

    public function GetAllocatableWorkersBalanced($tasktype){
        $tasktype = Task_type::where('id', $tasktype)->first();
        // Get the workers with the given role and state (active)
        $assignableWorkers = User::where('role', $tasktype->assignableRole)
                                 ->where('user_state', 2)
                                 ->get();
        
        // Get the current date minus 30 days
        $thirtyDaysAgo = Carbon::now()->subDays(30);
    
        // Add a custom attribute to each worker to count completed tasks in the past 30 days
        $assignableWorkers = $assignableWorkers->map(function($worker) use ($thirtyDaysAgo) {
            $completedTasksCount = Task::where('worker', $worker->id)
                                       ->where('state', 2) // Task completed
                                       ->where('state2date', '>=', $thirtyDaysAgo) //  in the last 30 days?
                                       ->count();
                                       
            //custom attribute
            $worker->completed_tasks_count = $completedTasksCount;
    
            return $worker;
        });
    
        // Sort workers by the number of completed tasks in the past 30 days, in descending order
        $assignableWorkers = $assignableWorkers->sortByDesc('completed_tasks_count');
    
        return $assignableWorkers;
    }

    public function GetAllocatableWorkersBalancedSql($tasktype){
        $tasktype = Task_type::where('id', $tasktype)->first();
        // Get the date 30 days ago
        $thirtyDaysAgo = Carbon::now()->subDays(30)->toDateString();  // Get date as a string in 'Y-m-d' format
    
        // Perform the query to get assignable workers with task counts
        $assignableWorkers = DB::table('users')
            ->select('users.id', 'users.name', 'users.role', DB::raw('COUNT(tasks.id) as completed_tasks_count'))
            ->leftJoin('tasks', function($join) use ($thirtyDaysAgo) {
                $join->on('users.id', '=', 'tasks.worker')
                     ->where('tasks.state', '=', 2)  // State = 2 (completed task)
                     ->where('tasks.state2date', '>=', $thirtyDaysAgo); // Task completed in the last 30 days
            })
            ->where('users.role', $tasktype->assignableRole)  // Filter users by role
            ->where('users.user_state', 2)  // Filter active users (state 2)
            ->groupBy('users.id', 'users.name', 'users.role')  // Group by user ID, name, and role
            ->orderBy('completed_tasks_count')  // Sort by number of completed tasks (asc)
            ->get();
    
        return $assignableWorkers;
    }

    public function GetAllocatableWorkersTimeBalanced($tasktype){
        $thirtyDaysAgo = Carbon::now()->subDays(30)->toDateString();  // Get date as a string in 'Y-m-d' format

        $assignableWorkers = DB::table('users')
            ->select(
                'users.id',
                'users.name',
                'users.role',
                DB::raw('SUM(TIMESTAMPDIFF(SECOND, tasks.state1date, tasks.state2date)) as total_work_time')
            )
            ->leftJoin('tasks', function ($join) use ($thirtyDaysAgo) {
                $join->on('users.id', '=', 'tasks.worker')
                    ->where('tasks.state', '=', 2)  // Only completed tasks
                    ->where('tasks.state2date', '>=', $thirtyDaysAgo);  // completed in the last 30 days
            })
            ->where('users.role', $tasktype->assignableRole)  // Filter users by role
            ->where('users.user_state', 2)  // Only active users (state 2)
            ->groupBy('users.id', 'users.name', 'users.role')  // Group by user ID, name, and role
            ->orderBy('total_work_time')  // Sort by total work time (ascending)
            ->get();

        return $assignableWorkers;
    }
}
