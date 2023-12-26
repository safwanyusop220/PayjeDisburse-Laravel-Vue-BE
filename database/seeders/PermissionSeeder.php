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
        Permission::create(['name' => 'view_role',   'group_name' => 'System Role Module']);
        Permission::create(['name' => 'create_role',   'group_name' => 'System Role Module']);
        Permission::create(['name' => 'update_role',   'group_name' => 'System Role Module']);
        Permission::create(['name' => 'delete_role',   'group_name' => 'System Role Module']);

        Permission::create(['name' => 'view_user',   'group_name' => 'System User Module']);
        Permission::create(['name' => 'create_user',   'group_name' => 'System User Module']);
        Permission::create(['name' => 'update_user',   'group_name' => 'System User Module']);
        Permission::create(['name' => 'delete_user',   'group_name' => 'System User Module']);

        Permission::create(['name' => 'view_bank_panel',   'group_name' => 'Bank Panel Module']);
        Permission::create(['name' => 'create_bank_panel', 'group_name' => 'Bank Panel Module']);
        Permission::create(['name' => 'update_bank_panel', 'group_name' => 'Bank Panel Module']);
        Permission::create(['name' => 'delete_bank_panel', 'group_name' => 'Bank Panel Module']);

        Permission::create(['name' => 'view_program',   'group_name' => 'Program Module']);
        Permission::create(['name' => 'create_program', 'group_name' => 'Program Module']);
        Permission::create(['name' => 'update_program', 'group_name' => 'Program Module']);
        Permission::create(['name' => 'delete_program', 'group_name' => 'Program Module']);
        Permission::create(['name' => 'approver_program',   'group_name' => 'Program Module']);
        Permission::create(['name' => 'recommender_program',   'group_name' => 'Program Module']);

        Permission::create(['name' => 'view_recipient',   'group_name' => 'Recipient Module']);
        Permission::create(['name' => 'create_recipient', 'group_name' => 'Recipient Module']);
        Permission::create(['name' => 'update_recipient', 'group_name' => 'Recipient Module']);
        Permission::create(['name' => 'delete_recipient', 'group_name' => 'Recipient Module']);
        Permission::create(['name' => 'approver_recipient',   'group_name' => 'Recipient Module']);
        Permission::create(['name' => 'recommender_recipient',   'group_name' => 'Recipient Module']);

        Permission::create(['name' => 'view_payment',   'group_name' => 'Payment Module']);
        Permission::create(['name' => 'view_report',   'group_name' => 'Report Module']);
        Permission::create(['name' => 'view_audit_trail',   'group_name' => 'Audit Trail Module']);

        $admin_role = Role::findByName('Administrator');
        $permissions = Permission::pluck('name')->toArray();
        $admin_role->syncPermissions($permissions);

        $user_role = Role::findByName('User');
        $user_permissions = [
            'view_bank_panel',
            'view_program',
            'view_recipient',
            'view_payment',
            'view_report',
            'view_audit_trail'
        ];
        $user_role->syncPermissions($user_permissions);

        $recommender_role = Role::findByName('Recommender');
        $recommender_permissions = [
            'view_bank_panel',
            'view_program',
            'recommender_program',
            'view_recipient',
            'recommender_recipient'
        ];
        $recommender_role->syncPermissions($recommender_permissions);

        $approver_role = Role::findByName('Approver');
        $approver_permissions = [
            'view_bank_panel',
            'view_program',
            'approver_program',
            'view_recipient',
            'approver_recipient'
        ];
        $approver_role->syncPermissions($approver_permissions);

        

    }
}
