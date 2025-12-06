<?php
namespace App\Repositories\Projects;

use App\Exports\ProjectCustomersExport;
use App\Http\Resources\Fields\FieldIndexResource;
use App\Http\Resources\Positions\PositionsShortResource;
use App\Http\Resources\Projects\Forms\ProjectFromIndexResource;
use App\Http\Resources\Projects\Invoices\ProjectInvoiceIndexResource;
use App\Http\Resources\Projects\levels\ProjectLevelIndexResource;
use App\Http\Resources\Projects\Projects\ProjectCustomerClientsResource;
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
use App\Models\Position;
use App\Models\Project;
use App\Models\Project_Customer;
use App\Models\Project_Customer_Invoice;
use App\Models\Project_Customer_Report;
use App\Models\Project_Customer_Status;
use App\Models\Project_Level;
use App\Models\Projects\Invoice_Product;
use App\Models\User;
use App\Models\User_Project;
use App\Models\User_Project_Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Morilog\Jalali\Jalalian;
use App\Traits\SearchingTrait;


class ProjectRepository implements ProjectInterface
{
   use SearchingTrait;
   public function index()
   {
       $data = Project::query();
       $data->where('is_active', true);

       $data->orderBy(request('sort_by'),request('sort_type'));
       $data->withCount('users');
       return helper_response_fetch(ProjectIndexResource::collection($data->paginate(request('per_page')))->resource);
   }

   public function inactive()
   {
       $data = Project::query();
       $data->where('is_active', false);
       $data->orderBy(request('sort_by'), request('sort_type'));
       $data->withCount('users');
       return helper_response_fetch(ProjectIndexResource::collection($data->paginate(request('per_page')))->resource);
   }
   public function activation($item)
   {
       $item->update(['is_active' => !$item->is_active]);
       return helper_response_updated(new ProjectIndexResource($item));
   }

   public function summery($item)
   {
    //get project summery data
    $numbers = $item->customers()->count();
    $referrals = $item->customers()->where('status', Project_Customer::STATUS_ASSIGNED)->count();
    $customers = $item->customers()->whereHas('invoices')->count();
    $amounts = $item->invoices()->sum('amount');
    $target = $item->customers()->sum('target_price');
    $convert_rate = 0;
    if($referrals > 0){
        $convert_rate = round(($customers / $referrals) * 100,2);
    }

    //get users data
    $users_ids = [];
    $user_data = [];
    foreach($item->users as $user){
        if(!in_array($user->user_id,$users_ids)){
            //get sum invoices amount by user
            $invoices_amount = $item->invoices()->where('user_id',$user->user_id)->sum('amount');
            //get sum target price by user
            $target_price = $item->customers()->whereHas('users',function ($query)use($user){
                $query->where('user_id',$user->user_id);
            })->sum('target_price');
            $user_data[] = [
                'user' => [
                    'id' => $user->user_id,
                    'name' => $user->user->name,
                    'phone' => $user->user->phone,
                ],
                'invoices_amount' => $invoices_amount,
                'target_price' => $target_price,
            ];
            $users_ids[] = $user->user_id;
        }
    }

    //get import methods data
    $import_methods_data = [];
    foreach($item->import_methods as $import_method){
        $get = $item->customers()->where('import_method_id',$import_method->id)->count();
        if($get > 0){
            $import_methods_data[] = [
                'id' => $import_method->id,
                'name' => $import_method->name,
                'count' => $get,
            ];
        }
    }
    $no_import_methods = $item->customers()->whereNull('import_method_id')->count();
    if($no_import_methods > 0){
        $import_methods_data[] = [
            'id' => null,
            'name' => 'بدون منبع',
            'count' => $no_import_methods,
        ];
    }

    //get levels info
    $levels_data = [];
    foreach($item->levels as $level){
        $levels_data[] = [
            'id' => $level->id,
            'name' => $level->name,
            'color' => $level->color,
            'priority' => $level->priority,
            'count' => $item->customers()->where('project_level_id',$level->id)->count(),
        ];
    }
    $levels_data[] = [
        'id' => null,
        'name' => 'بدون وضعیت',
        'color' => '#212121',
        'priority' => 0,
        'count' => $item->customers()->whereNull('project_level_id')->count(),
    ];

    // مرتب‌سازی آرایه بر اساس priority (از کوچک به بزرگ)
    // priority = 0 (بدون وضعیت) باید همیشه در خانه اول باشد
    usort($levels_data, function($a, $b) {
        // اگر یکی priority = 0 باشد، آن را اول قرار بده
        if ($a['priority'] == 0 && $b['priority'] != 0) {
            return -1;
        }
        if ($a['priority'] != 0 && $b['priority'] == 0) {
            return 1;
        }
        // در غیر این صورت بر اساس priority مرتب کن (از کوچک به بزرگ)
        return $a['priority'] <=> $b['priority'];
    });

    //get invoices data by days
    $invoice_start_date = $item->invoices()->min('created_at');
    $invoice_end_date = $item->invoices()->max('created_at');
    $invoices_data = [];
    if ($invoice_start_date && $invoice_end_date) {
        $invoice_start_date = Carbon::parse($invoice_start_date);
        $invoice_end_date = Carbon::parse($invoice_end_date);
        while($invoice_start_date->lte($invoice_end_date)){
            $invoices_data[] = [
                'date' => $invoice_start_date->format('Y-m-d'),
                'amount' => $item->invoices()->whereDate('created_at',$invoice_start_date)->sum('amount'),
            ];
            $invoice_start_date->addDay();
        }
    }





    $result = [
        'user_data' => $user_data,
        'numbers' => $numbers,
        'referrals' => $referrals,
        'customers' => $customers,
        'amounts' => $amounts,
        'target' => $target,
        'convert_rate' => $convert_rate,
        'import_methods_data' => $import_methods_data,
        'levels_data' => $levels_data,
        'invoices_data' => $invoices_data,
    ];





    return helper_response_fetch($result);

   }

