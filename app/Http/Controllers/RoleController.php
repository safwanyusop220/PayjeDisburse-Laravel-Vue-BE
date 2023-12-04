<?php

namespace App\Http\Controllers;

use App\Models\PermissionGroup;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function AllPermission()
    {
        $permissions = Permission::with('permissionGroup')->get();

        return response()->json([
            'permissions' => $permissions,
            'message'  => 'permissions',
            'code'     => 200,
        ]);
    }

    public function roles()
    {
        $roles = Role::all();

        return response()->json([
            'roles'    => $roles,
            'message'  => 'roles',
            'code'     => 200,
        ]);
    }

    public function storeRole(Request $request)
    {
        $role = new Role();
        $role->name  = $request->name; 
        $role->description  = $request->description; 
        $role->save();

        if($request->has('permissions')){
            $role->syncPermissions($request->input('permissions.*.name'));
        }
        $role->permissions()->attach($request->input('permissions'));

        return response()->json([
            'message' => 'Role Created Successfully',
            'data' => $role,
            'code' => 200,
        ]);
    }

    public function getPermissionGroups()
    {
        $permissionGroups = PermissionGroup::all();
        
        return response()->json([
            'permissionGroups'  => $permissionGroups,
            'message'           => 'permissionGroups',
            'code'              => 200
        ]);
    }

    public function store(Request $request)
    {
        $permission = new Permission();
        $permission->name          = $request->name; 
        $permission->group_name_id = $request->group_name_id; 
        $permission->save();

        return response()->json([
            'message' => 'Permission Created Successfully',
            'data' => $permission,
            'code' => 200,
        ]);
    }

    public function addRolePermission()
    {
        $roles = Role::orderBy('id', 'desc')->get();
        $permissions = Permission::with('permissionGroup')->get();
        $groupedPermissions = $permissions->groupBy('group_name_id');

        return response()->json([
            'roles'       => $roles,
            'permissions'  => $groupedPermissions,
            'code'        => 200
        ]);
    }

    public function getSelectedPermissionRole($id)
    {
        // $roleId = $request->selectedRole;
        $selectedRole = Role::with('permissions')->find($id);

        if ($selectedRole) {
            // return response()->json([
            //     'data' => $selectedRole,
            //     'code' => 200,
            // ]);
            return response()->json($selectedRole);
        } else {
            return response()->json(['error' => 'Role not found'], 404);
        }
    }
}