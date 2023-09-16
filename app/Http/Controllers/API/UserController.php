<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class UserController extends BaseController
{

    /**
     * @class initialization
     */
    public function __construct(){
        $this->middleware('auth:sanctum');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * @update user data from setting section
     * @param Request $request userData
     * @return object
     * @author yusuf
     */
    public function update(Request $request): object
    {
        try{
            #check auth user
            $user = Auth::user();
            if(!$user){
                return $this->sendError(__('locale.exceptions.user_unauthorized'),['data'=>__('locale.exceptions.user_unauthorized')] );
            }
            #set and update request data
            $inputs = $request->only('name','gender','dob','about_me','occupation','city','state','postcode','fav_source','fav_author','fav_category');
            $user->update($inputs);
            return $this->sendResponse($user, __('locale.exceptions.user_data_updated_mgs'));
        }catch (\Throwable $th) {
            return $this->sendError($th->getMessage(),['reason'=>$th->getMessage()],500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    /**
     * @reset user password
     * @param Request $request password
     * @return object
     * @author yusuf
     */
    public function resetPassword(Request $request): object
    {
        try {
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
        }catch (\Throwable $th) {
            return $this->sendError($th->getMessage(),['reason'=>$th->getMessage()],500);
        }
    }

    /**
     * @Log out the user of the application.
     * @param  Request  $request token
     * @return object
     * @author yusuf
     */
    public function logout(Request $request): object
    {
        try{
            auth()->user()->tokens->each(function ($token, $key) {
                $token->delete();
            });
            return $this->sendResponse('success', __('locale.exceptions.sign_out_success_mgs'));
        }catch (\Throwable $th) {
            return $this->sendError($th->getMessage(),['reason'=>$th->getMessage()],500);
        }
    }

}