   public function all()
   {
       $data = Project::query();
       $data->orderBy(request('sort_by'),request('sort_type'));
       return helper_response_fetch(ProjectShortResource::collection($data->get()));
   }
   public function customers_client_index($project)
   {
       $data = $project->customers();
       $data->whereHas('invoices');
       $this->advance_search($data);
       $data->orderByRaw('(SELECT MAX(created_at) FROM project_customer_invoices WHERE project_customer_invoices.project_customer_id = project_customers.id) DESC');
       return helper_response_fetch(ProjectCustomerClientsResource::collection($data->paginate(request('per_page')))->resource);
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
       // activity log
       helper_activity_create(null,null,$data->id,null,'ایجاد پروژه','ایجاد پروژه '.$data->name);
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
       // activity log
       helper_activity_create(null,null,$item->id,null,'ویرایش پروژه',"ویرایش پروژه ".$item->name);
       return helper_response_updated(new ProjectSingleResource($item));
   }

   public function update_logo($request,$item)
   {
        if($request->hasFile('logo')){
            $path = Storage::put('public/projects/logo',$request->file('logo'));
            $url = Storage::url($path);
            $item->update(['image' => $url,'image_path' => $path]);
            return helper_response_fetch(['logo' => $url]);
        }else{
            Storage::delete($item->image_path);
            $item->update(['image' => null,'image_path' => null]);
            return helper_response_fetch(['logo' => null]);
        }
   }



   public function destroy($item)
   {
       $item->delete();
       // activity log
       helper_activity_create(null,null,$item->id,null,'حذف پروژه','حذف پروژه '.$item->name);
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
       // activity log
       helper_activity_create(null,null,$item->id,null,'تغییر فعالیت پروژه',"تغییر فعالیت پروژه ".$item->name);
       return helper_response_updated([]);
   }

