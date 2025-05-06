<?php

namespace App\Http\Controllers\Users\Customers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customers\CustomerUpdateRequest;
use App\Http\Requests\User_Customers\Invoices\UserCustomerInvoiceStoreRequest;
use App\Http\Requests\User_Customers\Invoices\UserCustomerInvoiceTargetStoreRequest;
use App\Http\Requests\User_Customers\Reports\UserCustomerReportStoreRequest;
use App\Http\Requests\User_Customers\UserCustomerStatusStoreRequest;
use App\Interfaces\Customers\CustomerSettingsStatusInterface;
use App\Interfaces\Users\UserCustomerInterface;
use App\Models\Customer;
use App\Models\Project_Customer;

class CustomerController extends Controller
{
    protected UserCustomerInterface $repository;
    protected CustomerSettingsStatusInterface $setting_repository;
    public function __construct(UserCustomerInterface $customer,CustomerSettingsStatusInterface $customerSettingsStatus)
    {
        $this->middleware('generate_fetch_query_params')->only('index');

        $this->repository = $customer;
        $this->setting_repository = $customerSettingsStatus;
    }

    public function index()
    {
        return $this->repository->users_index(auth('users')->user());
    }

    public function show(Customer $customer)
    {
        return $this->repository->show($customer);
    }

    public function update(Customer $customer,CustomerUpdateRequest $request)
    {
        return $this->repository->update($customer,$request);
    }

    public function statuses()
    {
        return $this->setting_repository->all();
    }

    public function statuses_store(Project_Customer $customer,UserCustomerStatusStoreRequest $request)
    {
        return $this->repository->statuses_store($customer,$request);
    }

    public function reports_store(Project_Customer $customer,UserCustomerReportStoreRequest $request)
    {
        return $this->repository->reports_store($customer,$request);
    }

    public function invoices_store(Project_Customer $customer,UserCustomerInvoiceStoreRequest $request)
    {
        return $this->repository->invoices_store($customer,$request);
    }

    public function all_reports_latest(Customer $customer)
    {
        return $this->repository->all_reports_latest($customer);
    }

    public function all_invoice_latest(Customer $customer)
    {
        return $this->repository->all_invoice_latest($customer);
    }

    public function invoices_target_store(Project_Customer $customer,UserCustomerInvoiceTargetStoreRequest $request)
    {
        return $this->repository->invoices_target_store($customer,$request);
    }

    public function projects(Customer $customer)
    {
        return $this->repository->projects($customer);
    }

}
