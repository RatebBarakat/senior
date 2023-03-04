<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResourse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(): \Illuminate\Http\JsonResponse
    {
        if (Cache::has('users')) $users = Cache::get('users');
        else $users = UserResourse::collection(User::all());
        if (count($users) > 0){
            return response()->json(['users' => $users]);
        }
        return response()->json('no users');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|min:2|max:255',
            'password' => 'required|string|min:8' ,
            'email' => 'required|email:unique:users,email',
        ]);
        if ($validator->fails()){
            return response()->json(['errors' => $validator->errors()],400);
        }
        $user = User::create([
            'name' => $request->input('name'),
            'password' => Hash::make($request->input('password')),
            'email' => $request->input('email'),
        ]);
        if ($user) return response()->json(['message' => 'user added successfully']);
        else return response()->json(['message' => 'an error accuse']);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): \Illuminate\Http\JsonResponse
    {
        $user = User::where('id', $id)->get();

        if (!$user->isEmpty()) {
            $user = UserResourse::collection($user);
            return response()->json(['user' => $user]);
        } else {
            return response()->json(['message' => 'User not found.'], 404);
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): \Illuminate\Http\JsonResponse
    {
        $user = User::where('id', $id)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }
        $validator = Validator::make($request->all(),[
            'name' => 'required|min:2|max:255',
            'password' => 'nullable|string|min:8' ,
            'email' => 'required|email:unique:users,email,'.$user->id,
        ]);
        if ($validator->fails()){
            return response()->json(['errors' => $validator->errors()],400);
        }
        $password = empty($request->input('password')) ? $user->password :
            Hash::make($request->input('password'));
        $user->update([
            'name' => $request->input('name'),
            'password' => $password,
            'email' => $request->input('email'),
        ]);
        return response()->json(['message' => 'user updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): \Illuminate\Http\JsonResponse
    {
        $user = User::where('id', $id)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }
        $user->delete();
        return response()->json(['message' => 'User deleted successfully .']);
    }
}
