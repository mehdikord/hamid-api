<?php
namespace App\Http\Controllers\Admins\Fields;
use App\Http\Controllers\Controller;
use App\Http\Requests\Fields\FieldCreateRequest;
use App\Http\Requests\Fields\FieldUpdateRequest;
use App\Interfaces\Fields\FieldInterface;
use App\Models\Fields\Field;
use App\Models\Project;

class FieldController extends Controller
{
    protected FieldInterface $repository;

    public function __construct(FieldInterface $field)
    {
        $this->middleware('generate_fetch_query_params')->only('index');
        $this->repository = $field;
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
    public function store(Project $project,FieldCreateRequest $request)
    {
        return $this->repository->store($request,$project);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project,Field $field)
    {
        return $this->repository->show($field,$project);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Project $project,FieldUpdateRequest $request,Field $field)
    {
        return $this->repository->update($request,$field,$project);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project,Field $field)
    {
        return $this->repository->destroy($field,$project);
    }

}
