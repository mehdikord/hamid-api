<?php

namespace App\Http\Controllers\Admins\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AdminLoginRequest;
use App\Interfaces\Auth\AuthInterface;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected AuthInterface $repository;

    public function __construct(AuthInterface $auth)
    {
        $this->repository = $auth;
    }

    public function login(AdminLoginRequest $request)
    {
       return $this->repository->admin_login($request);  
    }
}
