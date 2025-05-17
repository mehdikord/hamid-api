<?php

namespace App\Http\Controllers\Users\Profile;

use App\Http\Controllers\Controller;
use App\Interfaces\Profile\ProfileInterface;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    protected ProfileInterface $repository;

    public function __construct(ProfileInterface $profile)
    {
        $this->repository = $profile;
    }

    public function index()
    {
        return $this->repository->index();
    }

    public function projects()
    {
        return $this->repository->projects();
    }
}
