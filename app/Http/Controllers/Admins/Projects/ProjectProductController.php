<?php

namespace App\Http\Controllers\Admins\Projects;

use App\Http\Controllers\Controller;
use App\Http\Requests\Projects\Products\ProjectProductRequest;
use App\Interfaces\Projects\ProjectProductInterface;
use App\Models\Project;
use App\Models\Projects\Project_Product;

class ProjectProductController extends Controller
{

    protected ProjectProductInterface $repository;

    public function __construct(ProjectProductInterface $product)
    {
        $this->repository = $product;
    }
    public function index(Project $project)
    {
        return $this->repository->index($project);
    }
    public function all(Project $project)
    {
        return $this->repository->all($project);
    }
    public function store(ProjectProductRequest $request,Project $project)
    {
        return $this->repository->store($project,$request);
    }
    public function show(Project $project,Project_Product $product)
    {
        return $this->repository->show($project,$product);
    }
    public function update(ProjectProductRequest $request,Project $project,Project_Product $product)
    {
        return $this->repository->update($project,$request,$product);
    }
    public function destroy(Project $project,Project_Product $product)
    {
        return $this->repository->destroy($project,$product);
    }


}

