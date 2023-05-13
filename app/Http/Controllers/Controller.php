<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Dompdf\Dompdf;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => ['required',Password::min(8)],
        ]);
        $guard = '';
        $class = '';
        $redirect = '';
        if (str_ends_with($request->input('email'),'@live.bd.lb')) {
            $guard = 'admin';
            $class = "\\App\\Models\\Admin";
            $redirect = '/admin';
        }else{
            $guard = 'web';
            $class = "\\App\\Models\\User";
            $redirect = '/user';
        }

        $credentials = $request->only('email', 'password');
        if (Auth::guard($guard)->attempt($credentials)) {
            $actor = ($class)::where('email',$request->input('email'))->firstOrFail();
            Auth::guard($guard)->login($actor);
            return redirect($redirect);
        } else {
            return redirect()->back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Invalid login credentials.']);
        }
    }
}
