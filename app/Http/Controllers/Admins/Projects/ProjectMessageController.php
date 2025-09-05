<?php

namespace App\Http\Controllers\Admins\Projects;

use App\Http\Controllers\Controller;
use App\Http\Requests\Projects\Messages\ProjectMessageCreateRequest;
use App\Http\Requests\Projects\Messages\ProjectMessageUpdateRequest;
use App\Interfaces\Projects\ProjectMessageInterface;
use App\Models\Project;
use App\Models\Projects\Project_Message;
use Illuminate\Http\Request;

class ProjectMessageController extends Controller
{

    protected ProjectMessageInterface $repository;

    public function __construct(ProjectMessageInterface $message)
    {
        $this->repository = $message;
    }
    public function index(Project $project)
    {
        return $this->repository->index($project);
    }
    public function all(Project $project)
    {
        return $this->repository->all($project);
    }
    public function store(ProjectMessageCreateRequest $request,Project $project)
    {
        return $this->repository->store($project,$request);
    }
    public function show(Project $project,Project_Message $message)
    {
        return $this->repository->show($project,$message);
    }
    public function update(ProjectMessageUpdateRequest $request,Project $project,Project_Message $message)
    {
        return $this->repository->update($project,$request,$message);
    }
    public function destroy(Project $project,Project_Message $message)
    {
        return $this->repository->destroy($project,$message);
    }


}
