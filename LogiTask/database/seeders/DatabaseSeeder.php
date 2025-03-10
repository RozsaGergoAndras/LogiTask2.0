<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            RoleSeeder::class,    // Seed roles first
            UserStateSeeder::class,    // Seed users_states after roles
            TaskTypeSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        User::factory()->create([
            'name' => 'Jani',
            'email' => 'jani@example.com',
        ]);

        $this->call([
            TaskSeeder::class,  // miután készek a teszt userek mehetnek a test taskok
        ]);
    }
}
