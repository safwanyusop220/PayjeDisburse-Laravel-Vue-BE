<?php

namespace Database\Seeders;

use App\Models\RefBank;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RefBankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banks = json_decode(file_get_contents(config_path('banks.json')), true);

        RefBank::upsert($banks, ['id'], ['name', 'bnm_code', 'logo']);    
    }
}
