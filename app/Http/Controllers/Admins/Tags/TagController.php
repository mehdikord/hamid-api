<?php
namespace App\Http\Controllers\Admins\Tags;
use App\Http\Controllers\Controller;
use App\Http\Requests\ImportMethods\ImportMethodCreateRequest;
use App\Http\Requests\ImportMethods\ImportMethodUpdateRequest;
use App\Http\Requests\Tags\TagCreateRequest;
use App\Http\Requests\Tags\TagUpdateRequest;
use App\Interfaces\ImportMethods\importMethodInterface;
use App\Interfaces\Tags\TagInterface;
use App\Models\Import_Method;
use App\Models\Project;
use App\Models\Tag;

class TagController extends Controller
{
    protected TagInterface $repository;

    public function __construct(TagInterface $tag)
    {
        $this->middleware('generate_fetch_query_params')->only('index');
        $this->repository = $tag;
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
    public function store(Project $project,TagCreateRequest $request)
    {
        return $this->repository->store($request,$project);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project,Tag $tag)
    {
        return $this->repository->show($tag,$project);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Project $project,TagUpdateRequest $request,Tag $tag)
    {
        return $this->repository->update($request,$tag,$project);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project,Tag $tag)
    {
        return $this->repository->destroy($tag,$project);
    }

}
