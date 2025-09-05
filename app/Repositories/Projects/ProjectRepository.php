<?php
namespace App\Repositories\Projects;

use App\Exports\ProjectCustomersExport;
use App\Http\Resources\Fields\FieldIndexResource;
use App\Http\Resources\Positions\PositionsShortResource;
use App\Http\Resources\Projects\Forms\ProjectFromIndexResource;
use App\Http\Resources\Projects\Invoices\ProjectInvoiceIndexResource;
use App\Http\Resources\Projects\levels\ProjectLevelIndexResource;
use App\Http\Resources\Projects\Projects\ProjectCustomerIndexResource;
use App\Http\Resources\Projects\Projects\ProjectCustomerShortResource;
use App\Http\Resources\Projects\Projects\ProjectIndexResource;
use App\Http\Resources\Projects\Projects\ProjectPositionResource;
use App\Http\Resources\Projects\Projects\ProjectShortResource;
use App\Http\Resources\Projects\Projects\ProjectSingleResource;
use App\Http\Resources\Projects\Reports\ProjectReportIndexResource;
use App\Http\Resources\Users\UserProjectCustomerResource;
use App\Interfaces\Projects\ProjectInterface;
use App\Models\Customer;
use App\Models\Project;
use App\Models\Project_Customer;
use App\Models\Project_Customer_Invoice;
use App\Models\Project_Customer_Report;
use App\Models\Project_Customer_Status;
use App\Models\Project_Level;
use App\Models\User_Project;
use App\Models\User_Project_Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Morilog\Jalali\Jalalian;


class ProjectRepository implements ProjectInterface
{

   public function index()
   {
       $data = Project::query();
       $data->orderBy(request('sort_by'),request('sort_type'));
       $data->withCount('users');
       return helper_response_fetch(ProjectIndexResource::collection($data->paginate(request('per_page')))->resource);
   }

   public function all()
   {
       $data = Project::query();
       $data->orderBy(request('sort_by'),request('sort_type'));
       return helper_response_fetch(ProjectShortResource::collection($data->get()));
   }

   public function pending_customers($project)
   {
       $data = $project->customers();
       $data->where('status', Project_Customer::STATUS_PENDING);
       $data->whereDoesntHave('users');

       if (request()->filled('search')){

            if (isset(request()->search['has_name']) && request()->search['has_name'] != 'all'){
                if (request()->search['has_name'] == '1'){
                    $data->whereHas('customer',function ($query){
                        $query->whereNotNull('name');
                    });
                }else{
                    $data->whereHas('customer',function ($query){
                        $query->whereNull('name');
                    });
                }
            }
            if(isset(request()->search['import_method'])){
                $data->where('import_method_id',request()->search['import_method']);
            }
            if(isset(request()->search['tag'])){
                $data->whereHas('tags',function ($query){
                    $query->where('tag_id',request()->search['tag']);
                });
            }
            if(isset(request()->search['from_date']) && !empty(request()->search['from_date'])){
                $fromDate = Carbon::make(request()->search['from_date']);
                $data->where('created_at','>=',$fromDate);
            }
            if(isset(request()->search['to_date']) && !empty(request()->search['to_date'])){

                $toDate = Carbon::make(request()->search['to_date']);
                $data->where('created_at','<=',$toDate);
            }
       }

       if (request()->filled('total') && is_numeric(request()->total) && request()->total > 0){
           $result = $data->take(request()->total)->get();
       }else{
           $result = $data->get();
       }
       return helper_response_fetch(ProjectCustomerShortResource::collection($result));
   }

    public function pending_customers_success($project)
    {

        $data = $project->customers()->where('project_customer_status_id',helper_data_customer_status_success());
        $data->with('users',function ($user){
            $user->where('position_id',1);
        });
        return helper_response_fetch(ProjectCustomerShortResource::collection($data->get()));

    }

    public function store($request)
   {
       $data = Project::create([
           'project_category_id' => $request->project_category_id,
           'project_status_id' => $request->project_status_id,
           'name' => $request->name,
           'manager_name' => $request->manager_name,
           'manager_phone' => $request->manager_phone,
           'start_at' => $request->start_at,
           'end_at' => $request->end_at,
           'description' => $request->description,
           'is_active' => true,
       ]);
       $data->update(['code' => helper_core_code_generator($data->id)]);
       return helper_response_fetch(new ProjectSingleResource($data));
   }

   public function show($item)
   {
       return helper_response_fetch(new ProjectSingleResource($item));
   }

