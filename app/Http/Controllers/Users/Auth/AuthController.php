<?php

namespace App\Http\Controllers\Users\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UserLoginRequest;
use App\Http\Requests\Auth\UserOTPSendRequest;
use App\Http\Requests\Auth\UserOTPVerifyRequest;
use App\Interfaces\Auth\AuthInterface;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected AuthInterface $repository;

    public function __construct(AuthInterface $auth)
    {
        $this->repository = $auth;
    }

    public function login(UserLoginRequest $request)
    {
        return $this->repository->user_login($request);
    }

    public function otp_send(UserOTPSendRequest $request)
    {
        return $this->repository->user_otp_login($request);
    }

    public function otp_verify(UserOTPVerifyRequest $request)
    {
        return $this->repository->user_otp_verify($request);
    }
}
