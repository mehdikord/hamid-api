<?php

namespace App\Http\Controllers\Users\Projects;

use App\Http\Controllers\Controller;
use App\Interfaces\ProjectLevels\ProjectLevelInterface;
use App\Interfaces\Projects\ProjectStatusInterface;
use App\Interfaces\Projects\UserProjectInterface;
use App\Models\Project;

class ProjectController extends Controller
{
    protected UserProjectInterface $repository;
    protected ProjectStatusInterface $statusRepository;
    protected ProjectLevelInterface $levelRepository;
    public function __construct(UserProjectInterface $userProject,ProjectStatusInterface $statusRepository,ProjectLevelInterface $levelRepository)
    {
        $this->repository = $userProject;
        $this->statusRepository = $statusRepository;
        $this->levelRepository = $levelRepository;
    }

    public function all()
    {
        return $this->repository->all();
    }

    public function statuses(Project $project)
    {
        return $this->statusRepository->all($project);
    }

    public function levels(Project $project)
    {
        return $this->levelRepository->all($project);
    }

}
