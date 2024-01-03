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

        foreach ($banks as $bank) {
            RefBank::updateOrInsert(
                ['id' => $bank['id']],
                [
                    'abbreviation' => $bank['abbreviation'],
                    'name' => $bank['name'],
                    'bnm_code' => $bank['bnm_code'],
                    'logo' => $bank['logo'],
                    'account_number_length' => json_encode($bank['account_number_length']),
                ]
            );
        }   
    }
}
