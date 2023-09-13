<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Language;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\UpdateAvatarRequest;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SendEmailNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class AuthController extends BaseController
{
    /**
     * @user sign in
     * @param Request $request email && password
     * @return object
     * @author yusuf
    */
    public function signIn(Request $request): object
    {
        $credentials = $request->only('email', 'password');

        #validation message array
        $messages = [
            'email.required'        => __('validation.email_empty'),
            'email.exists'          => __('validation.email_check'),
            'email.email'           => __('validation.empty_invalid'),
            'password.required'     => __('validation.password_empty'),
        ];

        #check validation with message array
        $validator = Validator::make($credentials, [
            'email'             =>'required|email|exists:users',
            'password'          =>'required'
        ], $messages);

        # Send failed response if request is not valid
        if ($validator->fails()){
            return $this->sendError($validator->errors()->all()[0], $validator->errors(),404);
        }
        #check credential and send success or fail message
        if(Auth::attempt($credentials)){
            $authUser = Auth::user();
            if($authUser->status === 'active'){
                $authUser->last_access_at = now();
                $authUser->update();
                $success['token'] =  $authUser->createToken('name')->plainTextToken;
                $success['data'] =  $authUser;
                return $this->sendResponse($success, __('locale.exceptions.sign_in_success_mgs'));
            }else{
                return $this->sendError(__('locale.exceptions.user_inactive_mgs'), ['accountInactive'=>__('locale.exceptions.user_inactive_mgs')],404);
            }
        }else{
            return $this->sendError(__('locale.exceptions.user_password_check_mgs'), ['password'=>__('locale.exceptions.user_password_check_mgs')],404);
        }
    }

    /**
     * @Log out the user of the application.
     * @param  Request  $request token
     * @return object
     * @author yusuf
     */
    public function signOut(Request $request): object
    {
       Auth::logout();
       Session::flush();
       return $this->sendResponse('success', __('locale.exceptions.sign_out_success_mgs'));
    }

    /**
     * @user sign up in the application
     * @param Request $request name, email & pass
     * @return object
     * @author yusuf
     */
    public function signUp(Request $request): object
    {
        #check validation
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'confirm_password' => 'required|same:password',
        ]);

        # Send failed response if request is not valid
        if($validator->fails()){
            return $this->sendError($validator->errors()->all()[0], $validator->errors(),404);
        }

        $input = $request->all(); #get all request data
        $input['password'] = bcrypt($input['password']); # bcrypt password
        $input['locale'] =  app()->getLocale(); #set localization
        $input['timezone'] = config('app.timezone'); # set timezone,
        $user = User::create($input); #create a user

        #check user data
        if($user){
            $user->token = $user->createToken('name')->plainTextToken;
            $verifyUrl =URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
                [
                    'id' => $user->getKey(),
                    'hash' => sha1($user->getEmailForVerification()),
                ]
            );
            //send notification to user
            $details['subject'] = 'Verify Email Address';
            $details['greeting'] = 'Hello';
            $details['body'] = 'We have received a request to activate an user account using your email address. Please click the button below to verify your email address.';
            $details['actionText'] = 'Verify Email Address';
            $details['actionUrl'] =$verifyUrl;
            $details['endText'] = 'Please let me know with our contact details if you have any further question.';
            Notification::send($user, new SendEmailNotification($details));
            return $this->sendResponse($user, __('locale.exceptions.sign_up_success_mgs'));
        }else{
            return $this->sendError(__('locale.exceptions.sign_up_error_mgs'), ['errorMessage' =>__('locale.exceptions.sign_up_error_mgs')],404);
        }
    }

    /**
     * @update user data from setting section
     * @param Request $request userData
     * @return object
     * @author yusuf
     */
    public function updateUserData(Request $request): object
    {
        #check auth user
        $user = Auth::user();
        if(!$user){
            return $this->sendError(__('locale.exceptions.user_unauthorized'),['data'=>__('locale.exceptions.user_unauthorized')] );
        }
        #set and update request data
        $inputs = $request->only('name','gender','dob','about_me','occupation','city','state','postcode');
        $user->update($inputs);
        return $this->sendResponse($user, __('locale.exceptions.user_data_updated_mgs'));
    }

    /**
     * @reset user password
     * @param Request $request password
     * @return object
     * @author yusuf
     */
    public function resetPassword(Request $request): object
    {
        #check validation
        $validator = Validator::make($request->all(), [
            'password' => 'required| min:6',
            'confirm_password' => 'required|same:password',
        ]);
        # Send failed response if request is not valid
        if($validator->fails()){
            return $this->sendError($validator->errors()->all()[0], $validator->errors(),404);
        }
        #check auth user
        $user = Auth::user();
        if(!$user){
            return $this->sendError(__('locale.exceptions.user_unauthorized'),['data'=>__('locale.exceptions.user_unauthorized')] );
        }
        #set and update request password
        $input = $request->only('password');
        $input['password'] = bcrypt($input['password']);
        $user->update($input);
        return $this->sendResponse($user, __('locale.exceptions.password_reset_success_mgs'));
    }
}
