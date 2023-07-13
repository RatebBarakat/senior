<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Http\Resources\ProfileResourse;
use App\Models\Social;
use App\Traits\ResponseApi;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Socialite\Facades\Socialite;

class ProfileController extends Controller
{
    use ResponseApi;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = request()->user();

        // if (!$user->profile) {
        //     return $this->responseError('profile not found');
        // }

        return ProfileResourse::make($user->profile);
    }
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'location' => 'required|string|max:100',
            'bio' => 'required|string|max:1000',
            'blood_type' => [
                'required',
                Rule::in('A+', 'B+', 'O+', 'AB+', 'A-', 'B-', 'O-', 'AB-')
            ],
            'avatar' => 'nullable|image|max:1024'
        ]);

        if ($validator->fails()) {
            return $this->validationErrors($validator->errors());
        }

        $user = request()->user();

        $avatar = $user->profile->avatar;

        if ($request->hasFile('avatar')) {
            $name = $request->file('avatar')->getClientOriginalName();
            $extension = $request->file('avatar')->getClientOriginalExtension();
            $avatarName = $name . '_' . Str::random(10) . '.' . $extension;
            $path = $request->file('avatar')->move(
                public_path('storage/avatars/'),
                $avatarName
            );

            $avatar = $avatarName;

            if ($user->profile->avatar) {
                try {
                    unlink(public_path('storage/avatars/' . $user->profile->avatar));
                } catch (Exception $exception) {
                    // Handle the exception here
                }
            }
        }

        $profileData = [
            'avatar' => $avatar,
            'bio' => $request->input('bio'),
            'blood_type' => $request->input('blood_type'),
            'location' => $request->input('location'),
        ];

        $user->profile()->updateOrCreate(
            [
                'user_id' => $user->id,
                'user_type' => get_class($user)
            ],
            $profileData
        );
        

        return $this->successResponse([
            'profile' => ProfileResourse::make($user->profile),
        ], 'Profile updated successfully');
    }

    public function syncAvatar(){
        $user = request()->user();
        if(!$user->social()->exists()){
            response()->json('not linked');
        }else{
            $socialUser = Social::where('user_id',$user->id)->first();
            $providerUser = Socialite::driver('google')->stateless()
            ->userFromToken($socialUser->provider_token);

            $avatarUrl = $providerUser->getAvatar();
            $avatarContents = file_get_contents($avatarUrl);
            $filename = 'avatar' . Str::random(10) . '.jpg';
            $filePath = 'avatars/' . $filename;
            $fullPath = public_path('storage/' . $filePath);
            file_put_contents($fullPath, $avatarContents);
            if ($user->profile->avatar) {
                try {
                    unlink(asset('storage/avatars/'.$user->profile->avatar));
                } catch (Exception $exception) {
                }
            }
    
            $user->profile()->updateOrCreate(
                [
                    'user_id' => $user->id,
                ],
                ['avatar' => $filename]
            );
            return response()->json(['message' => 'avatar suncted successfully']);
        }
    }
}
