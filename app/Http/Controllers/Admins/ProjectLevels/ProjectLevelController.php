<?php
namespace App\Http\Controllers\Admins\ProjectLevels;
use App\Http\Controllers\Controller;
use App\Http\Requests\ImportMethods\ImportMethodCreateRequest;
use App\Http\Requests\ImportMethods\ImportMethodUpdateRequest;
use App\Http\Requests\ProjectLevels\ProjectLevelCreateRequest;
use App\Http\Requests\ProjectLevels\ProjectLevelUpdateRequest;
use App\Http\Requests\Tags\TagCreateRequest;
use App\Http\Requests\Tags\TagUpdateRequest;
use App\Interfaces\ImportMethods\importMethodInterface;
use App\Interfaces\ProjectLevels\ProjectLevelInterface;
use App\Interfaces\Tags\TagInterface;
use App\Models\Import_Method;
use App\Models\Project_Level;
use App\Models\Tag;

class ProjectLevelController extends Controller
{
    protected ProjectLevelInterface $repository;

    public function __construct(ProjectLevelInterface $level)
    {
        $this->middleware('generate_fetch_query_params')->only('index');
        $this->repository = $level;
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
    public function store(ProjectLevelCreateRequest $request)
    {
        return $this->repository->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project_Level $level)
    {
        return $this->repository->show($level);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProjectLevelUpdateRequest $request,Project_Level $level)
    {
        return $this->repository->update($request,$level);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project_Level $level)
    {
        return $this->repository->destroy($level);
    }

}
