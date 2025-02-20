<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class UserStateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('user_states')->insert([
            ['state_name' => 'Not Available'],  //1 nincs bent
            ['state_name' => 'Available'],      //2 beosztható
            ['state_name' => 'Work Assigned'],  //3 van kiosztott feladata
        ]);
    }
}
