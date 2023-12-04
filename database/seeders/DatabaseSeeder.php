<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(ActivitySeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(PermissionGroupSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(RefBankSeeder::class);
        $this->call(BankPanelSeeder::class);
        $this->call(ProgramTypeSeeder::class);
        $this->call(FrequencySeeder::class);
        $this->call(StatusSeeder::class);
        $this->call(ProgramSeeder::class);
        $this->call(ReceipientSeeder::class);
    }
}
