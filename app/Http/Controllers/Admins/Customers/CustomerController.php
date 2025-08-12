<?php
namespace App\Http\Controllers\Admins\Customers;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customers\CustomerCreateRequest;
use App\Http\Requests\Customers\CustomerUpdateRequest;
use App\Interfaces\Customers\CustomerInterface;
use App\Models\Customer;
use App\Models\Project;

class CustomerController extends Controller
{
    protected CustomerInterface $repository;

    public function __construct(CustomerInterface $customer)
    {
        $this->middleware('generate_fetch_query_params')->only('index','projects_reports','projects_invoices');
        $this->repository = $customer;
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
    public function store(CustomerCreateRequest $request)
    {
        return $this->repository->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        return $this->repository->show($customer);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerUpdateRequest $request,Customer $customer)
    {
        return $this->repository->update($request,$customer);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        return $this->repository->destroy($customer);
    }

    public function projects_fields(Customer $customer,Project $project)
    {
        return $this->repository->projects_fields($customer,$project);
    }

    public function projects_reports(Customer $customer,Project $project)
    {
        return $this->repository->projects_reports($customer,$project);

    }

    public function projects_invoices(Customer $customer,Project $project)
    {
        return $this->repository->projects_invoices($customer,$project);

    }
}
