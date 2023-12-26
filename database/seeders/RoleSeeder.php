<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'Administrator', 'description' => 'Manages administration activities']);
        Role::create(['name' => 'Recommender', 'description' => 'Review and recommend all program & recipient request']);
        Role::create(['name' => 'Approver', 'description' => 'Review and approve all program & recipient request']);
        Role::create(['name' => 'User', 'description' => 'Standard & basic access']);
    }
}
