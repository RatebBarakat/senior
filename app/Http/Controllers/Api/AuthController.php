<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Models\DonationCenter;
use App\Models\Admin;
use App\Models\PasswordReset;
use App\Models\User;
use App\Notifications\SendCodeResetPassword;
use App\Traits\ResponseApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    use ResponseApi;

    protected Admin|User $actor;
    public function sendResetPassword(Request $request)
    {
        $actor = null;

        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                'email',
                function ($attribute, $value, $fail) use ($request) {
                    $userExists = User::where('email', $request->input('email'))->first();
                    $adminExists = Admin::where('email', $request->input('email'))->first();

                    if (is_null($userExists) && is_null($adminExists)) {
                        $fail('We cannot find this email.');
                    }

                    $this->actor = $userExists ?? $adminExists;
                },
            ],
        ]);

        if ($validator->fails()) {
            return $this->validationErrors($validator->errors());
        }


        DB::transaction(function () use ($request, $actor) {
            $token = Str::random(60);
            PasswordReset::updateOrCreate(['email' => $this->actor->email],[
                'code' => $token
            ]);

            $this->actor->notify(new SendCodeResetPassword($this->actor,$token));

            return response()->json(['success' => 'password reset link sent successfully.']);
        });
    }

    public function show(string $token){
        if(PasswordReset::where('code',$token)){
            return view('reset-password',['token' => $token]);
        }
        abort(404);
    }
    public function setPassword(Request $request,string $token){
        if($PasswordReset = PasswordReset::where('code',$token)->exists()){
            $PasswordReset = PasswordReset::where('code',$token)->first();
            User::where('email',$PasswordReset->email)->first()->update([
                'password' => Hash::make($request->input('password'))
            ]);
            $PasswordReset->delete();
        }
        return redirect('http://localhost:8080/login');
    }
}