   public function update($request, $item)
   {
       $item->update([
           'project_category_id' => $request->project_category_id,
           'project_status_id' => $request->project_status_id,
           'name' => $request->name,
           'manager_name' => $request->manager_name,
           'manager_phone' => $request->manager_phone,
           'start_at' => $request->start_at,
           'end_at' => $request->end_at,
           'description' => $request->description,
       ]);
       return helper_response_updated(new ProjectSingleResource($item));
   }


   public function destroy($item)
   {
       $item->delete();
       return helper_response_deleted();
   }

   public function all_customers($item)
   {
       $data = $item->customers();
       $result=[];
       //
       if (request()->filled('search')){
           $data->whereHas('customer',function ($query){
              $query->where('name','LIKE','%'.request('search').'%')->orWhere('phone','LIKE','%'.request('search').'%');
           });
           $result = $data->get();
       }
       return helper_response_fetch(ProjectCustomerShortResource::collection($result));
   }

    public function change_activation($item)
   {
       $item->update(['is_active' => !$item->is_active]);
       return helper_response_updated([]);
   }

    public function add_customers($request,$item)
    {
        $exists_projects=[];
        $counter=0;
        //check the Excel file
        if ($request->hasFile('excel')) {
            $excel = Excel::toArray([], request()->file('excel'));
            $import_date = null;
            foreach ($excel[0] as $key => $value) {
                if ($value['0']){
                    if (mb_substr($value['0'], 0, 1, 'UTF-8') != '0'){
                        $value['0'] = '0'.$value['0'];
                    }
                }
                $customer = Customer::where('phone',$value[0])->first();
                $find_customer = $item->customers()->where('customer_id',$customer->id)->first();
                if ($customer && $find_customer) {
                    $exists_projects[] = [
                        'phone' => $value[0],
                        'created_at' => $find_customer->created_at,
                        'users' => UserProjectCustomerResource::collection($find_customer->users),
                    ];

                }else{
                    if (!$customer){
                        $customer = Customer::create([
                            'phone' => $value[0],
                            'name' => $value[1],
                            'instagram_id' => $value[2],
                        ]);
                    }
                    if ($value[3]){
                        $jalali = Jalalian::fromFormat('Y/m/d', $value[3]);
                        $import_date = $jalali->toCarbon();
                    }

                    $new_customer = $item->customers()->create([
                        'customer_id' => $customer->id,
                        'import_method_id' => $request->import_method_id,
                        'import_at' => $import_date,
                        'created_at' => $import_date,
                        'description' => $request->description,
                        'status' => Project_Customer::STATUS_PENDING,
                    ]);
                    if ($request->filled('tags')){
                        $new_customer->tags()->attach($request->tags);
                    }
                    $counter++;
                }
            }

        }
        //check numbers
        if ($request->filled('numbers')){
            $numbers = explode(',',$request->numbers);
            if (is_array($numbers) && count($numbers)){
                foreach ($numbers as $number){
                    $number = str_replace(' ','',$number);
                    if (mb_substr($number, 0, 1, 'UTF-8') != '0'){
                        $number = '0'.$number;
                    }
                    $customer = Customer::where('phone',$number)->first();
                    $find_customer = null;
                    if ($customer){
                        $find_customer = $item->customers()->where('customer_id',$customer->id)->first();
                    }
                    if ($customer && $find_customer) {
                        $exists_projects[] = [
                            'phone' => $number,
                            'created_at' => $find_customer->created_at,
                            'users' => UserProjectCustomerResource::collection($find_customer->users),
                        ];
                    }else{
                        if (!$customer){
                            $customer = Customer::create([
                                'phone' => $number,
                            ]);
                        }
                        $new_customer = $item->customers()->create([
                            'customer_id' => $customer->id,
                            'import_method_id' => $request->import_method_id,
                            'import_at' => Carbon::now(),
                            'description' => $request->description,
                            'status' => Project_Customer::STATUS_PENDING,
                        ]);
                        if ($request->filled('tags')){
                            $new_customer->tags()->attach($request->tags);
                        }
                        $counter++;
                    }
                }
            }
        }
        $item->update(['total_customers' => $counter + $item->total_customers]);
        return helper_response_created($exists_projects);
    }

