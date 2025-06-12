<?php

namespace App\Http\Controllers\Users\Products;

use App\Http\Controllers\Controller;
use App\Interfaces\Projects\UserProjectInterface;

class ProductController extends Controller
{
    protected UserProjectInterface $repository;
    public function __construct(UserProjectInterface $userProject)
    {
        $this->repository = $userProject;
    }

    public function all()
    {
        return $this->repository->all();
    }
}
