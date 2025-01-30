<?php

namespace App\Http\Controllers\Admins\Projects;

use App\Http\Controllers\Controller;
use App\Http\Requests\Projects\Categories\ProjectCategoryCreateRequest;
use App\Http\Requests\Projects\Categories\ProjectCategoryUpdateRequest;
use App\Interfaces\Projects\ProjectCategoryInterface;
use App\Models\Project_Category;

class ProjectCategoryController extends Controller
{
    protected ProjectCategoryInterface $repository;

    public function __construct(ProjectCategoryInterface $category)
    {
        $this->middleware('generate_fetch_query_params')->only('index');
        $this->repository = $category;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->repository->index();
    }

    public function all()
    {
        return $this->repository->all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProjectCategoryCreateRequest $request)
    {
        return $this->repository->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project_Category $category)
    {
        return $this->repository->show($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProjectCategoryUpdateRequest $request,Project_Category $category)
    {
        return $this->repository->update($request,$category);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project_Category $category)
    {
        return $this->repository->destroy($category);
    }

}
