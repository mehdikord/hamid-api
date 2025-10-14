<?php
namespace App\Http\Controllers\Admins\ImportMethods;
use App\Http\Controllers\Controller;
use App\Http\Requests\ImportMethods\ImportMethodCreateRequest;
use App\Http\Requests\ImportMethods\ImportMethodUpdateRequest;
use App\Interfaces\ImportMethods\importMethodInterface;
use App\Models\Import_Method;
use App\Models\Project;

class ImportMethodController extends Controller
{
    protected importMethodInterface $repository;

    public function __construct(importMethodInterface $importMethod)
    {
        $this->middleware('generate_fetch_query_params')->only('index');
        $this->repository = $importMethod;
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
    public function store(ImportMethodCreateRequest $request,Project $project)
    {
        return $this->repository->store($request,$project);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project,Import_Method $import_method)
    {
        return $this->repository->show($import_method,$project);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Project $project,ImportMethodUpdateRequest $request,Import_Method $import_method)
    {
        return $this->repository->update($request,$import_method,$project);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project,Import_Method $import_method)
    {
        return $this->repository->destroy($import_method,$project);
    }

}
