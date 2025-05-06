<?php
namespace App\Http\Controllers\Admins\Fields;
use App\Http\Controllers\Controller;
use App\Http\Requests\Fields\FieldCreateRequest;
use App\Http\Requests\Fields\FieldUpdateRequest;
use App\Interfaces\Fields\FieldInterface;
use App\Models\Fields\Field;

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
    public function store(FieldCreateRequest $request)
    {
        return $this->repository->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Field $field)
    {
        return $this->repository->show($field);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FieldUpdateRequest $request,Field $field)
    {
        return $this->repository->update($request,$field);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Field $field)
    {
        return $this->repository->destroy($field);
    }

}
