<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'View Bank Panel',   'group_name' => 'Bank Panel Module']);
        Permission::create(['name' => 'Create Bank Panel', 'group_name' => 'Bank Panel Module']);
        Permission::create(['name' => 'Update Bank Panel', 'group_name' => 'Bank Panel Module']);
        Permission::create(['name' => 'Delete Bank Panel', 'group_name' => 'Bank Panel Module']);

        Permission::create(['name' => 'View Program',   'group_name' => 'Program Module']);
        Permission::create(['name' => 'Create Program', 'group_name' => 'Program Module']);
        Permission::create(['name' => 'Update Program', 'group_name' => 'Program Module']);
        Permission::create(['name' => 'Delete Program', 'group_name' => 'Program Module']);

        Permission::create(['name' => 'View Recipient',   'group_name' => 'Recipient Module']);
        Permission::create(['name' => 'Create Recipient', 'group_name' => 'Recipient Module']);
        Permission::create(['name' => 'Update Recipient', 'group_name' => 'Recipient Module']);
        Permission::create(['name' => 'Delete Recipient', 'group_name' => 'Recipient Module']);

        Permission::create(['name' => 'View Payment',   'group_name' => 'Payment Module']);
        Permission::create(['name' => 'Create Payment', 'group_name' => 'Payment Module']);
        Permission::create(['name' => 'Update Payment', 'group_name' => 'Payment Module']);
        Permission::create(['name' => 'Delete Payment', 'group_name' => 'Payment Module']);

        Permission::create(['name' => 'View Report',   'group_name' => 'Report Module']);
        Permission::create(['name' => 'Create Report', 'group_name' => 'Report Module']);
        Permission::create(['name' => 'Update Report', 'group_name' => 'Report Module']);
        Permission::create(['name' => 'Delete Report', 'group_name' => 'Report Module']);

        Permission::create(['name' => 'View Audit Trail',   'group_name' => 'Audit Trail Module']);
        Permission::create(['name' => 'Create Audit Trail', 'group_name' => 'Audit Trail Module']);
        Permission::create(['name' => 'Update Audit Trail', 'group_name' => 'Audit Trail Module']);
        Permission::create(['name' => 'Delete Audit Trail', 'group_name' => 'Audit Trail Module']);

        $adminRole = Role::findByName('Admin');
        $adminRole->givePermissionTo('View Bank Panel');
        $adminRole->givePermissionTo('Create Bank Panel');
        $adminRole->givePermissionTo('Update Bank Panel');
        $adminRole->givePermissionTo('Delete Bank Panel');
        

        $adminRole->givePermissionTo('View Program');
        $adminRole->givePermissionTo('Create Program');
        $adminRole->givePermissionTo('Update Program');
        $adminRole->givePermissionTo('Delete Program');

        $adminRole->givePermissionTo('View Recipient');
        $adminRole->givePermissionTo('Create Recipient');
        $adminRole->givePermissionTo('Update Recipient');
        $adminRole->givePermissionTo('Delete Recipient');
        
        $adminRole->givePermissionTo('View Payment');
        $adminRole->givePermissionTo('Create Payment');
        $adminRole->givePermissionTo('Update Payment');
        $adminRole->givePermissionTo('Delete Payment');

        $adminRole->givePermissionTo('View Report');
        $adminRole->givePermissionTo('Create Report');
        $adminRole->givePermissionTo('Update Report');
        $adminRole->givePermissionTo('Delete Report');

        $adminRole->givePermissionTo('View Audit Trail');
        $adminRole->givePermissionTo('Create Audit Trail');
        $adminRole->givePermissionTo('Update Audit Trail');
        $adminRole->givePermissionTo('Delete Audit Trail');

        $userRole = Role::findByName('User');
        $userRole->givePermissionTo('View Bank Panel');
        $userRole->givePermissionTo('View Program');
        $userRole->givePermissionTo('View Recipient');
        $userRole->givePermissionTo('View Payment');
        $userRole->givePermissionTo('View Report');
        $userRole->givePermissionTo('View Audit Trail');



    }
}
