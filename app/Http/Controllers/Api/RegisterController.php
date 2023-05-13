<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'name' => ['required', 'string', 'max:255','unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', 'not_regex:/@live\.bd\.lb$/i'],
            'password' => ['required', 'string', 'min:8'],
        ],[
            'email.not_regex' => 'please enter valid email format',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors,400);
        }

        try {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);
    
        // Send verification email
        $user->sendEmailVerificationNotification();
    
        // Redirect to home or show a success message
        return response()->json('Please check your email to verify your account.');
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage());

        }
    }
}
