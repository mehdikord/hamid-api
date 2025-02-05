<?php

namespace App\Http\Controllers\Admins\Projects;

use App\Http\Controllers\Controller;
use App\Http\Requests\Projects\Categories\ProjectCategoryCreateRequest;
use App\Http\Requests\Projects\Categories\ProjectCategoryUpdateRequest;
use App\Http\Requests\Projects\Customers\ProjectCustomersAssignedRequest;
use App\Http\Requests\Projects\Projects\ProjectCreateRequest;
use App\Http\Requests\Projects\Projects\ProjectCustomersCreateRequest;
use App\Http\Requests\Projects\Projects\ProjectUpdateRequest;
use App\Interfaces\Projects\ProjectCategoryInterface;
use App\Interfaces\Projects\ProjectInterface;
use App\Models\Project;
use App\Models\Project_Category;

class ProjectController extends Controller
{
    protected ProjectInterface $repository;

    public function __construct(ProjectInterface $project)
    {
        $this->middleware('generate_fetch_query_params')->only('index','get_customers');
        $this->repository = $project;
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
    public function store(ProjectCreateRequest $request)
    {
        return $this->repository->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        return $this->repository->show($project);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProjectUpdateRequest $request,Project $project)
    {
        return $this->repository->update($request,$project);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        return $this->repository->destroy($project);
    }

    public function add_customers(ProjectCustomersCreateRequest $request,Project $project)
    {
        return $this->repository->add_customers($request,$project);
    }

    public function get_customers(Project $project)
    {
        return $this->repository->get_customers($project);
    }

    public function assigned_customers(Project $project,ProjectCustomersAssignedRequest $request)
    {
        return $this->repository->assigned_customers($project,$request);
    }

}
