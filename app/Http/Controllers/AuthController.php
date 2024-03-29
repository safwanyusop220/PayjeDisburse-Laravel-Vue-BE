<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\HasApiTokens;

class AuthController extends Controller
{

    public function register(Request $request)
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

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->isCustomAccess = $request->isCustomAccess;
            $user->password = Hash::make($request->password);
            $user->role_id = $request->role;

            $user->save();

            if ($request->has('role')) {
                $user->syncRoles($request->input('role.*.name'));
            }
            $user->roles()->attach($request->input('role'));

            if ($request->has('permissions')) {
                $user->syncPermissions($request->input('permissions.*.name'));
            }
            $user->permissions()->attach($request->input('permissions'));

            $user = $request->user();
            $user->log(User::ACTIVITY_CREATED, "App\Models\User");

            return response()->json([
                'message' => 'User registered Successfully',
                'data' => $user,
                'code' => 200,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while registering the user',
                'code' => 500,
            ], 500);
        }
    }

    public function getUserRolePermission($id)
    {
        $selectedUser = User::with('role', 'role.permissions', 'permissions')->find($id);

        if ($selectedUser) {
            return response()->json($selectedUser);
        } else {
            return response()->json(['error' => 'User not found'], 404);
        }
    }

    public function updateUserRolePermission($id, Request $request)
    {
        $user = User::where('id', $id)->first();
        $user->name            = $request->name; 
        $user->email           = $request->email; 
        $user->role_id         = $request->role; 
        $user->isCustomAccess  = $request->isCustomAccess; 
        $user->save();

        if($request->has('role')){
            $user->syncRoles($request->input('role.*.name'));
        }
        $user->roles()->attach($request->input('role'));

        if($request->has('permissions')){
            $user->syncPermissions($request->input('permissions.*.name'));
        }
        $user->permissions()->attach($request->input('permissions'));

        return response()->json([
            'message' => 'User updated Successfully'
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);
     
        $user = User::where('email', $request->email)->first();
     
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        Log::info($user->role);
     
        return response()->json([
            $userPermissions = $user->permissions,
            ...$user->toArray(),
            'permissions' => $userPermissions,
            'role' => $user->role->name,
            'token' => $user->createToken($request->device_name)->plainTextToken,
        ]);
    }

    public function getProfile()
    {
        $user = auth()->user();

        if ($user instanceof User) {
            return response()->json([
                $userPermissions = $user->permissions,
                ...$user->toArray(),
                'permissions' => $userPermissions,
                'role' => $user->role->name,
            ]);
        }

        return response()->status(401);
    }

    public function user()
    {
        $users = User::with('role')->orderBy('id', 'desc')->get();
        
        return response()->json([
            'users' => $users,
            'code'  => 200,
        ]);
    }

    public function getCurrentUser($id)
    {
        $user = User::with('role')->find($id);

        return response()->json($user);
    }

    public function updateUser($id, Request $request)
    {
        $user = User::find($id);
        $user->name = $request->name; 
        $user->email = $request->email; 
        $user->save();

        return response()->json([
            'message' => 'User Updated Successfully',
            'code'    => 200
        ]);
    }

    public function destroy($id, Request $request)
    {
        $user = User::find($id);
        if($user) {
            $user->delete();

            $user = $request->user();
            $user->log(User::ACTIVITY_DELETED, "App\Models\User");
            return response()->json([
                'message' => 'User Deleted Successfully',
                'code'    => 200
            ]);
        } else {
            return response()->json([
                'message' => "User with id:$id does not exist"
            ]);
        }
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            $user = $request->user();
            $user->tokens()->delete();

            return response()->json(['success' => true]);
        } else {
            return response()->json(['error' => 'User not authenticated']);
        }
    }

    public function getRules()
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role' => 'required|exists:roles,id',
            'permissions' => 'required'
        ];
    }

    public function getMessages()
    {
        return [
            'name.required' => '* Name field is required',
            'email.required' => '* Email field is required',
            'email.email' => '* Email address is invalid',
            'email.unique' => ' Email address is already in use',
            'password.required' => '* Password field is required',
            'password.min' => '* Password must be at least :min characters',
            'role.required' => '* Role field is required',
            'permissions.required' => '* At least one permission is required'
        ];
    }
}
