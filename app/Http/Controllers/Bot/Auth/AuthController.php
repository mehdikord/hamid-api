<?php

namespace App\Http\Controllers\Bot\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Bot\Auth\SendRequest;
use App\Interfaces\Auth\AuthInterface;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    protected AuthInterface $repository;

    public function __construct(AuthInterface $auth)
    {
        $this->repository = $auth;
    }

    public function send(SendRequest $request)
    {
        return $this->repository->bot_send($request);
    }

    public function verify(Request $request)
    {
        return $this->repository->bot_verify($request);
    }

}