    public function get_customers($item)
    {
        $data = $item->customers();
        if (request()->filled('search') && request()->search['status_id'] != 0 ){
            if (request()->search['status_id'] == 'none'){
                $data->whereNull('project_customer_status_id');
            }else{
                $data->where('project_customer_status_id', request()->search['status_id']);

            }
        }
        if (request()->filled('search') && request()->search['level_id'] != 0 ){
            if (request()->search['level_id'] == 'none'){
                $data->whereNull('project_level_id');
            }else{
                $data->where('project_level_id', request()->search['level_id']);
            }
        }


        if (request()->filled('search') && request()->search['seller_id'] != 0 ){

            if (request()->search['seller_id'] == 'none'){
                $data->whereDoesntHave('users',function ($query){
                    $query->where('position_id','2');
                });
            }else{
                $data->whereHas('users', function ($query) {$query->where('position_id',2)->where('user_id', request()->search['seller_id']);});
            }
        }
        if (request()->filled('search') && request()->search['consultant_id'] != 0 ){

            if (request()->search['consultant_id'] == 'none'){
                $data->whereDoesntHave('users',function ($query){
                    $query->where('position_id','1');
                });
            }else{
                $data->whereHas('users', function ($query) {$query->where('position_id',1)->where('user_id', request()->search['consultant_id']);});
            }
        }



        if (request()->filled('search') && isset(request()->search['phone']) && request()->search['phone'] ){
            $data->whereHas('customer', function ($query) {$query->where('phone','LIKE','%'.request()->search['phone'].'%');});
        }
        if (request()->filled('search') && request()->search['has_name'] != 'all' ){
            if (request()->search['has_name'] == '1'){
                $data->whereHas('customer', function ($query) {$query->whereNotNull('name');});
            }else{
                $data->whereHas('customer', function ($query) {$query->whereNull('name');});
            }
        }
        if (request()->filled('search') && request()->search['has_report'] != 'all' ){
            if (request()->search['has_report'] == 1){
                $data->whereHas('reports');
            }
            if (request()->search['has_report'] == 0){
                $data->whereDosentHas('reports');
            }
        }
        if (request()->filled('search') && request()->search['has_invoice'] != 'all' ){
            if (request()->search['has_invoice'] == 1){
                $data->whereHas('invoices');
            }
            if (request()->search['has_invoice'] == 0){
                $data->whereDosentHas('invoices');
            }
        }

        $data->orderBy(request('sort_by'),request('sort_type'));
        $data->with('tags');
        return helper_response_fetch(ProjectCustomerIndexResource::collection($data->paginate(request('per_page')))->resource);
    }

    public function customers_change_status($request, $item)
    {
        $project_customer = $item->customers()->find($request->project_customer_id);
        if ($project_customer){
            $project_customer->update(['project_customer_status_id' => $request->status_id]);
            return helper_response_updated(new ProjectCustomerIndexResource($project_customer));
        }
        return helper_response_error('Project Customer not found');
    }
    public function customers_change_level($request, $item)
    {
        $project_customer = $item->customers()->find($request->project_customer_id);
        if ($project_customer){
            $project_customer->update(['project_level_id' => $request->level_id]);
            return helper_response_updated(new ProjectCustomerIndexResource($project_customer));
        }
        return helper_response_error('Project Customer not found');
    }

    public function customers_change_target($request, $item)
    {
        $project_customer = $item->customers()->find($request->project_customer_id);
        if ($project_customer){
            if ($request->users){
                foreach ($request->users as $user){
                    $get_user = $project_customer->users()->find($user['id']);
                    $get_user->update(['target_price' => $user['price']]);
                }
            }
            return helper_response_updated(new ProjectCustomerIndexResource($project_customer));
        }
        return helper_response_error('Project Customer not found');
    }

    public function delete_customers($project, $item)
    {
        //delete user
        $item->user()->delete();
        //delete reports
        $item->reports()->delete();
        //delete invoices
        $item->invoices()->delete();
        $item->delete();
        $project->update(['total_customers' => $project->total_customers - 1]);
        return helper_response_deleted();
    }

    public function delete_multi($project, $request)
    {
        $project_customers = Project_Customer::whereIn('id',$request->ids)->get();
        foreach ($project_customers as $project_customer){
            $this->delete_customers($project,$project_customer);
        }
        return helper_response_deleted();
    }

