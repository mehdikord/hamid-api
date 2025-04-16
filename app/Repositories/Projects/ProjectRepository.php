<?php
namespace App\Repositories\Projects;

use App\Http\Resources\Projects\Invoices\ProjectInvoiceIndexResource;
use App\Http\Resources\Projects\Projects\ProjectCustomerIndexResource;
use App\Http\Resources\Projects\Projects\ProjectIndexResource;
use App\Http\Resources\Projects\Projects\ProjectSingleResource;
use App\Http\Resources\Projects\Reports\ProjectReportIndexResource;
use App\Interfaces\Projects\ProjectInterface;
use App\Models\Customer;
use App\Models\Project;
use App\Models\Project_Customer;
use App\Models\User_Project_Customer;
use Carbon\Carbon;
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
                $customer = Customer::where('phone',$value[1])->first();
                if ($customer && $item->customers()->where('customer_id',$customer->id)->exists()) {
                    $exists_projects[] = $value[1];
                }else{
                    if (!$customer){
                        $customer = Customer::create([
                            'phone' => $value[1],
                            'instagram_id' => $value[2],
                        ]);
                    }
                    if ($value[3]){
                        $jalali = Jalalian::fromFormat('Y/m/d', $value[3]);
                        $import_date = $jalali->toCarbon();
                    }
                    $item->customers()->create([
                        'customer_id' => $customer->id,
                        'import_at' => $import_date,
                        'description' => $request->description,
                        'status' => Project_Customer::STATUS_PENDING,
                    ]);
                    $counter++;
                }
            }

        }
        //check numbers
        if ($request->filled('numbers')){
            $numbers = explode(',',$request->numbers);
            if (is_array($numbers) && count($numbers)){
                foreach ($numbers as $number){
                    $customer = Customer::where('phone',$number)->first();
                    if ($customer && $item->customers()->where('customer_id',$customer->id)->exists()) {
                        $exists_projects[] = $number;
                    }else{
                        if (!$customer){
                            $customer = Customer::create([
                                'phone' => $number,
                            ]);
                        }
                        $item->customers()->create([
                            'customer_id' => $customer->id,
                            'import_at' => Carbon::now(),
                            'description' => $request->description,
                            'status' => Project_Customer::STATUS_PENDING,
                        ]);
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
        $data->orderBy(request('sort_by'),request('sort_type'));
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

    public function assigned_customers($item,$request)
    {
        if ($request->filled('divides') && is_array($request->divides)) {
            foreach ($request->divides as $divide) {
               $customers = $item->customers()->where('status',Project_Customer::STATUS_PENDING)->take($divide['amount'])->get();
               $ids=[];
               foreach ($customers as $customer) {
                   User_Project_Customer::create([
                       'user_id' => $divide['user_id'],
                       'project_customer_id' => $customer->id,
                       'description' => $request->description,
                       'start_at' => Carbon::now(),
                   ]);
                   $ids[] = $customer->id;
               }
               Project_Customer::whereIn('id',$ids)->update(['status' => Project_Customer::STATUS_ASSIGNED]);
               if (count($ids)){
                   $item->users()->updateOrCreate(['user_id' => $divide['user_id']],[]);
               }

            }
            return helper_response_created([]);
        }
        return helper_response_error('Invalid Request');
    }

    public function assigned_customers_single($item, $request)
    {
        if ($request->filled('user_id') && $request->filled('project_customer_id')) {
            User_Project_Customer::create([
                'user_id' => $request->user_id,
                'project_customer_id' => $request->project_customer_id,
                'description' => $request->description,
                'start_at' => Carbon::now(),
            ]);
        }
        //get project customer
        $data = $item->customers()->find($request->project_customer_id);
        $data->update(['status' => Project_Customer::STATUS_ASSIGNED]);
        return helper_response_fetch(new ProjectCustomerIndexResource($data));
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


}
