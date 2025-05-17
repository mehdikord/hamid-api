<?php
namespace App\Repositories\Users;
use App\Http\Resources\Projects\Projects\ProjectShortResource;
use App\Http\Resources\User_Customers\Customers\UserCustomerProfileResource;
use App\Http\Resources\User_Customers\UserCustomerInvoiceResource;
use App\Http\Resources\User_Customers\UserCustomerReportResource;
use App\Http\Resources\User_Customers\UserCustomerStatusResource;
use App\Http\Resources\Users\UserCustomerIndexResource;
use App\Interfaces\Users\UserCustomerInterface;
use App\Models\Customer;
use App\Models\Fields\Project_Customer_Field;
use App\Models\Project;
use App\Models\Project_Customer;
use App\Models\Project_Customer_Invoice;
use App\Models\Project_Customer_Report;
use App\Models\User_Project;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class UserCustomerRepository implements UserCustomerInterface
{
    public function users_index($user)
    {
        $data = $user->customers();
        if (request()->filled('search')) {
            if (!empty(request()->search['status_id'])){
                $data->whereHas('project_customer', function ($query) {
                    $query->where('project_customer_status_id', request()->search['status_id']);
                });
            }
            if (!empty(request()->search['phone'])){
                $data->whereHas('project_customer', function ($project) {
                    $project->whereHas('customer', function ($customer) {
                        $customer->where('phone', 'LIKE', '%' . request()->search['phone'] . '%');
                    });
                });
            }
        }

        $data->orderBy(request('sort_by'),request('sort_type'));
        return helper_response_fetch(UserCustomerIndexResource::collection($data->paginate(request('per_page')))->resource);
    }

    public function statuses_store($customer,$request)
    {
        $item = $customer->statuses()->create([
            'customer_status_id' => $request->status_id,
            'project_level_id' => $request->project_level_id,
            'customer_id' => $customer->customer_id,
            'user_id' => auth('users')->id(),
            'description' => $request->description,
        ]);
        $customer->update([
            'project_customer_status_id' => $request->status_id,
            'project_level_id' => $request->project_level_id,
        ]);
        return helper_response_fetch(new UserCustomerIndexResource($customer->user));
    }

    //Reports
    public function reports_store($customer,$request)
    {
        $file_url = null;
        $file_size = null;
        $file_path = null;
        $file_name = null;
        if ($request->hasFile('file')){
            $file_name = $request->file('file')->getClientOriginalName();
            $file_size = $request->file('file')->getSize();
            $file_path = Storage::put('public/users/reports/'.$customer->id.'/', $request->file('file'),'public');
            $file_url = Storage::url($file_path);
        }
        $date = Carbon::now();
        if ($request->filled('date')){
            $date = Carbon::make($request->date);
        }

        //check change status
        if ($request->filled('status_id') && $request->status_id != $customer->project_customer_status_id ){

             $customer->statuses()->create([
                'customer_status_id' => $request->status_id,
                'customer_id' => $customer->customer_id,
                'user_id' => auth('users')->id(),
                'description' => $request->report,
            ]);
            $customer->update([
                'project_customer_status_id' => $request->status_id,
            ]);
        }

        $item = $customer->reports()->create([
            'user_id' => auth('users')->id(),
            'project_id' => $customer->project_id,
            'file_path' => $file_path,
            'file_url' => $file_url,
            'file_size' => $file_size,
            'file_name' => $file_name,
            'report' => $request->report,
            'created_at' => $date,
        ]);
        $user_project = User_Project::where('project_id', $customer->project_id,'user_id',auth()->id())->first();
        if ($user_project){
            $item->update(['total_reports' => $user_project->total_reports + 1]);
        }
        return helper_response_fetch(new UserCustomerReportResource($item));
    }

    public function all_reports_latest($customer)
    {
        $projects = helper_core_get_user_customer_access($customer);
        $data = Project_Customer_Report::query();
        $data->whereIn('project_customer_id', $projects);
        $data->orderByDesc('created_at');
        return helper_response_fetch(UserCustomerReportResource::collection($data->take(5)->get()));

    }

    public function reports_index($customer)
    {
        $projects = helper_core_get_user_customer_access($customer);
        $data = Project_Customer_Report::query();
        $data->whereIn('project_customer_id', $projects);
        $data->orderBy(request('sort_by'),request('sort_type'));
        return helper_response_fetch(UserCustomerReportResource::collection($data->paginate(request('per_page')))->resource);

    }

    public function all_invoice_latest($customer)
    {
        $projects = helper_core_get_user_customer_access($customer);
        $data = Project_Customer_Invoice::query();
        $data->whereIn('project_customer_id', $projects);
        $data->orderByDesc('created_at');
        return helper_response_fetch(UserCustomerInvoiceResource::collection($data->take(5)->get()));

    }
    public function invoices_index($customer)
    {
        $projects = helper_core_get_user_customer_access($customer);
        $data = Project_Customer_Invoice::query();
        $data->whereIn('project_customer_id', $projects);
        $data->orderBy(request('sort_by'),request('sort_type'));
        return helper_response_fetch(UserCustomerInvoiceResource::collection($data->paginate(request('per_page')))->resource);
    }

    public function show($customer)
    {
        return helper_response_fetch(new UserCustomerProfileResource($customer));

    }

    public function update($customer, $request)
    {
        //TODO create log for users
        $customer->update([
            'name' => $request->name,
            'email' => $request->email,
            'national_code' => $request->national_code,
            'instagram_id' => $request->instagram_id,
            'tel' => $request->tel,
            'job' => $request->job,
            'register_reason' => $request->register_reason,
            'obstacles' => $request->obstacles,
            'address' => $request->address,
            'postal_code' => $request->postal_code,
        ]);
        if ($request->filled('project_id') && $request->filled('fields')){
            $customer_project = $customer->projects()->where('project_id', $request->project_id)->first();
            if ($customer_project){
                $customer_project->fields()->delete();
                foreach ($request->fields as $field_key => $field_value){
                    $customer_project->fields()->create([
                        'user_id' => auth('users')->id(),
                        'field_id' => $field_key,
                        'val' => $field_value,
                    ]);
                }
            }
        }



        return helper_response_updated(new UserCustomerProfileResource($customer));
    }

    //Invoices
    public function invoices_store($customer, $request)
    {
        //check sum invoices amount
        if ($customer->invoices()->sum('amount') + $request->price > $customer->user->target_price ){
            return helper_response_error('مجموع مبلغ فاکتور های ثبت شده نباید بیشتر از مبلغ معامله باشد');
        }
        $file_url = null;
        $file_size = null;
        $file_path = null;
        $file_name = null;
        if ($request->hasFile('file')){
            $file_name = $request->file('file')->getClientOriginalName();
            $file_size = $request->file('file')->getSize();
            $file_path = Storage::put('public/users/invoices/'.$customer->id.'/', $request->file('file'),'public');
            $file_url = Storage::url($file_path);
        }

        $date = Carbon::now();
        if ($request->filled('date')){
            $date = Carbon::make($request->date);
        }
        $item = $customer->invoices()->create([
            'user_id' => auth('users')->id(),
            'project_id' => $customer->project_id,
            'description' => $request->description,
            'amount' => $request->price,
            'created_at' => $date,
            'file_path' => $file_path,
            'file_url' => $file_url,
            'file_size' => $file_size,
            'file_name' => $file_name,
        ]);
        $user_project = User_Project::where('project_id', $customer->project_id,'user_id',auth()->id())->first();
        if ($user_project){
            $item->update(['total_price' => $user_project->total_price + $item->amount]);
        }
        return helper_response_fetch(new UserCustomerInvoiceResource($item));
    }

    public function invoices_target_store($customer,$request)
    {
        if (!$customer->user->target_price){
            $customer->user->update(['target_price' => $request->price]);
            return helper_response_fetch(new UserCustomerIndexResource($customer->user));
        }
        return helper_response_error('ُTarget price already exists !');
    }

    public function dashboard($customer)
    {

    }

    public function projects($customer)
    {
        $projects = helper_core_get_user_customer_access($customer);
        $customer_projects = Project_Customer::whereIn('id', $projects)->get();
        $result=[];
        foreach ($customer_projects as $customer_project){
            if ($customer_project->project){
                $result[]=$customer_project->project;
            }
        }
        return helper_response_fetch(ProjectShortResource::collection($result));
    }

    public function projects_own($customer, $project)
    {
        $project_customer = $customer->projects()->whereIn('id',helper_core_get_user_customer_access($customer))->where('project_id', $project->id)->first();
        if (!$project_customer){
            return helper_response_error('شما دسترسی لازم به این پروژه را ندارید');
        }
        $user_project = $project_customer->user;
        if (!$user_project){
            return helper_response_error('شما دسترسی لازم به این پروژه را ندارید');
        }
        return helper_response_fetch(new UserCustomerIndexResource($user_project));
    }

    public function projects_report_store($customer,$project,$request)
    {
        //Find project customer
        $project_customer = $customer->projects()->whereIn('id',helper_core_get_user_customer_access($customer))->where('project_id', $project->id)->first();
        if (!$project_customer){
            return helper_response_error('شما دسترسی لازم به این پروژه را ندارید');
        }


        $file_url = null;
        $file_size = null;
        $file_path = null;
        $file_name = null;
        if ($request->hasFile('file')){
            $file_name = $request->file('file')->getClientOriginalName();
            $file_size = $request->file('file')->getSize();
            $file_path = Storage::put('public/users/reports/'.$project_customer->id.'/', $request->file('file'),'public');
            $file_url = Storage::url($file_path);
        }
        $date = Carbon::now();
        if ($request->filled('date')){
            $date = Carbon::make($request->date);
        }

        //check change status
        if ($request->filled('status_id') && $request->status_id != $project_customer->project_customer_status_id ){

            $project_customer->statuses()->create([
                'customer_status_id' => $request->status_id,
                'project_level_id' => $request->project_level_id,
                'customer_id' => $project_customer->customer_id,
                'user_id' => auth('users')->id(),
                'description' => $request->report,
            ]);
            $project_customer->update([
                'project_customer_status_id' => $request->status_id,
                'project_level_id' => $request->project_level_id,

            ]);
        }

        $item = $project_customer->reports()->create([
            'user_id' => auth('users')->id(),
            'project_id' => $project_customer->project_id,
            'file_path' => $file_path,
            'file_url' => $file_url,
            'file_size' => $file_size,
            'file_name' => $file_name,
            'report' => $request->report,
            'created_at' => $date,
        ]);
        return helper_response_fetch(new UserCustomerReportResource($item));

    }

    public function projects_invoice_store($customer,$project,$request)
    {
        //Find project customer
        $project_customer = $customer->projects()->whereIn('id',helper_core_get_user_customer_access($customer))->where('project_id', $project->id)->first();
        if (!$project_customer){
            return helper_response_error('شما دسترسی لازم به این پروژه را ندارید');
        }

        //check sum invoices amount
        if ($project_customer->invoices()->sum('amount') + $request->price > $project_customer->user->target_price ){
            return helper_response_error('مجموع مبلغ فاکتور های ثبت شده نباید بیشتر از مبلغ معامله باشد');
        }

        $file_url = null;
        $file_size = null;
        $file_path = null;
        $file_name = null;
        if ($request->hasFile('file')){
            $file_name = $request->file('file')->getClientOriginalName();
            $file_size = $request->file('file')->getSize();
            $file_path = Storage::put('public/users/invoices/'.$project_customer->id.'/', $request->file('file'),'public');
            $file_url = Storage::url($file_path);
        }

        $date = Carbon::now();
        if ($request->filled('date')){
            $date = Carbon::make($request->date);
        }
        $item = $project_customer->invoices()->create([
            'user_id' => auth('users')->id(),
            'project_id' => $project_customer->project_id,
            'description' => $request->description,
            'amount' => $request->price,
            'created_at' => $date,
            'file_path' => $file_path,
            'file_url' => $file_url,
            'file_size' => $file_size,
            'file_name' => $file_name,
        ]);
        return helper_response_fetch(new UserCustomerInvoiceResource($item));
    }

    public function projects_fields($customer, $project)
    {
        $project_customer = $customer->projects()->where('project_id', $project->id)->first();
        if ($project_customer){
            return helper_response_fetch($project_customer->fields);
        }

    }

    public function projects_levels($customer, $project)
    {
        $project_customer = $customer->projects()->where('project_id', $project->id)->first();
        if ($project_customer){
            $final=[];
            foreach ($project_customer->project->levels as $level){
                $final[]=['id' => $level->project_level_id, 'name' => $level->level->name];
            }
            return helper_response_fetch($final);
        }

    }


}