    public function assigned_customers($item,$request)
    {
        $customers=[];
        $users =[];
        if ($request->filled('divides') && is_array($request->divides)) {
            foreach ($request->divides as $divide) {
                User_Project_Customer::updateOrcreate(['start_at' => Carbon::now(),'user_id' => $divide['user_id'],'project_customer_id' => $divide['customer_id']],['position_id' => $divide['position_id']]);
                $customers[]=$divide['customer_id'];
                if (!in_array($divide['user_id'], $users)) {
                    $users[] = $divide['user_id'];
                }
            }
            $get_customers = $item->customers()->whereIn('id',$customers)->get();
            foreach ($get_customers as $customer) {
                $customer->update(['status' => Project_Customer::STATUS_ASSIGNED]);
                if ($request->filled('type') && $request->type == 'success'){
                    $customer->update(['project_level_id' => helper_data_project_level_checkup(),'project_customer_status_id' => helper_data_customer_status_follow()]);
                    $customer->statuses()->create([
                        'customer_id' => $customer->customer_id,
                        'project_level_id' => helper_data_project_level_checkup(),
                        'customer_status_id' => helper_data_customer_status_follow(),
                        'description' => 'تخصیص کارشناس فروش بعد از وضعیت موفق'
                    ]);
                }
            }
            foreach ($users as $user_id) {
                if (!$item->users()->where('user_id',$user_id)->exists()) {
                    $item->users()->create(['user_id' => $user_id]);
                }
            }
            return helper_response_created([]);
        }
        return helper_response_error('Invalid Request');
    }

    public function assigned_customers_single($item, $request)
    {
        if ($request->filled('user_id') && $request->filled('project_customer_id')) {
            $target = null;
            $current_user = User_Project_Customer::where('position_id',$request->position_id)->where('project_customer_id',$request->project_customer_id)->first();
            User_Project_Customer::where('position_id',$request->position_id)->where('project_customer_id',$request->project_customer_id)->delete();
            if ($current_user){
                $target = $current_user->target_price;
                $current_user->delete();
            }
            User_Project_Customer::create([
                'user_id' => $request->user_id,
                'project_customer_id' => $request->project_customer_id,
                'position_id' => $request->position_id,
                'description' => $request->description,
                'target_price' => $target,
                'start_at' => Carbon::now(),
            ]);
                 //get project customer
            $data = $item->customers()->find($request->project_customer_id);
            $data->update(['status' => Project_Customer::STATUS_ASSIGNED]);
            $item->users()->updateOrCreate(['user_id' => $request->user_id,'position_id' => $request->position_id],[]);
        }

        return helper_response_fetch(new ProjectCustomerIndexResource($data));
    }

    public function assigned_customers_multi($item, $request)
    {
        if ($request->filled('items') && is_array($request->items)) {
            foreach ($request->items as $customer) {
                $current_user = User_Project_Customer::where('position_id',$request->position_id)->where('project_customer_id',$customer)->first();
                User_Project_Customer::where('position_id',$request->position_id)->where('project_customer_id',$customer)->delete();
                $target=null;
                if ($current_user){
                    $target = $current_user->target_price;
                    $current_user->delete();
                }
                User_Project_Customer::create([
                    'user_id' => $request->user_id,
                    'project_customer_id' => $customer,
                    'position_id' => $request->position_id,
                    'description' => $request->description,
                    'target_price' => $target,
                    'start_at' => Carbon::now(),
                ]);
                $data = $item->customers()->find($customer);
                $data->update(['status' => Project_Customer::STATUS_ASSIGNED]);
            }
            $item->users()->updateOrCreate(['user_id' => $request->user_id,'position_id' => $request->position_id],[]);
        }
        return helper_response_updated('');
    }

    public function reports($item)
    {
        $data = $item->reports();
        if (request()->filled('search') && request()->search['user_id']){
            $data->where('user_id',request()->search['user_id']);
        }
        $data->orderBy(request('sort_by'),request('sort_type'));
        return helper_response_fetch(ProjectReportIndexResource::collection($data->paginate(request('per_page')))->resource);
    }

    public function reports_store($item, $request)
    {
        //find customer_project
        $customer_project = Project_Customer::find($request->project_customer_id);
        if ($customer_project){
            $file_url = null;
            $file_size = null;
            $file_path = null;
            $file_name = null;
            if ($request->hasFile('file')){
                $file_name = $request->file('file')->getClientOriginalName();
                $file_size = $request->file('file')->getSize();
                $file_path = Storage::put('public/users/reports/'.$customer_project->customer_id.'/', $request->file('file'),'public');
                $file_url = Storage::url($file_path);
            }
            $date = \Illuminate\Support\Carbon::now();
            if ($request->filled('date')){
                $date = Carbon::make($request->date);
            }
            $data = Project_Customer_Report::create([
                'project_id' => $item->id,
                'project_customer_id' =>  $request->project_customer_id,
                'user_id' => $request->user_id,
                'admin_id' => auth('admins')->id(),
                'report' => $request->report,
                'file_path' => $file_path,
                'file_url' => $file_url,
                'file_size' => $file_size,
                'file_name' => $file_name,
                'created_at' => $date,
            ]);
            return helper_response_created(new ProjectReportIndexResource($data));
        }

    }

