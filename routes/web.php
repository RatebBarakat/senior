<?php

use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Api\SocialLoginController;
use App\Models\Role;
use Illuminate\Http\Request;
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
})->name('home');
Route::get('/success',function (){
   return view('success');
})->name('success');

Route::get('/seed',function (){

});


Route::get('auth/{provider}', [SocialLoginController::class, 'redirectToProvider'])
    ->name('google.redirect');
Route::get('auth/{provider}/callback', [SocialLoginController::class, 'handleCallback'])
    ->name('google.callback');

Route::get('/login',function (){
   \Illuminate\Support\Facades\Auth::guard('admin')->login(\App\Models\Admin::first());
});

Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function (){

    Route::post('/logout', function (Request $request) {
        auth()->guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    })->name('logout');

   Route::view('/','admin.dashboard')->name('index');
   Route::prefix('location')->name('location.')->group(function (){
       Route::view('/create','admin.create-location')->name('create');
       Route::post('/create',[LocationController::class,'store'])->name('store');
   });

    Route::get('/profile/{id}',[ProfileController::class,'viewProfile'])->name('profile.show');
    Route::get('/profile/',[ProfileController::class,'index'])
        ->name('profile');

   Route::middleware('superAdmin')->group(function (){
       Route::view('/roles','admin.super.roles')->name('roles');
       Route::view('/centers','admin.super.centers')->name('centers');
       Route::view('/admins','admin.super.admins')->name('admins');
   });
});
