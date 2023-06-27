<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ResponseApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use ResponseApi;
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
            return $this->validationErrors($validator->errors());
        }

        try {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);
    
        $user->sendEmailVerificationNotification();
    
        return $this->successResponse([],'Please check your email to verify your account.');
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage());

        }
    }
}
