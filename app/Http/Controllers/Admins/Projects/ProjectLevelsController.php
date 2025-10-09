<?php

namespace App\Http\Controllers\Admins\Projects;

use App\Http\Controllers\Controller;
use App\Http\Requests\Projects\levels\ProjectLevelsCreateRequest;
use App\Http\Requests\Projects\levels\ProjectLevelsUpdateRequest;
use App\Interfaces\Projects\ProjectLevelInterface;
use App\Models\Project;
use App\Models\Project_Level;


class ProjectLevelsController extends Controller
{
    protected ProjectLevelInterface $repository;

    public function __construct(ProjectLevelInterface $status)
    {
        $this->middleware('generate_fetch_query_params')->only('index');
        $this->repository = $status;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Project $project)
    {

        return $this->repository->index($project);
    }

    public function all(Project $project)
    {
        return $this->repository->all($project);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(ProjectLevelsCreateRequest $request,Project $project)
    {
        return $this->repository->store($request,$project);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Project $project,ProjectLevelsUpdateRequest $request,Project_Level $level)

    {
        return $this->repository->update($request,$level);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project,Project_Level $level)
    {
        return $this->repository->destroy($level,$project  );
    }

}
