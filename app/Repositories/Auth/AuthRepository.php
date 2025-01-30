<?php
namespace App\Repositories\Auth;

use App\Http\Resources\Auth\AdminInfoAuthResource;
use App\Http\Resources\Auth\UserInfoAuthResource;
use App\Interfaces\Auth\AuthInterface;
use App\Models\User;

class AuthRepository implements AuthInterface
{

    public function admin_login($request)
    {
        if (! $token = auth('admins')->attempt(request(['email', 'password']))){
            return helper_response_unauthorized();
        }
        $user = auth('admins')->user();
        return helper_response_main('user login success',[
            'token' => $token,
            'user' => (new AdminInfoAuthResource($user)),
            'token_type' => 'Bearer'
        ]);
    }

    public function user_otp_login($request)
    {
        if (helper_auth_otp_check_time($request->phone)){
            return helper_response_error("پیام ارسال شده قبلی تا دو دقیقه معتبر است!");
        }
        $user = User::where('phone',$request->phone)->first();
        if (!$user){
            $user = User::create([
                'phone' => $request->phone,
                'is_active' => 1,
            ]);
        }
        helper_auth_otp_make_code($request->phone);
        return helper_response_created([]);
    }

    public function user_otp_verify($request)
    {

        if (helper_auth_otp_check_code($request->phone,$request->code)){
            if (!helper_auth_otp_check_time($request->phone)){
                return helper_response_error("مدت زمان ارسال پیام به پایان رسیده است");
            }
            $user = User::where('phone',$request->phone)->first();
            $token =  auth('users')->login($user);
            helper_auth_otp_remove_code($request->phone);
            return helper_response_main('user login success',[
                'token' => $token,
                'user' => (new UserInfoAuthResource($user)),
                'token_type' => 'Bearer'
            ]);

        }
        return helper_response_error("کد ارسال شده نادرست است");


    }

    public function user_login($request)
    {
        if (! $token = auth('users')->attempt(request(['email', 'password']))){
            return helper_response_unauthorized();
        }
        $user = auth('users')->user();
        return helper_response_main('user login success',[
            'token' => $token,
            'user' => (new UserInfoAuthResource($user)),
            'token_type' => 'Bearer'
        ]);

    }

}
