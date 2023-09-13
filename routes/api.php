<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\VerifyEmailController;
use App\Http\Controllers\API\ForgotPasswordController;
use App\Http\Controllers\API\ResetPasswordController;
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

#user authentication
Route::post('signIn', [AuthController::class, 'signIn']);
Route::post('signOut', [AuthController::class, 'signOut']);
Route::post('signUp', [AuthController::class, 'signUp']);

#forgot and reset password
Route::post('password/email', [ForgotPasswordController::class,'sendResetLinkEmail']);
Route::post('password/confirm', [ResetPasswordController::class,'reset']);

#Verify email
Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

#Resend link to verify email
Route::post('/email/verify/resend', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth:api', 'throttle:6,1'])->name('verification.send');

#user authorization routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    #upate user data
    Route::post('user/update',[AuthController::class, 'updateUserData']);
    #reset password
    Route::post('reset/password',[AuthController::class,'resetPassword']);
});
