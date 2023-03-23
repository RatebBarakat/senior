<?php

use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Api\SocialLoginController;
use App\Mail\SendPdfEmail as MailSendPdfEmail;
use App\Models\Role;
use App\Models\User;
use App\Notifications\SendPdfEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

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


Route::middleware('guest:admin')->get('/', function () {
    return view('admin.login');
})->name('home');

Route::post('/admin/login',[\App\Http\Controllers\Controller::class,'login'])
    ->name('admin.login');


Route::get('/success',function (){
   return view('success');
})->name('success');

Route::get('/seed',function (){

});

Route::post('/admin/login',[\App\Http\Controllers\Controller::class,'login'])
    ->name('admin.login');

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

       Route::prefix('centers')->name('centers.')->group(function (){
           Route::view('/','admin.super.centers')->name('index');
           Route::view('/create','admin.super.create-center')->name('create');
       });

       Route::view('/admins','admin.super.admins')->name('admins');
   });

   Route::middleware('centerAdmin')->name('admincenter.')
       ->prefix('center')->group(function (){
      Route::view('/','admin.center.index')->name('index');
   });

   Route::middleware(['employee'])->name('appointments.')->group(function () {
      Route::view('/appointments','admin.appointment.index')->name('index');
   });
});

Route::get('/email/verify/{id}/{expiry}/{token}', function ($id, $expiry, $token) {
    $user = User::find($id);

    if (!$user || !hash_hmac('sha256', $user->getEmailForVerification().$expiry, env('APP_KEY')) === $token) {
        // if the user is not found or the email hash doesn't match, return an error message
        return 'Invalid verification link.';
    }

    if ($user->email_verified_at) {
        // if the user's email is already verified, return a message saying so
        return 'Your email has already been verified.';
    }

    $user->email_verified_at = now();
    $user->save();

    return 'Your email has been verified.';
})->name('verification.verify')->middleware(['signed']);




 
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
 
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');
// Route::get('/pdf',function() {
//     $data = [
//         'title' => 'مرحبا بالعالم',
//         'content' => 'هذا هو محتوى الصفحة باللغة العربية'
//     ];

//     // Create a new TCPDF object
//     $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);

//     // Set the document properties
//     $pdf->SetCreator('Your Name');
//     $pdf->SetAuthor('Your Name');
//     $pdf->SetTitle('My PDF');
//     $pdf->SetSubject('Example');

//     // Set the default font and font size
//     $pdf->SetFont('dejavusans', '', 12);

//     // Add a new page to the PDF
//     $pdf->AddPage();

//     // Render the view as HTML
//     $html = View::make('admin.appointment-pdf', compact('data'))->render();

//     // Write the HTML to the PDF
//     $pdf->writeHTML($html, true, false, true, false, '');

//     // Output the PDF
//     $pdf->Output(storage_path('app/public/pdf/my-pdf.pdf'), 'F');

//     // Send email with PDF attachment
//     $email = 'rfb005@live.aul.edu.lb';
//     $subject = 'Test PDF';
//     $body = 'Please find the attached PDF file.';
//     $attachment = storage_path('app/public/my-pdf.pdf');

//     $pdfContent = $pdf->Output('example.pdf', 'S');
//     Mail::send([], [], function ($message) use ($pdfContent) {
//         $message->to('rfb005@live.aul.edu.lb')
//                 ->subject('PDF Example')
//                 ->attachData($pdfContent, 'example.pdf', [
//                     'mime' => 'application/pdf',
//                 ]);
//     });

//     // Delete the PDF file
//     unlink($attachment);
// });

