<?php

namespace App\Interfaces\Auth;

interface AuthInterface
{
    public function admin_login($request);

    public function user_login($request);

    public function user_otp_login($request);

    public function user_otp_verify($request);

    public function bot_send($request);

    public function bot_verify($request);



}
