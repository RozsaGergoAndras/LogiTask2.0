<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Task_type;
use App\Models\Role;

class TaskTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::first();  // Get the first role, assuming you already have roles in the roles table
        
        // Create task types
        Task_type::create([
            'type_name' => 'Task For Workers',
            'assignable_role' => 1,  // Assign the first role
        ]);

        Task_type::create([
            'type_name' => 'Task For Mangement',
            'assignable_role' => 2,
        ]);

        Task_type::create([
            'type_name' => 'Task For Logistics Worker',
            'assignable_role' => 3,
        ]);

        Task_type::create([
            'type_name' => 'Task For Administration',
            'assignable_role' => 4,
        ]);

        Task_type::create([
            'type_name' => 'Task For Virtual Agents',
            'assignable_role' => 5,
        ]);
        Task_type::create([
            'type_name' => 'Task For Robots',
            'assignable_role' => 6,
        ]);
    }
}
