<?php

namespace App\Http\Controllers\Admins\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\AdminUsersCreateRequest;
use App\Http\Requests\Users\UsersUpdatePasswordRequest;
use App\Http\Requests\Users\UsersUpdateRequest;
use App\Interfaces\Users\UserInterface;
use App\Models\User;

class UserController extends Controller
{
    protected UserInterface $repository;

    public function __construct(UserInterface $user)
    {
        $this->middleware('generate_fetch_query_params')->only('index');
        $this->repository = $user;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->repository->index();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdminUsersCreateRequest $request)
    {
        return $this->repository->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return $this->repository->show($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UsersUpdateRequest $request,User $user)
    {
        return $this->repository->update($request,$user);
    }

    public function change_password(UsersUpdatePasswordRequest $request,User $user)
    {
        return $this->repository->change_password($request,$user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        return $this->repository->destroy($user);
    }

    public function activation(User $user)
    {
        return $this->repository->change_activation($user);

    }
}