    public function reports_update($project, $report, $request)
    {
        $report->update(['report' => $request->report]);
        return helper_response_updated(new ProjectReportIndexResource($report));
    }

    public function reports_destroy($project, $report)
    {
        $report->delete();
        return helper_response_deleted();
    }

    public function invoices_update($project, $invoice, $request)
    {
        //check sum invoices amount
        if (($request->amount > $invoice->amount) && $invoice->project_customer->invoices()->sum('amount') + $request->amount > $invoice->project_customer->user->target_price ){
            return helper_response_error('مجموع مبلغ فاکتور های ثبت شده نباید بیشتر از مبلغ معامله باشد');
        }
        $invoice->update([
            'amount' => $request->amount,
            'user_id' => $request->user_id,
            'description' => $request->description,
        ]);
        $invoice->load('user');
        return helper_response_updated(new ProjectInvoiceIndexResource($invoice));

    }

    public function invoices_settle($project, $invoice)
    {
        $invoice->update(['settle' => !$invoice->settle]);
        return helper_response_updated(new ProjectInvoiceIndexResource($invoice));
    }

    public function invoices_download($project, $invoice)
    {
        if ($invoice->file_url){
           return Storage::download($invoice->file_path,$invoice->file_name);
        }
        return helper_response_error('nofile');


    }

   public function reports_download($project, $report)
   {
       if ($report->file_url){
           return Storage::download($report->file_path,$report->file_name);
        }
        return helper_response_error('nofile');
   }

    public function invoices_destroy($project, $invoice)
    {
        $user_project = User_Project::where('project_id', $project->id)->where('user_id',$invoice->user_id)->first();
        if ($user_project){
            $user_project->update(['total_price' => $user_project->total_price - $invoice->amount]);
        }
        $invoice->delete();

        return helper_response_deleted();
    }

    public function invoices($item)
    {
        $data = $item->invoices();
        if (request()->filled('search') && request()->search['user_id']){
            $data->where('user_id',request()->search['user_id']);
        }
        if (request()->filled('search') && request()->search['settle'] && request()->search['settle'] !== 'all'){
            if (request()->search['settle'] == 'yes'){
                $data->where('settle',true);
            }else{
                $data->where('settle',false);
            }
        }


        $data->orderBy(request('sort_by'),request('sort_type'));
        return helper_response_fetch(ProjectInvoiceIndexResource::collection($data->paginate(request('per_page')))->resource);
    }

    public function invoices_store($item,$request)
    {

         //find customer_project
        $customer_project = Project_Customer::find($request->project_customer_id);
        if ($customer_project){
            $file_url = null;
            $file_size = null;
            $file_path = null;
            $file_name = null;
            if ($request->hasFile('file')){
                $file_name = $request->file('file')->getClientOriginalName();
                $file_size = $request->file('file')->getSize();
                $file_path = Storage::put('public/users/invoices/'.$customer_project->customer_id.'/', $request->file('file'),'public');
                $file_url = Storage::url($file_path);
            }
            $date = \Illuminate\Support\Carbon::now();
            if ($request->filled('date')){
                $date = Carbon::make($request->date);
            }

            //find project customer user
            $user = $customer_project->users()->where('user_id',$request->user_id)->first();
            if($user){
                if($request->target_price){
                    $user->update(['target_price' => $request->target_price]);
                }

            }

            $data = Project_Customer_Invoice::create([
                'project_id' => $item->id,
                'project_customer_id' =>  $request->project_customer_id,
                'user_id' => $request->user_id,
                'admin_id' => auth('admins')->id(),
                'description' => $request->description,
                'amount' => $request->amount,
                'file_path' => $file_path,
                'file_url' => $file_url,
                'file_size' => $file_size,
                'file_name' => $file_name,
                'created_at' => $date,
            ]);
            return helper_response_created(new ProjectInvoiceIndexResource($data));
        }

    }

    public function get_latest_reports($item)
    {
        $data = $item->reports()->orderByDesc('id')->take(request('count'))->get();
        return helper_response_fetch(ProjectReportIndexResource::collection($data));

    }

