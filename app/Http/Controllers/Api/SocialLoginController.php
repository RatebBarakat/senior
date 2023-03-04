<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResourse;
use App\Models\Profile;
use App\Models\Social;
use App\Models\User;
use App\Traits\ResponseApi;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    use ResponseApi;
    public function redirectToProvider(string $provider)
    {
        if(!$this->checkProvider($provider)){//check provider name if in array
            return $this->responseError('wrong provider',400);
        }
            return Socialite::driver($provider)->stateless()->redirect();
    }

    public function handleCallback(string $provider)
    {
        if(!$this->checkProvider($provider)){
            $this->responseError('an error occurs');
        }
        try {
            $providerUser = Socialite::driver($provider)->stateless()->user();
            $user = Social::where('provider', $provider)
                ->where('email', $providerUser->email)
                ->first();
            if (!$user){
                $user = Social::create([
                    'name' => $providerUser->name,
                    'email' => $providerUser->email,
                    'provider' => $provider,
                    'provider_id' => $providerUser->getId(),
                    'provider_token' => $providerUser->token
                ]);
            }
            else{
                $user->update([
                    'provider' => $provider,
                    'provider_id' => $providerUser->getId(),
                    'provider_token' => $providerUser->token
                ]);
            }
            $this->updateUserAvatar($providerUser, $user);

            $token = $user->createToken('my_token')->plainTextToken;

            $user->load('profile');

            \auth()->guard('social')->login($user);

            return redirect('/');

//            return $this->successResponse([
//                'token' => $token,
//                'user' => UserResourse::make($user),
//                'profile' => $user->profile
//            ],'success login');
        } catch (Exception $e) {
            dd($e->getMessage());
        }

}

    protected function checkProvider(string $provider):bool
    {
        return in_array($provider,['google','facebook']);
    }

    /**
     * @param $providerUser
     * @param $user
     * @return void
     */
    protected function updateUserAvatar($providerUser, $user): void
    {

        // Save the user's avatar
        $avatarUrl = $providerUser->getAvatar();
        $avatarContents = file_get_contents($avatarUrl);
        $filename = 'avatar' . Str::random(10) . '.jpg';
        Storage::disk('public')->put('avatars/' . $filename, $avatarContents);

        // Delete the user's old avatar if it exists
        if ($user->profile->avatar) {
            try {
                Storage::disk('public')->delete('avatars/' . $user->profile->avatar);
            } catch (Exception $exception) {
                // Handle the exception here
            }
        }

        // Update the user's profile with the new avatar
        $user->profile()->updateOrCreate([],[
            'avatar' => $filename,
        ]);

    }
}
