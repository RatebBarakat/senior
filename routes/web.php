<?php

use App\Http\Controllers\Api\SocialLoginController;
use App\Models\Role;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('googlelogin');
});
Route::get('/success',function (){
   return view('success');
})->name('success');

Route::get('/seed-data',function (){
    $roles = Role::with('permissions')->get();
    foreach ($roles as $role){
        echo "<ul>$role->name</ul>";
        foreach ($role->permissions as $permission){
            echo "<li>$permission->name</li>";
        }
    }
});

Route::get('auth/{provider}', [SocialLoginController::class, 'redirectToProvider'])
    ->name('google.redirect');
Route::get('auth/{provider}/callback', [SocialLoginController::class, 'handleCallback'])
    ->name('google.callback');

Route::get('/login',function (){
   \Illuminate\Support\Facades\Auth::guard('admin')->login(\App\Models\Admin::first());
});

Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function (){
   Route::view('/','admin.dashboard')->name('index');
});
