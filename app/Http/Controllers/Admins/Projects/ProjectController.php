<?php

namespace App\Http\Controllers\Admins\Projects;

use App\Http\Controllers\Controller;
use App\Http\Requests\Projects\Categories\ProjectCategoryCreateRequest;
use App\Http\Requests\Projects\Categories\ProjectCategoryUpdateRequest;
use App\Http\Requests\Projects\Customers\ProjectCustomersAssignedRequest;
use App\Http\Requests\Projects\Forms\ProjectFormsCreateRequest;
use App\Http\Requests\Projects\Forms\ProjectFormsUpdateRequest;
use App\Http\Requests\Projects\Invoices\ProjectInvoiceUpdateRequest;
use App\Http\Requests\Projects\levels\ProjectLevelPriorityRequest;
use App\Http\Requests\Projects\levels\ProjectLevelRequest;
use App\Http\Requests\Projects\Projects\ProjectCreateRequest;
use App\Http\Requests\Projects\Projects\ProjectCustomersCreateRequest;
use App\Http\Requests\Projects\Projects\ProjectUpdateRequest;
use App\Http\Requests\Projects\Projects\Reports\ProjectReportCreateRequest;
use App\Interfaces\Projects\ProjectCategoryInterface;
use App\Interfaces\Projects\ProjectInterface;
use App\Models\Project;
use App\Models\Project_Category;
use App\Models\Project_Customer;
use App\Models\Project_Customer_Invoice;
use App\Models\Project_Customer_Report;
use App\Models\Project_Form;
use App\Models\Projects_Levels;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    protected ProjectInterface $repository;

    public function __construct(ProjectInterface $project)
    {
        $this->middleware('generate_fetch_query_params')->only('index','all','get_customers','reports','invoices');
        $this->repository = $project;
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

    public function pending_customers(Project $project)
    {
        return $this->repository->pending_customers($project);
    }

    public function pending_customers_success(Project $project)
    {
        return $this->repository->pending_customers_success($project);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProjectCreateRequest $request)
    {
        return $this->repository->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        return $this->repository->show($project);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProjectUpdateRequest $request,Project $project)
    {
        return $this->repository->update($request,$project);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        return $this->repository->destroy($project);
    }

    public function all_customers(Project $project)
    {
        return $this->repository->all_customers($project);
    }

    public function add_customers(ProjectCustomersCreateRequest $request,Project $project)
    {
        return $this->repository->add_customers($request,$project);
    }

    public function get_customers(Project $project)
    {
        return $this->repository->get_customers($project);
    }

    public function customers_change_status(Project $project,Request $request,)
    {
        return $this->repository->customers_change_status($request,$project);
    }
    public function customers_change_level(Project $project,Request $request,)
    {
        return $this->repository->customers_change_level($request,$project);
    }

    public function delete_customers(Project $project,Project_Customer $customer)
    {
        return $this->repository->delete_customers($project,$customer);
    }

    public function delete_multi(Project $project,Request $request)
    {
        return $this->repository->delete_multi($project,$request);

    }

    public function assigned_customers(Project $project,ProjectCustomersAssignedRequest $request)
    {
        return $this->repository->assigned_customers($project,$request);
    }

    public function assigned_customers_single(Project $project,Request $request)
    {
        return $this->repository->assigned_customers_single($project,$request);
    }

    public function assigned_customers_multi(Project $project,Request $request)
    {
        return $this->repository->assigned_customers_multi($project,$request);
    }

    public function reports(Project $project)
    {
        return $this->repository->reports($project);
    }

    public function reports_store(Project $project,ProjectReportCreateRequest $request)
    {
        return $this->repository->reports_store($project,$request);

    }

    public function reports_update(Project $project,Project_Customer_Report $report,Request $request)
    {
        return $this->repository->reports_update($project,$report,$request);
    }
    public function reports_destroy(Project $project,Project_Customer_Report $report)
    {
        return $this->repository->reports_destroy($project,$report);
    }
    public function invoices(Project $project)
    {
        return $this->repository->invoices($project);

    }

    public function get_latest_reports(Project $project)
    {
        return $this->repository->get_latest_reports($project);
    }

    public function invoices_update(Project $project,Project_Customer_Invoice $invoice,ProjectInvoiceUpdateRequest $request)
    {
        return $this->repository->invoices_update($project,$invoice,$request);
    }

    public function invoices_settle(Project $project,Project_Customer_Invoice $invoice)
    {
        return $this->repository->invoices_settle($project,$invoice);

    }

    public function invoices_destroy(Project $project,Project_Customer_Invoice $invoice)
    {
        return $this->repository->invoices_destroy($project,$invoice);
    }

    public function get_latest_invoices(Project $project)
    {
        return $this->repository->get_latest_invoices($project);
    }

    //Fields
    public function get_fields(Project $project)
    {
        return $this->repository->get_fields($project);
    }

    public function store_fields(Project $project,Request $request)
    {
        return $this->repository->store_fields($project,$request);
    }

    //Fields
    public function get_positions(Project $project)
    {
        return $this->repository->get_positions($project);
    }

    public function store_positions(Project $project,Request $request)
    {
        return $this->repository->store_positions($project,$request);
    }

    //Levels

    public function get_levels(Project $project)
    {
        return $this->repository->get_levels($project);
    }

    public function store_levels(Project $project,ProjectLevelRequest $request)
    {
        return $this->repository->store_levels($project,$request);
    }

    public function update_levels(Project $project,Projects_Levels $level,ProjectLevelPriorityRequest $request)
    {
        return $this->repository->update_levels($project,$level,$request);
    }

    public function delete_levels(Project $project,Projects_Levels $level)
    {
        return $this->repository->delete_levels($project,$level);
    }

    //Forms

    public function get_forms(Project $project)
    {
        return $this->repository->get_forms($project);

    }
    public function store_forms(Project $project,ProjectFormsCreateRequest $request)
    {
        return $this->repository->store_forms($project,$request);
    }

    public function update_forms(Project $project,Project_Form $form,ProjectFormsUpdateRequest $request)
    {
        return $this->repository->update_forms($project,$form,$request);
    }

    public function destroy_forms(Project $project,Project_Form $form)
    {
        return  $this->repository->destroy_forms($project,$form);
    }

    public function activation_forms(Project $project,Project_Form $form)
    {
        return  $this->repository->activation_forms($project,$form);
    }



}
