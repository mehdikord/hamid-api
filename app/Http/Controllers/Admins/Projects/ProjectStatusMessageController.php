<?php

namespace App\Http\Controllers\Admins\Projects;

use App\Http\Controllers\Controller;
use App\Http\Requests\StatusMessages\StatusMessageCreateRequest;
use App\Http\Requests\StatusMessages\StatusMessageUpdateRequest;
use App\Interfaces\StatusMessages\StatusMessageInterface;
use App\Models\Project;
use App\Models\Status_Message;
use Illuminate\Http\Request;

class ProjectStatusMessageController extends Controller
{
    protected StatusMessageInterface $repository;

    public function __construct(StatusMessageInterface $statusMessage)
    {
        $this->repository = $statusMessage;
        $this->middleware('generate_fetch_query_params')->only('index','all');
    }
    public function index(Project $project)
    {
        return $this->repository->index($project);
    }
    public function all(Project $project)
    {
        return $this->repository->all($project);
    }
    public function store(StatusMessageCreateRequest $request,Project $project)
    {
        return $this->repository->store($request,$project);
    }
    public function show(Project $project,Status_Message $status_message)
    {
        return $this->repository->show($status_message,$project);
    }
    public function update(StatusMessageUpdateRequest $request,Project $project,Status_Message $status_message)
    {
        return $this->repository->update($request,$status_message,$project);
    }
    public function destroy(Project $project,Status_Message $status_message)
    {
        return $this->repository->destroy($status_message,$project);
    }
}
