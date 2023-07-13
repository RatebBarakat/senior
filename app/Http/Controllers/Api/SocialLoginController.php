<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResourse;
use App\Models\Profile;
use App\Models\Social;
use App\Models\User;
use App\Traits\ResponseApi;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialLoginController extends Controller
{
    use ResponseApi;
    public function redirectToProvider(string $provider)
    {
        if (!$this->checkProvider($provider)) {
            return $this->responseError('wrong provider', 400);
        }

        $redirectUrl = Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();

        return response()->json(['url' => $redirectUrl]);
    }


    public function handleCallback(string $provider)
    {
        if (!$this->checkProvider($provider)) {
            $this->responseError('An error occurs');
        }
        try {
            $providerUser = Socialite::driver($provider)->stateless()->user();
            $social = Social::where('provider', $provider)
                ->where('provider_id', $providerUser->getId())
                ->first();
    
            if (!$social) {
                $user = User::updateOrCreate([
                    'email' => $providerUser->getEmail(), 
                ],
                [
                    'name' => $providerUser->getName(),
                    'password' => Hash::make(Str::random(16)),
                    'email_verified_at' => now()
                ]);
    
                $social = Social::create([
                    'user_id' => $user->id,
                    'provider' => $provider,
                    'provider_id' => $providerUser->getId(),
                    'provider_token' => $providerUser->token,
                ]);
            } else {
                $user = $social->user;
            }
    
            $this->updateUserAvatar($providerUser, $user);
    
            $token = $user->createToken('my_token')->plainTextToken;
            return response()->json(['token' => $token, 'user' => $user, 'type' => 'social']);
        } catch (Exception $e) {
            return response()->json($e->getMessage());
        }
    }
    

    protected function checkProvider(string $provider): bool
    {
        return in_array($provider, ['google', 'facebook']);
    }

    /**
     * @param $providerUser
     * @param $user
     * @return void
     */
    protected function updateUserAvatar($providerUser, $user): void
    {
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
                'user_type' => "App\\Models\\Social"
            ],
            ['avatar' => $filename]
        );
    }
}
