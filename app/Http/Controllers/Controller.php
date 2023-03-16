<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => ['required',Password::min(8)],
        ]);

        $credentials = $request->only('email', 'password');
        if (Auth::guard('admin')->attempt($credentials)) {
            $admin = Admin::where('email',$request->input('email'))->firstOrFail();
            Auth::guard('admin')->login($admin);
            return redirect('/admin');
        } else {
            return redirect()->back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Invalid login credentials.']);
        }
    }
}
