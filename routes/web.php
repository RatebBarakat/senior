<?php

use App\Http\Controllers\Admin\AdminPasswordController;
use App\Http\Controllers\Admin\BloodRequestController as AdminBloodRequestController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExcelController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Api\SocialLoginController;
use App\Http\Controllers\Center\ReportController;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::get('/email/verify/{id}/{expiry}/{token}', function ($id, $expiry, $token) {

    if (!request()->hasValidSignature()) {
        abort(404);
    }

    $user = User::find($id);

    if (!$user || !hash_hmac('sha256', $user->getEmailForVerification() . $expiry, env('APP_KEY')) === $token) {
        return 'Invalid verification link.';
    }

    if ($user->email_verified_at) {
        return 'Your email has already been verified.';
    }

    $user->email_verified_at = now();
    $user->save();

    return redirect()->to('http://localhost:8080/verify-email');
})->name('verification.verify')->middleware(['signed']);

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::middleware('guest:admin')->get('/', function () {
    return view('admin.login');
})->name('home');

Route::post('/admin/login', [\App\Http\Controllers\Controller::class, 'login'])
    ->name('admin.login');


Route::get('/success', function () {
    return view('success');
})->name('success');

Route::get('/seed', function () {
    $Permission = Permission::create(['name' => 'request-event', 'slug' => 'request-event']);
    $allowsRoles = array('hospital', 'center-admin');

    foreach ($allowsRoles as $role) {
        Role::where('name', $role)->first()->permissions()->attach($Permission);
    }
});

Route::post('/admin/login', [\App\Http\Controllers\Controller::class, 'login'])
    ->name('admin.login');

Route::get('auth/{provider}', [SocialLoginController::class, 'redirectToProvider'])
    ->name('google.redirect');
Route::get('auth/{provider}/callback', [SocialLoginController::class, 'handleCallback'])
    ->name('google.callback');

Route::get('/login', function () {
    \Illuminate\Support\Facades\Auth::guard('admin')->login(\App\Models\Admin::first());
});

Route::get('/admin/set-password/{id}/{token}', [AdminPasswordController::class, 'show'])->name('admin.setPassword');
Route::post('/admin/set-password/{id}/{token}', [AdminPasswordController::class, 'setPassword'])->name('admin.storePassword');


Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {

    Route::middleware('can:request-event')->name('event.')->prefix('event')->group(function () {
        Route::view('/create', 'admin.event.create-event')->name('create');
    });

    Route::prefix('notification')->name('notification.')->group(function () {
        Route::put('/{id}/markAsRead', [NotificationController::class, 'markAsRead'])->name('markRead');
    });

    Route::prefix('messages')->name('messages.')->group(function () {
        Route::view('/', 'admin.messages')->name('index');
    });

    Route::post('/logout', function (Request $request) {
        auth()->guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    })->name('logout');

    Route::get('/', [DashboardController::class, 'index'])->name('index');

    //    Route::prefix('blood')->name('request.')->group(function ()
    //    {
    //         Route::get('/show/{id}',[BloodRequestController::class,'show'])->name('show');
    //    });

    Route::get('/profile/{id}', [ProfileController::class, 'viewProfile'])->name('profile.show');
    Route::get('/profile/', [ProfileController::class, 'index'])
        ->name('profile');


    Route::middleware('can:super-admin')->group(function () {


        Route::prefix('events')->name('events.')->group(function () {
            Route::view('/', 'admin.super.events')->name('index');
        });

        Route::prefix('location')->name('location.')->group(function () {
            Route::get('/', [LocationController::class, 'index'])->name('index');
            Route::view('/create', 'admin.location.create-location')->name('create');
            Route::post('/create', [LocationController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [LocationController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [LocationController::class, 'update'])->name('update');
            Route::post('/delete/{id}', [LocationController::class, 'delete'])->name('delete');
        });
        Route::view('/roles', 'admin.super.roles')->name('roles');

        Route::prefix('centers')->name('centers.')->group(function () {
            Route::view('/', 'admin.super.centers')->name('index');
            Route::view('/create', 'admin.super.create-center')->name('create');
        });

        Route::view('/admins', 'admin.super.admins')->name('admins');
    });

    Route::middleware(['can:center-admin', 'centerAdmin'])->name('admincenter.')
        ->prefix('center')->group(function () {
            Route::view('/', 'admin.center.index')->name('index');
            Route::view('/report', 'admin.center.reports')->name('reports');
            Route::post('/report/create', [ReportController::class, 'generateReport'])
            ->name('reports.store');
            Route::name('excel.')->group(function () {
                Route::get('/excel', [ExcelController::class, 'index'])->name('index');
                Route::get('/excel/create', [ExcelController::class, 'create'])->name('create');
            });
        });

    Route::middleware(['can:center-employee', 'employee'])->group(function () {
        Route::prefix('blood-request')->name('blood-request.')->group(function () {
            Route::view('/', 'admin.blood-request.index')->name('index');
            Route::get('/{id}', [AdminBloodRequestController::class, 'show'])->name('show');
            Route::post('/unlock', [AdminBloodRequestController::class, 'unLock'])->name('unLock');
        });

        Route::name('appointments.')->group(function () {
            Route::view('/appointments', 'admin.appointment.index')->name('index');
        });
    });
});

include __DIR__ . "/user.php";
