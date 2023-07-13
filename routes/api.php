<?php

use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SocialLoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\LoginController;
use App\Http\Controllers\Api\BloodRequestController;
use App\Http\Controllers\api\NotificationController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Resources\CenterResource;
use App\Models\DonationCenter;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::get('auth/{provider}', [SocialLoginController::class, 'redirectToProvider'])
    ->name('google.redirect');
Route::post('auth/{provider}/callback', [SocialLoginController::class, 'handleCallback'])
    ->name('google.callback');

Route::get('/centers', function () {
    // if(cache()->has('centers')){
    // return CenterResource::collection(cache()->get('centers'));
    // }
    $centers = DonationCenter::get();
    // cache()->set('centers',$centers);
    return CenterResource::collection($centers);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('admin')->group(function () {
    Route::post('/login', [LoginController::class, 'Adminlogin']);
});
Route::prefix('user')->group(function () {
    Route::post('/login', [LoginController::class, 'Userlogin']);
});

Route::get('/dash', function () {
    return response()->json('im authorize');
})->middleware(['auth:sanctum']);

Route::middleware(['auth:sanctum', 'api.admin'])->group(function () {
    Route::apiResource('/users', UserController::class);
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('user')->name('user.')->group(function () {

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::post('/', [ProfileController::class, 'update'])->name('update');
        Route::get('/syncAvatar',[ProfileController::class,'syncAvatar']);
        Route::apiResource('', ProfileController::class)->only('index');
    });

    Route::get('/notification', [NotificationController::class, 'index']);

    Route::name('appointment.')->group(function () {
        Route::post('/appointment/availalbe-time', [AppointmentController::class, 'getAvailableTimes'])
            ->name('available');
        Route::post('/appointment/download/{id}', [AppointmentController::class, 'downloadPdf'])
            ->name('download');
        Route::apiResource('/appointment', AppointmentController::class);
    });

    Route::name('request.')->group(function () {
        Route::apiResource('/request', BloodRequestController::class);
    });
});

Route::middleware('guest:sanctum')->group(function () {
    Route::post('/user/register', [RegisterController::class, 'register']);
});
