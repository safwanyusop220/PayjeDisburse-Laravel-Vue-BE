<?php

namespace Database\Seeders;

use App\Models\Frequency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FrequencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Frequency::create(['name' => 'One Time',]);
        Frequency::create(['name' => 'Monthly',]);
        Frequency::create(['name' => 'Yearly',]);
        Frequency::create(['name' => 'Multiple',]);
    }
}
