<?php
namespace App\Repositories\Customers;
use App\Http\Resources\Customers\CustomerAdminIndexResource;
use App\Http\Resources\Customers\CustomerSingleResource;
use App\Http\Resources\ImportMethods\ImportMethodIndexResource;
use App\Interfaces\Customers\CustomerInterface;
use App\Models\Customer;
use App\Models\Import_Method;


class CustomerRepository implements CustomerInterface
{

   public function index()
   {
       $data = Customer::query();
       $data->orderBy(request('sort_by'),request('sort_type'));
       return helper_response_fetch(CustomerAdminIndexResource::collection($data->paginate(request('per_page')))->resource);
   }

    public function all()
    {

    }

   public function store($request)
   {
       $data = Customer::create([
           'province_id' =>  $request->province_id,
           'city_id' =>  $request->city_id,
           'name' =>  $request->name,
           'email' =>  $request->email,
           'phone' =>  $request->phone,
           'national_code' =>   $request->national_code,
           'gender' =>  $request->gender,
           'instagram_id' =>   $request->instagram_id,
           'description' =>  $request->description,
       ]);
       $data->load(['province','city']);
       return helper_response_fetch(new CustomerAdminIndexResource($data));
   }

   public function show($item)
   {

   }

   public function update($request, $item)
   {
       $item->update([
           'province_id' =>  $request->province_id,
           'city_id' =>  $request->city_id,
           'name' => $request->name,
           'email' => $request->email,
           'phone' => $request->phone,
           'national_code' => $request->national_code,
           'instagram_id' => $request->instagram_id,
           'tel' => $request->tel,
           'address' => $request->address,
           'postal_code' => $request->postal_code,
           'description' => $request->description,
       ]);

       if ($request->filled('project_id') && $request->filled('fields')){
           $customer_project = $item->projects()->where('project_id', $request->project_id)->first();
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


       return helper_response_updated(new CustomerAdminIndexResource($item));
   }

   public function destroy($item)
   {
       $item->delete();
       return helper_response_deleted();
   }

   public function projects_fields($item, $project)
   {

       $project_customer = $item->projects()->where('project_id', $project->id)->first();
       if ($project_customer){
           return helper_response_fetch($project_customer->fields);
       }
   }


}
