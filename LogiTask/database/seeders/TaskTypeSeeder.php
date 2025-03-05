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
            'assignable_role' => $role->id,  // Assign the first role
        ]);

        Task_type::create([
            'type_name' => 'Task For Administration',
            'assignable_role' => $role->id,
        ]);

        Task_type::create([
            'type_name' => 'Task For Virtual Agents',
            'assignable_role' => $role->id,
        ]);
    }
}
