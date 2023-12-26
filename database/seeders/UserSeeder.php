<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Admin
        $adminPermissions = Permission::pluck('name')->toArray();
        $admin = User::create([
            'name' => 'Abdillah Safwan',
            'email' => 'safwan@edaran.com',
            'password' => bcrypt('password1234'),
            'role_id' => 1
        ]);
        $admin->givePermissionTo($adminPermissions);

        //Recommender
        $recommender = User::create([
            'name' => 'Nadiatul Najihah',
            'email' => 'nadia@edaran.com',
            'password' => bcrypt('password1234'),
            'role_id' => 2
        ]);
        $recommenderPermissions = [
            'view_bank_panel',
            'view_program',
            'recommender_program',
            'view_recipient',
            'recommender_recipient',
            'view_payment',
            'view_report'
        ];
        $recommender->givePermissionTo($recommenderPermissions);

        //Approver
        $approver = User::create([
            'name' => 'Marwan Mazli',
            'email' => 'marwan@edaran.com',
            'password' => bcrypt('password1234'),
            'role_id' => 3
        ]);
        $approverPermissions = [
            'view_bank_panel',
            'view_program',
            'approver_program',
            'view_recipient',
            'approver_recipient',
            'view_payment',
            'view_report'
        ];
        $approver->givePermissionTo($approverPermissions);

        //Approver
        $user = User::create([
            'name' => 'Irsyad Ifwat',
            'email' => 'irsyad@edaran.com',
            'password' => bcrypt('password1234'),
            'role_id' => 4
        ]);
        $userPermissions = [
            'view_bank_panel',
            'create_bank_panel',
            'update_bank_panel',
            'view_program',
            'create_program',
            'update_program',
            'view_recipient',
            'create_recipient',
            'update_recipient',
            'view_payment',
            'view_report'
        ];
        $user->givePermissionTo($userPermissions);



        // User::create(['name' => 'Marwan Mazli', 'email' => 'marwan@edaran.com', 'password' => 'password1234', 'role_id' => 1]);
        // User::create(['name' => 'Abdillah Safwan', 'email' => 'safwan@edaran.com', 'password' => 'password1234', 'role_id' => 1]);
        // User::create(['name' => 'Nadiatul Najihah', 'email' => 'nadia@edaran.com', 'password' => 'password1234', 'role_id' => 1]);
    }
}
