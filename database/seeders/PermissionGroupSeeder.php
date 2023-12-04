<?php

namespace Database\Seeders;

use App\Models\PermissionGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PermissionGroup::create(['name' => 'Bank Panel',]);
        PermissionGroup::create(['name' => 'Program',]);
        PermissionGroup::create(['name' => 'Recipient',]);
        PermissionGroup::create(['name' => 'Payment',]);
        PermissionGroup::create(['name' => 'Report',]);
        PermissionGroup::create(['name' => 'Audit Trail',]);
    }
}
