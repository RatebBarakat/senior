<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Http\Resources\ProfileResourse;
use App\Traits\ResponseApi;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    use ResponseApi;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = request()->user();
        if (!$user) {
            $this->responseError('user not found');
        }
        if (!$user->profile) {
            return $this->responseError('profile not found');
        }


        return $this->successResponse(['profile' => ProfileResourse::make($user->profile)]
            , 'user profile');

    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'location' => 'required|string|max:100',
            'bio' => 'required|string|max:1000',
            'blood_type' => ['required',
                Rule::in('A','B','O','AB')],
            'avatar' => 'nullable|image|max:1024'
        ]);

        if ($validator->fails()) {
            return $this->validationErrors($validator->errors());
        }

        $user = request()->user();
        $avatar = $user->profile->avatar;

        if ($request->hasFile('avatar')){
            $name = $request->file('avatar')->getClientOriginalName();
            $extension = $request->file('avatar')->getClientOriginalExtension();
            $avatarName = $name . '_' . Str::random(10) . '.' . $extension;
            $path = $request->file('avatar')->move(public_path('storage/avatars/'),
                $avatarName);

            $avatar = $avatarName;

            if ($user->profile->avatar) {
                try {
                    Storage::disk('public')->delete('avatars/' . $user->profile->avatar);
                } catch (Exception $exception) {
                    // Handle the exception here
                }
            }

        }

        if (!$user->profile) {
            $user->profile()->create();
        }

        $user->profile->update([
            'avatar' => $avatar,
            'bio' => $request->input('bio'),
            'blood_type' => $request->input('blood_type'),
            'location' => $request->input('location'),
        ]);

        $user->profile->save();

        // Reload the user object to reflect the updated profile data
        $user->load('profile');

        return $this->successResponse([
            'profile' => ProfileResourse::make($user->profile),
            'message' => 'Profiles updated successfully'
        ]);
    }


}
