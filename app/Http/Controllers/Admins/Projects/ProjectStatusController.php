<?php

namespace App\Http\Controllers\Admins\Projects;

use App\Http\Controllers\Controller;
use App\Http\Requests\Projects\Statuses\ProjectStatusCreateRequest;
use App\Http\Requests\Projects\Statuses\ProjectStatusUpdateRequest;
use App\Interfaces\Projects\ProjectStatusInterface;
use App\Models\Project_Status;

class ProjectStatusController extends Controller
{
    protected ProjectStatusInterface $repository;

    public function __construct(ProjectStatusInterface $status)
    {
        $this->middleware('generate_fetch_query_params')->only('index');
        $this->repository = $status;
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
    public function store(ProjectStatusCreateRequest $request)
    {
        return $this->repository->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project_Status $status)
    {
        return $this->repository->show($status);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProjectStatusUpdateRequest $request,Project_Status $status)
    {
        return $this->repository->update($request,$status);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project_Status $status)
    {
        return $this->repository->destroy($status);
    }

}
