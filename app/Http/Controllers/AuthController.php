<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $request->validate([
            'name'                  => ['required'],
            'email'                 => ['required', 'email', 'unique:users'],
            'password'              => ['required',],
        ]);

        $user = new User();
        $user->name            = $request->name; 
        $user->email           = $request->email; 
        $user->isCustomAccess  = $request->isCustomAccess; 
        $user->password        = Hash::make($request->password); 
        $user->role_id         = $request->role; 

        $user->save();

        if($request->has('role')){
            $user->syncRoles($request->input('role.*.name'));
        }
        $user->roles()->attach($request->input('role'));

        if($request->has('permissions')){
            $user->syncPermissions($request->input('permissions.*.name'));
        }
        $user->permissions()->attach($request->input('permissions'));

        $user = $request->user();
        $user->log(User::ACTIVITY_CREATED, "App\Models\User");

        return response()->json([
            'message' => 'User registered Successfully'
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
     
        return response()->json([
            ...$user->toArray(),
            'token' => $user->createToken($request->device_name)->plainTextToken,
        ]);
    }

    public function user()
    {
        $users = User::with('role')->orderBy('id', 'desc')->get();
        
        return response()->json([
            'users' => $users,
            'code'  => 200,
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
}
