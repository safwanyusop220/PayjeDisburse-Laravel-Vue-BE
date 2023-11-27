<?php

namespace Database\Seeders;

use App\Models\Receipient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReceipientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Receipient::create([
            'program_id' => '4',
            'status_id' => '1',
            'name' => 'Abdillah Safwan',
            'identification_number' => '000831-13-8300',
            'address' => 'A-20-13A, Landmark Residence, Kajang',
            'phone_number' => '013-8469671',
            'email' => 'safwanyusop220@gmail.com',
            'bank_id' => '2',
            'account_number' => '161024581113'
        ]);
        Receipient::create([
            'program_id' => '1',
            'status_id' => '2',
            'name' => 'Ammar',
            'identification_number' => '000431-04-1232',
            'address' => 'A-20-13A, Landmark Residence, Kajang',
            'phone_number' => '013-8469671',
            'email' => 'ammar220@gmail.com',
            'bank_id' => '3',
            'account_number' => '161024545162'
        ]);
        Receipient::create([
            'program_id' => '1',
            'status_id' => '2',
            'name' => 'Nadia',
            'identification_number' => '000431-04-1232',
            'address' => 'A-20-13A, Landmark Residence, Kajang',
            'phone_number' => '013-8469671',
            'email' => 'ammar220@gmail.com',
            'bank_id' => '3',
            'account_number' => '161024545162'
        ]);
        Receipient::create([
            'program_id' => '1',
            'status_id' => '2',
            'name' => 'Irsyad',
            'identification_number' => '000431-04-1232',
            'address' => 'A-20-13A, Landmark Residence, Kajang',
            'phone_number' => '013-8469671',
            'email' => 'ammar220@gmail.com',
            'bank_id' => '3',
            'account_number' => '161024545162'
        ]);
    }
}
