<?php

namespace App\Http\Controllers\Users\Customers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customers\CustomerUpdateRequest;
use App\Http\Requests\User_Customers\Invoices\UserCustomerInvoiceStoreRequest;
use App\Http\Requests\User_Customers\Invoices\UserCustomerInvoiceTargetStoreRequest;
use App\Http\Requests\User_Customers\Reports\UserCustomerReportStoreRequest;
use App\Http\Requests\User_Customers\UserCustomerStatusStoreRequest;
use App\Interfaces\Customers\CustomerSettingsStatusInterface;
use App\Interfaces\ProjectLevels\ProjectLevelInterface;
use App\Interfaces\Users\UserCustomerInterface;
use App\Models\Customer;
use App\Models\Project;
use App\Models\Project_Customer;
use App\Models\Project_Customer_Report;

class CustomerController extends Controller
{
    protected UserCustomerInterface $repository;
    protected CustomerSettingsStatusInterface $setting_repository;

    protected ProjectLevelInterface $project_level_repository;
    public function __construct(UserCustomerInterface $customer,CustomerSettingsStatusInterface $customerSettingsStatus,ProjectLevelInterface $level)
    {
        $this->middleware('generate_fetch_query_params')->only('index','reports_index');

        $this->repository = $customer;
        $this->setting_repository = $customerSettingsStatus;
        $this->project_level_repository = $level;
    }

    public function index()
    {
        return $this->repository->users_index(auth('users')->user());
    }

    public function consultant()
    {
        return $this->repository->users_consultants(auth('users')->user());
    }

    public function seller()
    {
        return $this->repository->users_seller(auth('users')->user());
    }

    public function consultant_old()
    {
        return $this->repository->users_consultants_old(auth('users')->user());
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
    public function levels()
    {
        return $this->project_level_repository->all();
    }

    public function statuses_store(Project_Customer $customer,UserCustomerStatusStoreRequest $request)
    {
        return $this->repository->statuses_store($customer,$request);
    }

    public function reports_index(Customer $customer)
    {
        return $this->repository->reports_index($customer);
    }
    public function reports_store(Project_Customer $customer,UserCustomerReportStoreRequest $request)
    {
        return $this->repository->reports_store($customer,$request);
    }

    public function reports_delete(Project_Customer $customer,Project_Customer_Report $report)
    {
        return $this->repository->reports_delete($report);

    }

    public function invoices_index(Customer $customer)
    {
        return $this->repository->invoices_index($customer);
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

    public function projects_own(Customer $customer,Project $project)
    {
        return $this->repository->projects_own($customer,$project);
    }

    public function projects_report_store(Customer $customer,Project $project,UserCustomerReportStoreRequest $request)
    {
        return $this->repository->projects_report_store($customer,$project,$request);
    }

    public function projects_invoice_store(Customer $customer,Project $project,UserCustomerInvoiceStoreRequest $request)
    {
        return $this->repository->projects_invoice_store($customer,$project,$request);
    }

    public function projects_fields(Customer $customer,Project $project)
    {
        return $this->repository->projects_fields($customer,$project);
    }
    public function projects_levels(Customer $customer,Project $project)
    {
        return $this->repository->projects_levels($customer,$project);
    }



}
