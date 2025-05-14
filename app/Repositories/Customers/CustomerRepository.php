<?php
namespace App\Repositories\Customers;
use App\Http\Resources\Customers\CustomerSingleResource;
use App\Http\Resources\ImportMethods\ImportMethodIndexResource;
use App\Interfaces\Customers\CustomerInterface;
use App\Models\Import_Method;


class CustomerRepository implements CustomerInterface
{

   public function index()
   {
       $data = Import_Method::query();
       $data->orderBy(request('sort_by'),request('sort_type'));
       return helper_response_fetch(ImportMethodIndexResource::collection($data->paginate(request('per_page')))->resource);
   }

    public function all()
    {
        $data = Import_Method::query();
        $data->orderByDesc('id');
        return helper_response_fetch(ImportMethodIndexResource::collection($data->get()));
    }

   public function store($request)
   {
       $data = Import_Method::create([
           'name' => $request->name,
           'description' => $request->description,

       ]);
       return helper_response_fetch(new ImportMethodIndexResource($data));
   }

   public function show($item)
   {
       return helper_response_fetch(new CustomerSingleResource($item));
   }

   public function update($request, $item)
   {
       $item->update([
           'name' => $request->name,
           'email' => $request->email,
           'phone' => $request->phone,
           'national_code' => $request->national_code,
           'instagram_id' => $request->instagram_id,
           'tel' => $request->tel,
           'address' => $request->address,
           'job' => $request->job,
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


       return helper_response_updated(new CustomerSingleResource($item));
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
