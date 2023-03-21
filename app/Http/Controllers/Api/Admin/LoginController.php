<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminResourse;
use App\Http\Resources\UserResourse;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:sanctum');
    }

    public function Adminlogin(Request $request){
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        if ($validator->fails()){
            return response()->json(['errors' => $validator->errors()],400);
        }
        $admin = Admin::where('email',$request->input('email'))->first();
        if (!$admin){
            return response()->json(['errors' => 'credentials not match record'],400);
        }
        if ($admin) {
            $role = $admin->role;
            if ($role) {
                if ($role->name == 'super admin') $permissions = ['*'];
                else $permissions = $role->permissions->pluck('name')->toArray();
            } else {
                $permissions = [];
            }
        } else {
            $permissions = [];
        }
        if (Hash::check($request->input('password'),$admin->password)){
            $token = $admin->createToken($request->input('email').'Token',
                $permissions,now()->addHours(2))->plainTextToken;
            return response()->json(['token' => $token,'admin' => AdminResourse::make($admin),
                'permissions' => $permissions]);
        }
        return response()->json(['errors' => 'wrong password'],400);
    }

    public function Userlogin(Request $request){
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        if ($validator->fails()){
            return response()->json(['errors' => $validator->errors()],400);
        }
        $user = User::where('email',$request->input('email'))->first();
        if (!$user){
            return response()->json(['errors' => 'credentials not match record'],400);
        }
    
        if (Hash::check($request->input('password'),$user->password)){
            $token = $user->createToken($request->input('email').'Token',
                ['user'],now()->addHours(2))
                ->plainTextToken;
            return response()->json(['token' => $token,
                'user' => UserResourse::make($user),
                'role' => 'user'
            ]);
        }
        return response()->json(['errors' => 'wrong password'],400);
    }

}
