<?php

namespace Database\Seeders;

use App\Models\Program;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentDate = now();
        $paymentDateFormatted = Carbon::parse($paymentDate)->format('Y-m-d');
        $end_date_month = Carbon::parse($paymentDate)->addMonths(5)->format('d/m/Y');
        $end_date_year = Carbon::parse($paymentDate)->addYears(2)->format('d/m/Y');


        Program::create(['created_by_id' => 1 , 'name' => 'BKM', 'code' => 'BKM001', 'type_id' => '1', "bank_panel" => "1", 'disburse_amount' => '25000.00', 'payment_date' => $paymentDateFormatted, 'status_id' => '3']);

        Program::create(['created_by_id' => 2 ,'name' => 'STR', 'code' => 'STR001',  'type_id' => '2', "bank_panel" => "2", 'disburse_amount' => '15000.00', 'payment_date' => $paymentDateFormatted, 'status_id' => '1', 'frequency_id' => '1']);

        Program::create(['created_by_id' => 3 ,'name' => 'BRIM', 'code' => 'BRM001',  'type_id' => '3', "bank_panel" => "3", 'disburse_amount' => '550000.00', 'payment_date' => $paymentDateFormatted, 'status_id' => '2']);

        Program::create(['created_by_id' => 1 ,'name' => 'E-KASIH', 'code' => 'EKH001', 'type_id' => '4', "bank_panel" => "4", 'disburse_amount' => '3900.00', 'payment_date' => $paymentDateFormatted, 'status_id' => '4']);

        Program::create(['created_by_id' => 2 ,'name' => 'BKM2', 'code' => 'BKM002', 'type_id' => '1', "bank_panel" => "1", 'disburse_amount' => '1200.00', 'payment_date' => $paymentDateFormatted, 'status_id' => '3']);

        Program::create(['created_by_id' => 1 ,'name' => 'STR2', 'code' => 'STR002',  'type_id' => '2', "bank_panel" => "2", 'disburse_amount' => '7100.00', 'payment_date' => $paymentDateFormatted, 'status_id' => '1', 'frequency_id' => '2', "total_month" => '5', "end_date" => $end_date_month]);

        Program::create(['created_by_id' => 1 ,'name' => 'BRIM2', 'code' => 'BRM002',  'type_id' => '3', "bank_panel" => "3", 'disburse_amount' => '6500.00', 'payment_date' => $paymentDateFormatted, 'status_id' => '2']);

        Program::create(['created_by_id' => 2 ,'name' => 'E-KASIH2', 'code' => 'EKH002', 'type_id' => '4', "bank_panel" => "4",'disburse_amount' => '6900.00', 'payment_date' => $paymentDateFormatted, 'status_id' => '3']);    

        Program::create(['created_by_id' => 3 ,'name' => 'STR3', 'code' => 'STR003',  'type_id' => '2', "bank_panel" => "2", 'disburse_amount' => '1100.00', 'payment_date' => $paymentDateFormatted, 'status_id' => '1', 'frequency_id' => '3', "total_year" => '2', "end_date" => $end_date_year]);
    }
}
