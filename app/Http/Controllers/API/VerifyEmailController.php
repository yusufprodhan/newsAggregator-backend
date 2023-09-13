<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use App\Models\User;

class VerifyEmailController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $user = User::find($request->route('id'));
        $flag = 0;
        if ($user->hasVerifiedEmail()) {
            //return redirect(env('APP_URL') . '/email/verify/already-success');
            return  redirect(route('email.verify.already.success'));
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        //return redirect(env('APP_URL') . '/email/verify/success');
        return  redirect(route('email.verify.success'));
    }

    public function emailVerificationSuccessPage(Request $request)
    {
        $flag = 0;
        return view('auth.success.email_verification_success',compact('flag'));
    }
}
