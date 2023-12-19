<?php

namespace App\Http\Controllers;

use App\Models\PermissionGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function AllPermission()
    {
        $permissions = Permission::all();

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
        try {
            $rules = $this->getRules();
            $messages = $this->getMessages();

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation Error',
                    'messages' => $validator->errors(),
                    'code' => 400,
                ]);
            }

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
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while creating the bank panel',
                'code' => 500,
            ], 500);
        }
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
        $permission->group_name = $request->group_name; 
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
        $permissions = Permission::all();
        $groupedPermissions = $permissions->groupBy('group_name');

        return response()->json([
            'roles'       => $roles,
            'permissions'  => $groupedPermissions,
            'code'        => 200
        ]);
    }

    public function getSelectedPermissionRole($id)
    {
        $selectedRole = Role::with('permissions')->find($id);

        if ($selectedRole) {
            return response()->json($selectedRole);
        } else {
            return response()->json(['error' => 'Role not found'], 404);
        }
    }

    public function updateRole($id, Request $request)
    {
        try {
            $rules = $this->getRules();
            $messages = $this->getMessages();

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation Error',
                    'messages' => $validator->errors(),
                    'code' => 400,
                ]);
            }

            $role = Role::where('id', $id)->first();

            if (!$role) {
                return response()->json([
                    'error' => 'Role not found',
                    'code' => 404,
                ], 404);
            }

            $role->name = $request->name;
            $role->description = $request->description;
            $role->save();

            if ($request->has('permissions')) {
                $role->syncPermissions($request->input('permissions.*.name'));
            }
            $role->permissions()->sync($request->input('permissions'));

            return response()->json([
                'message' => 'Role updated successfully',
                'code' => 200
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while updating the role',
                'code' => 500,
            ], 500);
        }
    }

    public function destroy($id, Request $request)
    {
        $role = Role::find($id);
        if($role) {
            $role->delete();

            $user = $request->user();
            $user->log(User::ACTIVITY_DELETED, "App\Models\Role");
            return response()->json([
                'message' => 'Role Deleted Successfully',
                'code'    => 200
            ]);
        } else {
            return response()->json([
                'message' => "Role with id:$id does not exist"
            ]);
        }
    }

    public function getMessages()
    {
        return [
            'name.required' => 'Role name is required',
            'description.required' => 'Role description is required.',
            'permissions.required' => 'At least one permission is required.'
        ];
    }

    public function getRules()
    {
        return [
            'name' => 'required',
            'description' => 'required',
            'permissions' => ['required']
        ];
    }
}
