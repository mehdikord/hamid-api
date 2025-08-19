<?php

namespace App\Http\Controllers\Admins\Projects;

use App\Http\Controllers\Controller;
use App\Interfaces\Projects\ProjectInterface;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectsExportController extends Controller
{

    protected ProjectInterface $repository;
    public function __construct(ProjectInterface $project)
    {
        $this->repository = $project;
    }

    public function customers(Project $project)
    {
        return $this->repository->export_customers($project);
    }
}
