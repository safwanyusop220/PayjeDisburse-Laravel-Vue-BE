<?php

namespace Database\Seeders;

use App\Models\BankPanel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BankPanelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BankPanel::create(['holder_name' => 'Abdillah Safwan', 'bank_id' => 5, 'account_number' => '161024581121']);
        BankPanel::create(['holder_name' => 'Nadiatul Najihah', 'bank_id' => 22, 'account_number' => '161023847116']);
        BankPanel::create(['holder_name' => 'Nadia', 'bank_id' => 3, 'account_number' => '1610343447116']);
        BankPanel::create(['holder_name' => 'Safwan', 'bank_id' => 25, 'account_number' => '1610347116']);
    }
}
