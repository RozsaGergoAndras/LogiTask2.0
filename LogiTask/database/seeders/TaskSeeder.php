<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\User;
use App\Models\Task_type;
use Carbon\Carbon;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch users and task types to assign in the tasks
        $assigner = User::first();  // Get the first user as the assigner
        $worker = User::skip(1)->first();  // Get the second user as the worker
        $taskType = Task_type::first();  // Get the first task type

        // Create 3 tasks with the provided schema
        Task::create([
            'assigner' => $assigner->id,
            'worker' => $worker->id,
            'state' => 0,  // Example state
            'state0date' => Carbon::now(),
            'state1date' => null,
            'state2date' => null,
            'task_type' => $taskType->id,
            'description' => 'Description for Task 1',
        ]);

        Task::create([
            'assigner' => $assigner->id,
            'worker' => $worker->id,
            'state' => 1,  // Example state
            'state0date' => Carbon::now()->subDays(2),
            'state1date' => Carbon::now(),
            'state2date' => null,
            'task_type' => $taskType->id,
            'description' => 'Description for Task 2',
        ]);

        Task::create([
            'assigner' => $assigner->id,
            'worker' => $worker->id,
            'state' => 2,  // Example state
            'state0date' => Carbon::now()->subMinutes(50),
            'state1date' => Carbon::now()->subMinutes(35),
            'state2date' => Carbon::now(),
            'task_type' => $taskType->id,
            'description' => 'Description for Task 3',
        ]);
    }
}
