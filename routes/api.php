<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\VerifyEmailController;
use App\Http\Controllers\API\ForgotPasswordController;
use App\Http\Controllers\API\ResetPasswordController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\NewsController;
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
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

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

#news list
Route::get('news-list',[NewsController::class,'newsList']);

#user authorization routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    #user logout
    Route::post('logout', [UserController::class, 'logout']);

    #user password reset
    Route::post('user/reset-password',[UserController::class,'resetPassword']);

    #user resource route
    Route::apiResource('user', UserController::class)->only([
        'index', 'create', 'store', 'update', 'destroy'
    ]);
});
