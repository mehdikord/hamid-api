<?php
namespace App\Repositories\Users;

use App\Http\Resources\Projects\Projects\ProjectRelationResource;
use App\Http\Resources\Projects\Projects\ProjectShortResource;
use App\Http\Resources\User_Customers\Customers\UserCustomerProfileResource;
use App\Http\Resources\User_Customers\UserCustomerInvoiceResource;
use App\Http\Resources\User_Customers\UserCustomerReportResource;
use App\Http\Resources\Users\UserCustomerIndexResource;
use App\Interfaces\Users\UserCustomerInterface;
use App\Models\Project_Customer;
use App\Models\Project_Customer_Invoice;
use App\Models\Project_Customer_Report;
use App\Models\Projects\Invoice_Product;
use App\Models\Projects\Project_Product;
use App\Models\User_Project;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class UserCustomerRepository implements UserCustomerInterface
{
    public function users_index($user)
    {
        $data = $user->customers();
        $data->where('position_id',helper_data_position_seller());
        if (request()->filled('search')) {
            if (!empty(request()->search['status_id'])){
                $data->whereHas('project_customer', function ($query) {
                    $query->where('project_customer_status_id', request()->search['status_id']);
                });
            }
            if (!empty(request()->search['level_id'])){
                $data->whereHas('project_customer', function ($query) {
                    $query->where('project_level_id', request()->search['level_id']);
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

    public function users_consultants($user)
    {
        $data = $user->customers();
        $data->whereHas('project_customer',function($query){
            $query->whereHas('project',function($project_query){
                $project_query->where('is_active',true);
            });
        });
        $data->where('position_id',helper_data_position_consultant());

        if (request()->filled('search')) {

            if (!empty(request()->search['status_id'])){
                if(request()->search['status_id'] == 'no'){
                    $data->whereHas('project_customer', function ($query) {
                        $query->whereNull('project_customer_status_id');
                    });
                }else{
                    $data->whereHas('project_customer', function ($query) {
                        $query->where('project_customer_status_id', request()->search['status_id']);
                    });
                }

            }

            if (!empty(request()->search['level_id'])){
                if(request()->search['level_id'] == 'no'){
                    $data->whereHas('project_customer', function ($query) {
                        $query->whereNull('project_level_id');
                    });
                }else{
                    $data->whereHas('project_customer', function ($query) {
                        $query->where('project_level_id', request()->search['level_id']);
                    });
                }

            }

            if (!empty(request()->search['project_id'])){
                $data->whereHas('project_customer', function ($query) {
                    $query->where('project_id', request()->search['project_id']);
                });
            }

            if (!empty(request()->search['phone'])){
                $data->whereHas('project_customer', function ($project) {
                    $project->whereHas('customer', function ($customer) {
                        $customer->where('phone', 'LIKE', '%' . request()->search['phone'] . '%')->Orwhere('name', 'LIKE', '%' . request()->search['phone'] . '%');
                    });
                });
            }

            if (!empty(request()->search['from_date'])){
                $data->whereDate('start_at', '>=', request()->search['from_date']);
            }
            if (!empty(request()->search['to_date'])){
                $data->whereDate('start_at', '<=', request()->search['to_date']);
            }
        }

        $data->orderBy(request('sort_by'),request('sort_type'));
        return helper_response_fetch(UserCustomerIndexResource::collection($data->paginate(request('per_page')))->resource);

    }


    public function users_customers($user)
    {
        $data = $user->customers();

        if (request()->filled('search')) {
            if (!empty(request()->search['project_id']) && request()->search['project_id'] != 'all'){
                $data->whereHas('project_customer', function ($query) {
                    $query->where('project_id', request()->search['project_id']);
                });
            }
            if (!empty(request()->search['status_id']) && request()->search['status_id'] != 'all'){

                if(request()->search['status_id'] == 'no'){
                    $data->whereHas('project_customer', function ($query) {
                        $query->whereNull('project_customer_status_id');
                    });
                }else{
                    $data->whereHas('project_customer', function ($query) {
                        $query->where('project_customer_status_id', request()->search['status_id']);
                    });
                }
            }
            if (!empty(request()->search['level_id']) && request()->search['level_id'] != 'all'){
                if(request()->search['level_id'] == 'no'){
                    $data->whereHas('project_customer', function ($query) {
                        $query->whereNull('project_level_id');
                    });
                }else{
                    $data->whereHas('project_customer', function ($query) {
                        $query->where('project_level_id', request()->search['level_id']);
                    });
                }
            }

            if (!empty(request()->search['tag_id']) && request()->search['tag_id'] != 'all'){
                $data->whereHas('project_customer', function ($query) {
                    $query->whereHas('tags', function ($tag) {
                        $tag->where('tag_id', request()->search['tag_id']);
                    });
                });
            }

            if (!empty(request()->search['import_method_id']) && request()->search['import_method_id'] != 'all'){
                $data->whereHas('project_customer', function ($query) {
                    $query->whereHas('import_method', function ($import_method) {
                        $import_method->where('import_method_id', request()->search['import_method_id']);
                    });
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


    public function users_consultants_old($user)
    {
        $data = $user->customers();
        $data->where('position_id',helper_data_position_consultant());
        if (request()->filled('search')) {
            if (!empty(request()->search['status_id'])){
                $data->whereHas('project_customer', function ($query) {
                    $query->where('project_customer_status_id', request()->search['status_id']);
                });
            }

            if (!empty(request()->search['project_id'])){
                $data->whereHas('project_customer', function ($query) {
                    $query->where('project_id', request()->search['project_id']);
                });
            }

            if (!empty(request()->search['phone'])){
                $data->whereHas('project_customer', function ($project) {
                    $project->whereHas('customer', function ($customer) {
                        $customer->where('phone', 'LIKE', '%' . request()->search['phone'] . '%')->Orwhere('name', 'LIKE', '%' . request()->search['phone'] . '%');
                    });
                });
            }
        }

        $data->whereHas('project_customer', function ($query) {
            $query->whereIn('project_customer_status_id',[helper_data_customer_status_success(),helper_data_customer_status_failed()]);
        });
        $data->orderBy(request('sort_by'),request('sort_type'));
        return helper_response_fetch(UserCustomerIndexResource::collection($data->paginate(request('per_page')))->resource);

    }
    public function users_seller($user)
    {
        $data = $user->customers();
        $data->where('position_id',helper_data_position_seller());
        $data->whereHas('project_customer',function($query){
            $query->whereHas('project',function($project_query){
                $project_query->where('is_active',true);
            });
        });

        if (request()->filled('search')) {

            if (!empty(request()->search['status_id'])){
                if(request()->search['status_id'] == 'no'){
                    $data->whereHas('project_customer', function ($query) {
                        $query->whereNull('project_customer_status_id');
                    });
                }else{
                    $data->whereHas('project_customer', function ($query) {
                        $query->where('project_customer_status_id', request()->search['status_id']);
                    });
                }
            }
            if (!empty(request()->search['level_id'])){
                if(request()->search['level_id'] == 'no'){
                    $data->whereHas('project_customer', function ($query) {
                        $query->whereNull('project_level_id');
                    });
                }else{
                    $data->whereHas('project_customer', function ($query) {
                        $query->where('project_level_id', request()->search['level_id']);
                    });
                }
            }

            if (!empty(request()->search['project_id'])){
                $data->whereHas('project_customer', function ($query) {
                    $query->where('project_id', request()->search['project_id']);
                });
            }

            if (!empty(request()->search['phone'])){
                $data->whereHas('project_customer', function ($project) {
                    $project->whereHas('customer', function ($customer) {
                        $customer->where('phone', 'LIKE', '%' . request()->search['phone'] . '%')->Orwhere('name', 'LIKE', '%' . request()->search['phone'] . '%');
                    });
                });
            }
            if (!empty(request()->search['from_date'])){
                $data->whereDate('start_at', '>=', request()->search['from_date']);
            }
            if (!empty(request()->search['to_date'])){
                $data->whereDate('start_at', '<=', request()->search['to_date']);
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

        if($request->filled('messages')){

            if(is_array($request->messages)){
                foreach($request->messages as $message_key => $message_value){
                    $item->message_options()->create([
                        'message_option_id' => $message_value,
                    ]);
                }
            }else{
                foreach(json_decode($request->messages,true) as $message_key => $message_value){
                    $item->message_options()->create([
                        'message_option_id' => $message_value,
                    ]);
                }
            }
        }
        $customer->update([
            'project_customer_status_id' => $request->status_id,
            'project_level_id' => $request->project_level_id,
        ]);

        return helper_response_fetch(new UserCustomerIndexResource($customer->users()->where('user_id',auth('users')->id())->first()));
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
        if ($request->filled('status_id')){

             $new_status = $customer->statuses()->create([
                'customer_status_id' => $request->status_id,
                'customer_id' => $customer->customer_id,
                'user_id' => auth('users')->id(),
                'description' => $request->report,
            ]);
            $customer->update([
                'project_customer_status_id' => $request->status_id,
            ]);
            if($request->filled('messages')){
                if(is_array($request->messages)){
                    foreach($request->messages as $message_key => $message_value){
                        $new_status->message_options()->create([
                            'message_option_id' => $message_value,
                        ]);
                    }

                }else{
                    foreach(json_decode($request->messages,true) as $message_key => $message_value){
                        $new_status->message_options()->create([
                            'message_option_id' => $message_value,
                        ]);

                    }
                }

            }
        }

        if ($request->filled('project_level_id')){

             $customer->statuses()->create([
                'project_level_id' => $request->project_level_id,
                'customer_id' => $customer->customer_id,
                'user_id' => auth('users')->id(),
                'description' => $request->report,
            ]);
            $customer->update([
                'project_level_id' => $request->project_level_id,
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


        //check reminder data
        if($request->filled('reminder_title') && $request->filled('reminder_date')){
            auth('users')->user()->reminders()->create([
                'project_customer_id' => $customer->id,
                'title' => $request->reminder_title,
                'date' => $request->reminder_date,
                'offset' => $request->reminder_offset ?? '15',
                'status' => 'pending',
            ]);
        }


        $user_project = User_Project::where('project_id', $customer->project_id)->where('user_id',auth()->id())->first();
        if ($user_project){
            $user_project->update(['total_reports' => $user_project->total_reports + 1]);
        }
        // activity log
        helper_activity_create(null,null,$customer->project_id,$customer->customer_id,'ثبت گزارش'," : ثبت گزارش ".$request->report."");
        return helper_response_fetch(new UserCustomerIndexResource($item));
    }

    public function reports_delete($item)
    {


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
            'province_id' => $request->province_id,
            'city_id' => $request->city_id,
            'national_code' => $request->national_code,
            'instagram_id' => $request->instagram_id,
            'telegram_id' => $request->telegram_id,
            'tel' => $request->tel,
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
        // activity log
        helper_activity_create(null,null,$customer->project_id,$customer->customer_id,'ویرایش مشتری'," : ویرایش مشتری ".$request->name."");

        return helper_response_updated(new UserCustomerProfileResource($customer));
    }

    //Invoices
    public function invoices_store($customer, $request)
    {
        $product = Project_Product::find($request->project_product_id);


        //check sum invoices amount
        if ($request->price > $request->target_price){
            return helper_response_error('مبلغ فاکتور نباید بیشتر از مبلغ معامله باشد');
        }

        $date = Carbon::now();
        if ($request->filled('date')){
            $date = Carbon::make($request->date);
        }
        $paid = 0;
        if($request->price == $request->target_price){
            $paid = 1;
        }

        //create invoice
        $item = $customer->invoices()->create([
            'user_id' => auth('users')->id(),
            'project_id' => $customer->project_id,
            'description' => $request->description,
            'amount' => $request->price,
            'target_price' => $request->target_price,
            'paid' => $paid,
        ]);


        //handle file

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

        //create order
        $order = $item->orders()->create([
            'product_id' => $product->id,
            'project_id' => $customer->project_id,
            'quantity' => 1,
            'amount' => $request->price,
            'file_path' => $file_path,
            'file_url' => $file_url,
            'created_at' => $date,
        ]);



        //handle reminder
        if($request->filled("reminder_title") && $request->filled("reminder_date")){
            auth('users')->user()->reminders()->create([
                'title' => $request->reminder_title,
                'description' => $request->reminder_description,
                'date' => $request->reminder_date,
                'time' => $request->reminder_time,
                'offset' => $request->reminder_offset ?? '15',
                'status' => 'pending',
            ]);
        }

        $user_project = User_Project::where('project_id', $customer->project_id)->where('user_id',auth()->id())->first();
        if ($user_project){
            $user_project->update(['total_price' => $user_project->total_price + $item->amount]);
        }

        // activity log
        helper_activity_create(null,null,$customer->project_id,$customer->customer_id,'ثبت فاکتور',"# : ثبت فاکتور ".$item->id."");
        helper_bot_send_group_invoice($order);


        return helper_response_fetch(new UserCustomerInvoiceResource($item));


    }

    public function invoices_target_store($customer,$request)
    {
        if (!$customer->target_price){
            $customer->update(['target_price' => $request->price]);
            //get user project customer
            $user_project_customer = $customer->users()->where('user_id',auth()->id())->first();
            return helper_response_fetch(new UserCustomerIndexResource($user_project_customer));
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

        return helper_response_fetch(ProjectRelationResource::collection($result));
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

           $new_status = $project_customer->statuses()->create([
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
            if($request->filled('messages')){
                foreach(json_decode( $request->messages) as $message_key => $message_value){
                    $new_status->message_options()->create([
                        'message_option_id' => $message_value,
                    ]);

                }
            }
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
        //activity log
        helper_activity_create(null,null,$project_customer->project_id,$project_customer->customer_id,'ثبت گزارش'," : ثبت گزارش ".$request->report."");
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
        if ($request->price > $request->target_price){
            return helper_response_error('مبلغ فاکتور نباید بیشتر از مبلغ معامله باشد');
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
        $paid = 0;
        if($request->price == $request->target_price){
            $paid = 1;
        }

        $item = $project_customer->invoices()->create([
            'user_id' => auth('users')->id(),
            'description' => $request->description,
            'amount' => $request->price,
            'target_price' => $request->target_price,
            'paid' => $paid,
            'created_at' => $date,
        ]);

        //create order
        $order = $item->orders()->create([
            'product_id' => $request->project_product_id,
            'quantity' => 1,
            'amount' => $request->price,
            'file_path' => $file_path,
            'file_url' => $file_url,
            'created_at' => $date,
        ]);


        //handle reminder
        if($request->filled("reminder_title") && $request->filled("reminder_date")){
            auth('users')->user()->reminders()->create([
                'title' => $request->reminder_title,
                'description' => $request->reminder_description,
                'date' => $request->reminder_date,
                'time' => $request->reminder_time,
                'offset' => $request->reminder_offset ?? '15',
                'status' => 'pending',
            ]);
        }

        //activity log
        helper_activity_create(null,null,$project_customer->project_id,$project_customer->customer_id,'ثبت فاکتور',"# : ثبت فاکتور ".$item->id."");
        helper_bot_send_group_invoice($item);
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
                $final[]=['id' => $level->project_level_id, 'name' => $level->name];
            }
            return helper_response_fetch($final);
        }

    }


}
