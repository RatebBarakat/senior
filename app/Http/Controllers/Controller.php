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
use Illuminate\Support\Facades\RateLimiter;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function login(Request $request)
    {
        $key = 'login_attempts_'.$request->ip();
        $maxAttempts = 5;
        $decaySeconds = 180;//3 minutes

        if (RateLimiter::tooManyAttempts($key, $maxAttempts, $decaySeconds)) {
            $seconds = RateLimiter::availableIn($key);
            return redirect()->back()->withErrors([
                'login' => 'Too many attempts. Please try again in '.$seconds.' seconds.'
            ]);
        }

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
            RateLimiter::hit($key, $decaySeconds);
            return redirect()->back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Invalid login credentials.']);
        }
    }
}
