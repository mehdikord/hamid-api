<?php
namespace App\Http\Controllers\Admins\ImportMethods;
use App\Http\Controllers\Controller;
use App\Http\Requests\ImportMethods\ImportMethodCreateRequest;
use App\Http\Requests\ImportMethods\ImportMethodUpdateRequest;
use App\Interfaces\ImportMethods\importMethodInterface;
use App\Models\Import_Method;

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
    public function store(ImportMethodCreateRequest $request)
    {
        return $this->repository->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Import_Method $import_method)
    {
        return $this->repository->show($import_method);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ImportMethodUpdateRequest $request,Import_Method $import_method)
    {
        return $this->repository->update($request,$import_method);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Import_Method $import_method)
    {
        return $this->repository->destroy($import_method);
    }

}
