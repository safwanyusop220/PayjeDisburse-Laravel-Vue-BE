<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create(['name' => 'Abdillah Safwan', 'email' => 'safwan@edaran.com', 'password' => 'secret']);
        User::create(['name' => 'Nadiatul Najihah', 'email' => 'nadia@edaran.com', 'password' => 'secret']);
    }
}