    public function add_customers($request,$item)
    {

        $exists_projects=[];
        $counter=0;
        //check the Excel file
        if ($request->hasFile('excel') ) {
            $excel = Excel::toArray([], request()->file('excel'));
            $fileds=[];
            //get fields
            foreach(json_decode($request->fields) as $key => $value){
                $fileds[$value] = $key;
            }
            $has_header = $request->has_header;
            foreach($excel[0] as $key => $value){
                if($has_header == 'false'){
                    $phone  =  $value[$fileds['phone']];
                    if($phone){
                        // Format mobile number to standard format (09124435544)
                        $phone = helper_core_format_mobile_number($phone);
                        $customer = Customer::where('phone', $phone)->first();
                        $find_customer = null;
                        if($customer){
                            $find_customer = $item->customers()->where('customer_id',$customer->id)->first();
                        }
                        if($customer && $find_customer) {
                            $exists_projects[] = [
                                'phone' => $customer->phone,
                                'created_at' => $find_customer->created_at,
                                'users' => UserProjectCustomerResource::collection($find_customer->users),
                            ];
                            $field_data = [];
                            foreach($fileds as $customer_key => $customer_value){
                                if(str_starts_with($customer_key,'fields_')){
                                    $field_id = str_replace('fields_','',$customer_key);
                                    $field_val = $value[$customer_value];
                                    $field_data[] = [
                                        'id' => $field_id,
                                        'val' => $field_val,
                                    ];
                                }
                            }
                            if(count($field_data) > 0){
                                $find_customer->fields()->delete();
                                foreach($field_data as $field){
                                    $find_customer->fields()->create([
                                        'field_id' => $field['id'],
                                        'val' => $field['val'],
                                    ]);
                                }
                            }
                        }else{
                            $field_data = [];
                            $custoemr_data=[];
                            $import_date = Carbon::now();
                            foreach($fileds as $customer_key => $customer_value){
                                if($customer_key != 'date'){
                                    // Use formatted phone number for phone field
                                    if($customer_key == 'phone'){
                                        $custoemr_data[$customer_key] = $phone;
                                    }elseif(!str_starts_with($customer_key,'fields_')){
                                        $custoemr_data[$customer_key] = $value[$customer_value];
                                    }else{
                                        $field_id = str_replace('fields_','',$customer_key);
                                        $field_val = $value[$customer_value];
                                        $field_data[] = [
                                            'id' => $field_id,
                                            'val' => $field_val,
                                        ];
                                    }
                                }
                                if($customer_key == 'date'){
                                    $excel_date = $value[$customer_value];
                                    // Convert Excel date to Carbon format
                                    $import_date = $this->convertExcelDateToCarbon($excel_date);
                                }
                            }
                            if (!$customer){
                                $customer = Customer::create($custoemr_data);
                            }
                            $new_customer = $item->customers()->create([
                                'customer_id' => $customer->id,
                                'import_method_id' => $request->import_method_id,
                                'import_at' => $import_date,
                                'status' => Project_Customer::STATUS_PENDING,
                            ]);
                            if ($request->filled('tags')){
                                $new_customer->tags()->attach($request->tags);
                            }
                            if(count($field_data) > 0){
                                foreach($field_data as $field){
                                    $new_customer->fields()->create([
                                        'field_id' => $field['id'],
                                        'val' => $field['val'],
                                    ]);
                                }
                            }
                            //check customer on other projects
                            if($customer){
                                $other_project = Project_Customer::where('customer_id',$customer->id)->where('project_id','!=',$item->id)->first();
                                if($other_project && $other_project->user_id && $other_project->invoices()->count()){
                                    //get user
                                    if($other_project->user()->whereHas('user',function ($user_quesry){$user_quesry->where('is_active',true);})->where('position_id',helper_data_position_seller())->exists() && $item->positions()->where('position_id',helper_data_position_seller())->exists()){
                                        $new_customer->user()->create([
                                            'user_id' => $other_project->user_id,
                                            'position_id' => helper_data_position_seller(),
                                            'start_at' => Carbon::now(),
                                        ]);
                                        $message = "تعداد ".'1'." شماره از پروژه : ".$item->name." به عنوان : ".' فروش'." به شما تخصیص داده شد";
                                        $user = User::find($other_project->user_id);
                                        helper_bot_send_markdown($user->telegram_id,topic_id: $message);
                                    }elseif($other_project->user()->whereHas('user',function ($user_quesry){$user_quesry->where('is_active',true);})->where('position_id',helper_data_position_consultant())->exists() && $item->positions()->where('position_id',helper_data_position_consultant())->exists()){
                                        $new_customer->user()->create([
                                            'user_id' => $other_project->user_id,
                                            'position_id' => helper_data_position_consultant(),
                                            'start_at' => Carbon::now(),
                                        ]);
                                        $message = "تعداد ".'1'." شماره از پروژه : ".$item->name." به عنوان : ".' مشاور'." به شما تخصیص داده شد";
                                        $user = User::find($other_project->user_id);
                                        helper_bot_send_markdown($user->telegram_id,$message);
                                    }
                                }
                            }

                            $counter++;
                            }
                        }
                    }
                $has_header = 'false';
        }








            // $import_date = null;
            // foreach ($excel[0] as $key => $value) {
            //     if ($value['0']){
            //         if (mb_substr($value['0'], 0, 1, 'UTF-8') != '0'){
            //             $value['0'] = '0'.$value['0'];
            //         }
            //     }
            //     $customer = Customer::where('phone',$value[0])->first();
            //     $find_customer = null;
            //     if($customer){
            //         $find_customer = $item->customers()->where('customer_id',$customer->id)->first();
            //     }
            //     if ($customer && $find_customer) {
            //         $exists_projects[] = [
            //             'phone' => $value[0],
            //             'created_at' => $find_customer->created_at,
            //             'users' => UserProjectCustomerResource::collection($find_customer->users),
            //         ];

            //     }else{
            //         if (!$customer){
            //             $customer = Customer::create([
            //                 'phone' => $value[0],
            //                 'name' => $value[1],
            //                 'instagram_id' => $value[2],
            //             ]);
            //         }
            //         if ($value[3]){
            //             $jalali = Jalalian::fromFormat('Y/m/d', $value[3]);
            //             $import_date = $jalali->toCarbon();
            //         }

            //         $new_customer = $item->customers()->create([
            //             'customer_id' => $customer->id,
            //             'import_method_id' => $request->import_method_id,
            //             'import_at' => $import_date,
            //             'created_at' => $import_date,
            //             'description' => $request->description,
            //             'status' => Project_Customer::STATUS_PENDING,
            //         ]);
            //         if ($request->filled('tags')){
            //             $new_customer->tags()->attach($request->tags);
            //         }
            //         $counter++;
            //     }
            // }

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
                        //check customer on other projects
                        if($customer){
                            $other_project = Project_Customer::where('customer_id',$customer->id)->where('project_id','!=',$item->id)->first();
                            if($other_project && $other_project->invoices()->count()){
                                // return $other_project;
                                //get user
                                $seller_user = $other_project->users()->whereHas('user',function ($user_quesry){$user_quesry->where('is_active',true);})->where('position_id',helper_data_position_seller())->first();
                                $consultant_user = $other_project->users()->whereHas('user',function ($user_quesry){$user_quesry->where('is_active',true);})->where('position_id',helper_data_position_consultant())->first();
                                if($seller_user && $item->positions()->where('position_id',helper_data_position_seller())->exists()){
                                    $new_customer->users()->create([
                                        'user_id' => $seller_user->user_id,
                                        'position_id' => helper_data_position_seller(),
                                        'start_at' => Carbon::now(),
                                    ]);
                                    $message = "تعداد ".'1'." شماره از پروژه : ".$item->name." به عنوان : ".' فروش'." به شما تخصیص داده شد";
                                    $user = User::find($seller_user->user_id);
                                    helper_bot_send_markdown($user->telegram_id,$message);
                                }elseif($consultant_user && $item->positions()->where('position_id',helper_data_position_consultant())->exists()){
                                    $new_customer->users()->create([
                                        'user_id' => $consultant_user->user_id,
                                        'position_id' => helper_data_position_consultant(),
                                        'start_at' => Carbon::now(),
                                    ]);
                                    $message = "تعداد ".'1'." شماره از پروژه : ".$item->name." به عنوان : ".' مشاور'." به شما تخصیص داده شد";
                                    $user = User::find($consultant_user->user_id);
                                    helper_bot_send_markdown($user->telegram_id,$message);
                                }
                            }
                        }



                        $counter++;
                    }
                }
            }
        }
        $item->update(['total_customers' => $counter + $item->total_customers]);
        // activity log
        helper_activity_create(null,null,$item->id,null,'افزودن مشتری',': افزودن مشتری '.$counter.' مشتری');
        return helper_response_created($exists_projects);
    }

    public function get_customers($item)
    {
        $data = $item->customers();
        $this->advance_search($data);
        $data->orderBy(request('sort_by'),request('sort_type'));
        $data->with('tags');
        return helper_response_fetch(ProjectCustomerIndexResource::collection($data->paginate(request('per_page')))->resource);
    }

    public function customers_change_status($request, $item)
    {
        $project_customer = $item->customers()->find($request->project_customer_id);
        if ($project_customer){
            $project_customer->update(['project_customer_status_id' => $request->status_id]);
            // activity log
            $status = Project_Customer_Status::find($request->status_id)->name;
            helper_activity_create(null,null,$item->id,$project_customer->customer_id,'تغییر وضعیت مشتری',"تغییر وضعیت مشتری "." به ".$status);

            return helper_response_updated(new ProjectCustomerIndexResource($project_customer));
        }
        return helper_response_error('Project Customer not found');
    }
    public function customers_change_level($request, $item)
    {
        $project_customer = $item->customers()->find($request->project_customer_id);
        if ($project_customer){
            $project_customer->update(['project_level_id' => $request->level_id]);
            // activity log
            $level = Project_Level::find($request->level_id)->name;
            helper_activity_create(null,null,$item->id,$project_customer->customer_id,'تغییر مرحله مشتری',"تغییر مرحله مشتری "." به ".$level);
            return helper_response_updated(new ProjectCustomerIndexResource($project_customer));
        }
        return helper_response_error('Project Customer not found');
    }

    public function customers_change_target($request, $item)
    {
        $project_customer = $item->customers()->find($request->project_customer_id);
        if ($project_customer){
            $project_customer->update(['target_price' => $request->target_price]);
            // activity log
            helper_activity_create(null,null,$item->id,$project_customer->customer_id,'تغییر مبلغ معامله',"تغییر مبلغ معامله "." به ".$request->target_price);
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
        // activity log
        helper_activity_create(null,null,$project->id,null,'حذف مشتری',' : حذف مشتری '.$item->customer->name);
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
            $counter=[];
            foreach ($request->divides as $divide) {
                User_Project_Customer::updateOrcreate(['start_at' => Carbon::now(),'user_id' => $divide['user_id'],'project_customer_id' => $divide['customer_id']],['position_id' => $divide['position_id']]);
                // activity log
                $position = Position::find($divide['position_id'])->name;
                $counter[$divide['user_id']] = ($counter[$divide['user_id']] ?? 0) + 1;
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
                $message = "تعداد ".$counter[$user_id]." شماره از پروژه : ".$item->name." به عنوان : ".$position." به شما تخصیص داده شد";
                $user = User::find($user_id);
                helper_bot_send_markdown($user->telegram_id,null,$message);
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
            // activity log
            $position = Position::find($request->position_id)->name;
                 //get project customer
            $data = $item->customers()->find($request->project_customer_id);
            $data->update(['status' => Project_Customer::STATUS_ASSIGNED]);
            $item->users()->updateOrCreate(['user_id' => $request->user_id,'position_id' => $request->position_id],[]);
            $message = "تعداد ۱ شماره از پروژه : ".$item->name." به عنوان : ".$position." به شما تخصیص داده شد";
            $user = User::find($request->user_id);
            helper_bot_send_markdown($user->telegram_id,null,$message);
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
        $this->advance_search($data);
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
            // activity log
            helper_activity_create(null,$request->user_id,$item->id,$customer_project->customer_id,'ثبت گزارش'," : ثبت گزارش ".$request->report."");
            return helper_response_created(new ProjectReportIndexResource($data));
        }

    }

    public function reports_update($project, $report, $request)
    {
        $report->update(['report' => $request->report]);
        // activity log
        helper_activity_create(null,$report->user_id,$project->id,$report->project_customer->customer_id,'ویرایش گزارش'," : ویرایش گزارش ".$request->report."");
        return helper_response_updated(new ProjectReportIndexResource($report));
    }

    public function reports_destroy($project, $report)
    {
        // activity log
        helper_activity_create(null,$report->user_id,$project->id,$report->project_customer->customer_id,'حذف گزارش'," : حذف گزارش ".$report->report."");
        $report->delete();
        return helper_response_deleted();
    }

    public function invoices_update($project, $invoice, $request)
    {
        //check sum invoices amount
        if (($request->amount > $invoice->amount) && $invoice->project_customer->invoices()->where('id','!=',$invoice->id)->sum('amount') + $request->amount > $invoice->project_customer->target_price ){
            return helper_response_error('مجموع مبلغ فاکتور های ثبت شده نباید بیشتر از مبلغ معامله باشد');
        }
        $invoice->update([
            'amount' => $request->amount,
            'user_id' => $request->user_id,
            'description' => $request->description,
            'created_at' => $request->created_at,
        ]);

        // Remove all existing invoice products
        $invoice->invoice_products()->delete();

        // Handle products if provided
        if ($request->filled('products') && is_array($request->products)) {
            foreach ($request->products as $product_id) {
                $invoice->invoice_products()->create([
                    'project_product_id' => $product_id,
                ]);
            }
        }

        $invoice->load('user');
        // activity log
        helper_activity_create(null,$invoice->user_id,$project->id,$invoice->project_customer->customer_id,'ویرایش فاکتور',"# : ویرایش فاکتور ".$invoice->id."");
        return helper_response_updated(new ProjectInvoiceIndexResource($invoice));

    }

    public function invoices_settle($project, $invoice)
    {
        $invoice->update(['settle' => !$invoice->settle]);
        // activity log
        helper_activity_create(null,$invoice->user_id,$project->id,$invoice->project_customer->customer_id,'تسویه فاکتور',"# : تسویه فاکتور ".$invoice->id."");
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
        // activity log
        helper_activity_create(null,$invoice->user_id,$project->id,$invoice->project_customer->customer_id,'حذف فاکتور',"# : حذف فاکتور ".$invoice->id."");
        $invoice->delete();

        return helper_response_deleted();
    }

    public function invoices($item)
    {
        $data = $item->invoices();
        $this->advance_search($data);
        $data->orderBy(request('sort_by'),request('sort_type'));
        $all = $data;
        $all->get();
        $total_amount = $all->sum('amount');

        $result = [
            'items' => ProjectInvoiceIndexResource::collection($data->paginate(request('per_page')))->resource,
            'total_amount' => $total_amount,
        ];
        return helper_response_fetch($result);
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

            // Handle products if provided
            if ($request->filled('products') && is_array($request->products)) {
                foreach ($request->products as $product_id) {
                    $data->invoice_products()->create([
                        'project_product_id' => $product_id,
                    ]);
                }
            }

            // Check if total invoice amount >= target_price and update selled
            $total_invoice_amount = $customer_project->invoices()->sum('amount');
            if ($total_invoice_amount >= $customer_project->target_price) {
                $customer_project->update(['selled' => true]);
            }

            // activity log
            helper_activity_create(null,$request->user_id,$item->id,$customer_project->customer_id,'ثبت فاکتور',"# : ثبت فاکتور ".$data->id."");
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
            'import_method_id' => $request->import_method_id,
            'tag_id' => $request->tag_id,
            'token' => $token,
            'link' => $link,
             'is_active' => 1,
            'description' => $request->description,
            'theme_name' => $request->theme_name,
            'theme_color' => $request->theme_color,
        ]);
        if ($request->filled('fields')){
            foreach ($request->fields as $field){
                $form->fields()->create([
                   'field_id' => $field['field_id'],
                   'title' => $field['title'],
                   'required' => $field['required'],
                   'priority' => $field['priority'],
                ]);
            }
        }
        DB::commit();
        // activity log
        helper_activity_create(null,null,$project->id,null,'ایجاد فرم'," : ایجاد فرم ".$form->name."");
        return helper_response_created(new ProjectFromIndexResource($form));
    }

    public function update_forms($project, $item, $request)
    {
        DB::beginTransaction();
        $item->update([
            'name' => $request->name,
            'import_method_id' => $request->import_method_id,
            'tag_id' => $request->tag_id,
            'description' => $request->description,
            'theme_name' => $request->theme_name,
            'theme_color' => $request->theme_color,
        ]);
        if ($request->filled('fields')){
            $item->fields()->delete();
            foreach ($request->fields as $field){
                $item->fields()->create([
                    'field_id' => $field['field_id'],
                    'title' => $field['title'],
                    'required' => $field['required'],
                    'priority' => $field['priority'],
                ]);
            }
        }
        DB::commit();
        // activity log
        helper_activity_create(null,null,$project->id,null,'ویرایش فرم'," : ویرایش فرم ".$item->name."");
        return helper_response_created(new ProjectFromIndexResource($item));
    }

    public function destroy_forms($project,$item)
    {
        // activity log
        helper_activity_create(null,null,$project->id,null,'حذف فرم'," : حذف فرم ".$item->name."");
        $item->delete();
        return helper_response_deleted();
    }

    public function activation_forms($project,$item)
    {
        $item->update(['is_active' => !$item->is_active]);
        // activity log
        helper_activity_create(null,null,$project->id,null,'فعالسازی فرم'," : ".($item->is_active ? 'فعالسازی' : 'غیرفعالسازی')." فرم ".$item->name."");
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
    public function get_columns($project)
    {
        return helper_response_fetch(Project_Customer::columns($project));
    }

    public function customers_client_columns($project)
    {
        return helper_response_fetch(Project_Customer::columns($project));
    }

    public function invoices_columns($project)
    {
        return helper_response_fetch(Project_Customer_Invoice::columns($project));
    }

    public function reports_columns($project)
    {
        return helper_response_fetch(Project_Customer_Report::columns($project));
    }

    public function get_customer_fields($project)
    {
        $data = [
            [
                'value' => 'phone',
                'title' => 'موبایل',
            ],
            [
                'value' => 'name',
                'title' => 'نام',
            ],
            [
                'value' => 'email',
                'title' => 'ایمیل',
            ],
            [
                'value' => 'instagram_id',
                'title' => 'اینستاگرام',
            ],
            [
                'value' => 'telegram_id',
                'title' => 'تلگرام',
            ],
            [
                'value' => 'description',
                'title' => 'توضیحات',
            ],
            [
                'value' => 'date',
                'title' => 'تاریخ',
            ],
        ];
        foreach ($project->fields()->orderBy('id','asc')->get() as $field){
            $data[] = [
                'value' => 'fields_'.$field->id,
                'title' => $field->title,
            ];
        }
        return helper_response_fetch($data);
    }

    /**
     * Convert Excel date to Carbon format
     * Detects if date is Jalali or Gregorian and converts accordingly
     *
     * @param mixed $excel_date
     * @return Carbon|null
     */
    private function convertExcelDateToCarbon($excel_date)
    {
        if (empty($excel_date)) {
            return null;
        }

        // Convert to string if it's not already
        $date_string = (string) $excel_date;

        // Remove any extra whitespace
        $date_string = trim($date_string);

        // Extract only the date part (remove time if exists)
        // Handle formats like: 1404/08/24 11:49:43 or 2024/01/15 10:30:00
        if (preg_match('/^(\d{4}[\/\-\.]\d{1,2}[\/\-\.]\d{1,2})/', $date_string, $matches)) {
            $date_string = $matches[1];
        }

        // Common Jalali date patterns (Persian calendar)
        $jalali_patterns = [
            '/^\d{4}\/\d{1,2}\/\d{1,2}$/',  // 1403/01/15
            '/^\d{4}-\d{1,2}-\d{1,2}$/',    // 1403-01-15
            '/^\d{4}\.\d{1,2}\.\d{1,2}$/',  // 1403.01.15
        ];

        // Common Gregorian date patterns
        $gregorian_patterns = [
            '/^\d{4}\/\d{1,2}\/\d{1,2}$/',  // 2024/01/15
            '/^\d{4}-\d{1,2}-\d{1,2}$/',    // 2024-01-15
            '/^\d{4}\.\d{1,2}\.\d{1,2}$/',  // 2024.01.15
        ];

        // Check if it's a Jalali date (year > 1300 and < 1500 typically)
        $is_jalali = false;
        foreach ($jalali_patterns as $pattern) {
            if (preg_match($pattern, $date_string)) {
                $parts = preg_split('/[\/\-\.]/', $date_string);
                if (count($parts) >= 3) {
                    $year = (int) $parts[0];
                    // Jalali years are typically between 1300-1500
                    if ($year >= 1300 && $year <= 1500) {
                        $is_jalali = true;
                        break;
                    }
                }
            }
        }

        try {
            if ($is_jalali) {
                // Convert Jalali date to Carbon
                // Try different separators
                $separators = ['/', '-', '.'];
                foreach ($separators as $sep) {
                    if (strpos($date_string, $sep) !== false) {
                        $jalali = Jalalian::fromFormat('Y' . $sep . 'm' . $sep . 'd', $date_string);
                        return $jalali->toCarbon();
                    }
                }
            } else {
                // Try to parse as Gregorian date
                // Handle different formats
                $formats = ['Y/m/d', 'Y-m-d', 'Y.m.d', 'd/m/Y', 'd-m-Y', 'd.m.Y'];

                foreach ($formats as $format) {
                    try {
                        return Carbon::createFromFormat($format, $date_string);
                    } catch (\Exception $e) {
                        continue;
                    }
                }

                // If all formats fail, try Carbon's default parsing
                return Carbon::parse($date_string);
            }
        } catch (\Exception $e) {
            // If conversion fails, return null
            return null;
        }

        return null;
    }

}
