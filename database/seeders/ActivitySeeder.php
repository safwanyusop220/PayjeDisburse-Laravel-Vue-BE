<?php

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Activity::create(['name' => 'Created']);
        Activity::create(['name' => 'Updated']);
        Activity::create(['name' => 'Deleted']);
        Activity::create(['name' => 'Recommended']);
        Activity::create(['name' => 'Approved']);
        Activity::create(['name' => 'Rejected']);
    }
}
