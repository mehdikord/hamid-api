<?php

namespace App\Http\Controllers\Admins\Customers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customers\Settings\Statuses\CustomerSettingsStatusCreateRequest;
use App\Http\Requests\Customers\Settings\Statuses\CustomerSettingsStatusUpdateRequest;
use App\Interfaces\Customers\CustomerSettingsStatusInterface;
use App\Models\Project_Customer_Status;

class CustomerSettingsStatusController extends Controller
{
    protected CustomerSettingsStatusInterface $repository;

    public function __construct(CustomerSettingsStatusInterface $customerSettingsStatus)
    {
        $this->middleware('generate_fetch_query_params')->only('index');
        $this->repository = $customerSettingsStatus;
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
    public function store(CustomerSettingsStatusCreateRequest $request)
    {
        return $this->repository->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project_Customer_Status $status)
    {
        return $this->repository->show($status);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerSettingsStatusUpdateRequest $request,Project_Customer_Status $status)
    {
        return $this->repository->update($request,$status);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project_Customer_Status $status)
    {
        return $this->repository->destroy($status);
    }

}
