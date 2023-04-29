<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminPasswordController extends Controller
{
    public function show(int $id,string $token)
    {
        $admin = Admin::findOrFail($id);
        if ($admin->password_token === $token) {
            return view('admin.setPassword',compact('id','token'));
        }else {
            abort(404);
        }
    }

    public function setPassword(Request $request,int $id,string $token)
    {
        $validatedData = $request->validate([
            'password' => ['required',Password::min(8)->mixedCase()],
            'password-confirm' => ['required','same:password'],
        ]);

        $admin = Admin::findOrFail($id);
        if ($admin->password_token === $token) {//update record
            $admin->update([
                'password' => Hash::make($request->input('password')),
                'password_token' => null
            ]);

            return redirect('/');
        }else {
            abort(404);
        }
    }
}
