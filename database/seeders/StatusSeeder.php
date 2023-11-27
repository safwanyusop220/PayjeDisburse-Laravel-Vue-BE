<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Status::create(['name' => 'Submitted']);
        Status::create(['name' => 'Recommended']);
        Status::create(['name' => 'Approved']);
        Status::create(['name' => 'Rejected']);
        Status::create(['name' => 'Request']);
        Status::create(['name' => 'Processing']);
        Status::create(['name' => 'Proceed']);
    }
}