    public function get_latest_invoices($item)
    {
        $data = $item->invoices()->orderByDesc('id')->take(request('count'))->get();
        return helper_response_fetch(ProjectInvoiceIndexResource::collection($data));
    }

    public function get_fields($item)
    {
        $data = $item->fields;
        return helper_response_fetch(FieldIndexResource::collection($data));
    }

    public function store_fields($item, $request)
    {
        if ($request->filled('fields')){

            $item->fields()->sync($request->fields);

        }

        return helper_response_fetch(FieldIndexResource::collection($item->fields));

    }

    public function get_positions($item)
    {
        $data = $item->positions;
        return helper_response_fetch(ProjectPositionResource::collection($data));
    }

    public function store_positions($item, $request)
    {
        if ($request->filled('positions')){
            $item->positions()->delete();
            foreach ($request->positions as $position){
                $item->positions()->create([
                    'position_id' => $position,
                ]);
            }

        }
        return helper_response_fetch(ProjectPositionResource::collection($item->positions));
    }

    public function get_levels($item)
    {
        $data = $item->levels;
        return helper_response_fetch(ProjectLevelIndexResource::collection($data));
    }

    public function store_levels($item, $request)
    {
        if ($item->levels()->where('project_level_id',$request->project_level_id)->exists()) {
            return helper_response_error('Level already exists');
        }
        $data = $item->levels()->create([
            'project_level_id' => $request->project_level_id,
            'priority' => $request->priority,
        ]);
        return helper_response_fetch(new ProjectLevelIndexResource($data));
    }

    public function update_levels($project, $item, $request)
    {
        $item->update(['priority' => $request->priority]);
        return helper_response_updated(new ProjectLevelIndexResource($item));
    }

    public function delete_levels($project, $item)
    {
        $item->delete();
        return helper_response_deleted();
    }

    //Forms

    public function get_forms($project)
    {
        $data = $project->forms;
        return helper_response_fetch(ProjectFromIndexResource::collection($data));
    }

    public function store_forms($project, $request)
    {
        $token = Str::random(2).$project->id.Str::random(2);
        $link = "https://i.tonl.ir/".$token;
        DB::beginTransaction();
        $form = $project->forms()->create([
            'name' => $request->name,
            'token' => $token,
            'link' => $link,
             'is_active' => 1,
            'description' => $request->description,
        ]);
        if ($request->filled('fields')){
            foreach ($request->fields as $field){
                $form->fields()->create([
                   'field_id' => $field['field']['id'],
                   'title' => $field['title'],
                ]);
            }
        }
        DB::commit();
        return helper_response_created(new ProjectFromIndexResource($form));
    }

    public function update_forms($project, $item, $request)
    {
        DB::beginTransaction();
        $item->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);
        if ($request->filled('fields')){
            $item->fields()->delete();
            foreach ($request->fields as $field){
                $item->fields()->create([
                    'field_id' => $field['field']['id'],
                    'title' => $field['title'],
                ]);
            }
        }
        DB::commit();
        return helper_response_created(new ProjectFromIndexResource($item));
    }

    public function destroy_forms($project,$item)
    {
        $item->delete();
        return helper_response_deleted();
    }

    public function activation_forms($project,$item)
    {
        $item->update(['is_active' => !$item->is_active]);
        return helper_response_updated([]);
    }

    public function export_customers($project)
    {
        $header = [
            'نام',
            'موبایل',
            'اینستاگرام',
        ];
        foreach ($project->fields()->orderBy('id','asc')->get() as $field){
            $header[] = $field->title;
        }

        $result=[];

        $data = $project->customers();
        $data->with('customer',function ($query){
            $query->select(['id','name','phone','instagram_id']);
        });
        $data->with('fields',function ($query){
            $query->select(['id','val','field_id']);
        });
        $data->with('fields.field',function ($query){
            $query->select(['id','title']);
        });
        foreach ($data->get() as $item){
            $fields = [];
            foreach ($item->fields as $field){
                $fields[] = $field->val;
            }

            $result[]=[
                'name'=> $item->customer->name,
                'phone'=> $item->customer->phone,
                'instagram_id'=> $item->customer->instagram_id,
                'field_one' => $fields[0] ?? '',
                'field_two' => $fields[1] ?? ''
            ];
        }

        return Excel::download(new ProjectCustomersExport($result,$header), 'users.xlsx');



    }

}
