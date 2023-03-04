<?php

namespace App\Http\Middleware;

use App\Models\Social;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\PersonalAccessToken;

class checkAuth
{
    public function handle(Request $request, Closure $next)
    {
//        $token = PersonalAccessToken::findToken($request->bearerToken());
//
//        if (!$token) {
//            return response()->json(['error' => 'unauthorized.'], 401);
//        }
//        if ($token->expires_at && Carbon::parse($token->expires_at)->isPast()) {
//            return response()->json(['error' => 'token_expired'], 401);
//        }
        if (!User::where('email',$request->user()->email)->exists()
            || !Social::where('email',$request->user()->email)->exists()){
            return response()->json(['error' => 'token_expired'], 401);
        }
        return $next($request);
    }
}
