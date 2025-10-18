<?php

namespace App\Http\Controllers\Admins\Projects;

use App\Http\Controllers\Controller;
use App\Http\Requests\Projects\Statuses\ProjectStatusCreateRequest;
use App\Http\Requests\Projects\Statuses\ProjectStatusUpdateRequest;
use App\Interfaces\Projects\ProjectStatusInterface;
use App\Models\Project;
use App\Models\Project_Customer_Status;
use App\Models\Project_Status;
use App\Models\Status_Message;
use Illuminate\Http\Request;

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
    public function store(ProjectStatusCreateRequest $request,Project $project)
    {
        return $this->repository->store($request,$project);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project_Customer_Status $status)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Project $project,ProjectStatusUpdateRequest $request,Project_Customer_Status $status)
    {
        return $this->repository->update($request,$status);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project,Project_Customer_Status $status)
    {
        return $this->repository->destroy($status,$project  );
    }

    public function get_messages(Project $project,Project_Customer_Status $status)
    {
        return $this->repository->get_messages($project,$status);
    }
    public function store_messages(Project $project,Project_Customer_Status $status,Request $request)
    {
        return $this->repository->store_messages($project,$status,$request);
    }
    public function delete_messages(Project $project,Project_Customer_Status $status,Status_Message $message)
    {
        return $this->repository->delete_messages($project,$status,$message);
    }
}
