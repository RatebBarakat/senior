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
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use App\Http\Middleware\EnsureEmailIsVerified;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:sanctum');
    }

    // public function Adminlogin(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'email' => 'required|email',
    //         'password' => 'required|string',
    //     ]);
    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 400);
    //     }
    //     $admin = Admin::where('email', $request->input('email'))->first();
    //     if (!$admin) {
    //         return response()->json(['errors' => 'credentials not match record'], 400);
    //     }
    //     if ($admin) {
    //         $role = $admin->role;
    //         if ($role) {
    //             if ($role->name == 'super admin') $permissions = ['*'];
    //             else $permissions = $role->permissions->pluck('name')->toArray();
    //         } else {
    //             $permissions = [];
    //         }
    //     } else {
    //         $permissions = [];
    //     }
    //     if (Hash::check($request->input('password'), $admin->password)) {
    //         $token = $admin->createToken(
    //             $request->input('email') . 'Token',
    //             $permissions,
    //             now()->addHours(2)
    //         )->plainTextToken;
    //         return response()->json([
    //             'token' => $token, 'admin' => AdminResourse::make($admin),
    //             'permissions' => $permissions
    //         ]);
    //     }
    //     return response()->json(['errors' => 'wrong password'], 400);
    // }

    public function Userlogin(Request $request)
    {
        $key = 'login_attempts_api_' . $request->ip();
        $maxAttempts = 5;
        $decaySeconds = 180; //3 min

        if (RateLimiter::tooManyAttempts($key, $maxAttempts, $decaySeconds)) {
            // $seconds = RateLimiter::availableIn($key);
            return response()->json(['errors' => [
                'general' => ['Too many attempts']
            ]], 400);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        $user = User::where('email', $request->input('email'))->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            RateLimiter::hit($key, $decaySeconds);
            return response()->json(['errors' => ['general' => ['credentials not match record']]], 400);
        }

        if (!$user->hasVerifiedEmail()) {
            RateLimiter::hit($key, $decaySeconds);
            return response()->json(['errors' => ['email' => ['Your email is not verified.']]], 400);
        }

        $token = $user->createToken(
            $request->input('email') . 'Token',
            ['*'],
            now()->addHours(2)
        )->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => UserResourse::make($user),
        ]);
    }
}
