<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            ['role_name' => 'Worker'],
            ['role_name' => 'Manager'],
            ['role_name' => 'Logistics Worker'],
            ['role_name' => 'Administration Worker'],
            ['role_name' => 'Virtual Agent'],
            ['role_name' => 'Robot'],
        ]);
    }
}
