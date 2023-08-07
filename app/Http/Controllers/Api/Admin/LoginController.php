<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
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

    public function Userlogin(Request $request)
    {
        $key = 'login_attempts_api_' . $request->ip();
        $maxAttempts = 5;
        $decaySeconds = 180; // 3 minutes

        if (RateLimiter::tooManyAttempts($key, $maxAttempts, $decaySeconds)) {
            return response()->json(['errors' => [
                'general' => ['Too many attempts']
            ]], 412);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 412);
        }

        $user = User::where('email', $request->input('email'))->first();
        $admin = Admin::where('email', $request->input('email'))->first();
        
        if (!$user && !$admin) {
            RateLimiter::hit($key, $decaySeconds);
            return response()->json(['errors' => ['general' => ['Credentials not found']]], 412);
        }

        $actor = null;
        $permissions = [];

        if ($user) {
            $actor = $user;
            if (!$user->hasVerifiedEmail()) {
                RateLimiter::hit($key, $decaySeconds);
                return response()->json(['errors' => ['email' => ['Your email is not verified.']]], 412);
            }
        } elseif ($admin) {
            $actor = $admin;
        }

        if (!$actor || !Hash::check($request->input('password'), $actor->password)) {
            RateLimiter::hit($key, $decaySeconds);
            return response()->json(['errors' => ['general' => ['Credentials not match record']]], 412);
        }

        $token = $actor->createToken(
            $request->input('email') . get_class($actor) . 'Token',
            ['*'],
            now()->addHours(2)
        )->plainTextToken;

        return response()->json([
            'token' => $token,
            'actor' => $actor,
        ]);
    }

    public function spaLogin(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 412);
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();
            return response()->json($user)
                            ->message('Login was successful!');

        }else{
            return response()->json(['Wrong username or password!']);
        }
    }
}
