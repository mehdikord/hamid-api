<?php

namespace App\Http\Controllers\Users\Projects;

use App\Http\Controllers\Controller;
use App\Interfaces\ImportMethods\importMethodInterface;
use App\Interfaces\Projects\ProjectLevelInterface;
use App\Interfaces\Projects\ProjectProductInterface;
use App\Interfaces\Projects\ProjectStatusInterface;
use App\Interfaces\Projects\UserProjectInterface;
use App\Interfaces\Tags\TagInterface;
use App\Models\Project;

class ProjectController extends Controller
{
    protected UserProjectInterface $repository;
    protected ProjectStatusInterface $statusRepository;
    protected ProjectLevelInterface $levelRepository;
    protected TagInterface $tagRepository;
    protected importMethodInterface $importMethodRepository;
    protected ProjectProductInterface $productRepository;
    public function __construct(UserProjectInterface $userProject,ProjectStatusInterface $statusRepository,ProjectLevelInterface $levelRepository,TagInterface $tagRepository,importMethodInterface $importMethodRepository,ProjectProductInterface $productRepository)
    {
        $this->middleware('generate_fetch_query_params')->only('reports','invoices');

        $this->repository = $userProject;
        $this->statusRepository = $statusRepository;
        $this->levelRepository = $levelRepository;
        $this->tagRepository = $tagRepository;
        $this->importMethodRepository = $importMethodRepository;
        $this->productRepository = $productRepository;
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

    public function tags(Project $project)
    {
        return $this->tagRepository->all($project);
    }

    public function import_methods(Project $project)
    {
        return $this->importMethodRepository->all($project);
    }
    public function reports()
    {
        return $this->repository->reports();
    }
    public function invoices()
    {
        return $this->repository->invoices();
    }
    public function products(Project $project)
    {
        return $this->productRepository->all($project);
    }

    public function fields(Project $project)
    {
        return $this->repository->fields($project);
    }

}
