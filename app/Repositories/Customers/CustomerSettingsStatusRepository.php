<?php
namespace App\Repositories\Customers;
use App\Http\Resources\Customers\Settings\Statuses\CustomerSettingsStatusIndexResource;
use App\Http\Resources\Customers\Settings\Statuses\CustomerSettingsStatusShortResource;
use App\Http\Resources\Customers\Settings\Statuses\CustomerSettingsStatusSingleResource;
use App\Interfaces\Customers\CustomerSettingsStatusInterface;
use App\Models\Project_Customer_Status;


class CustomerSettingsStatusRepository implements CustomerSettingsStatusInterface
{

   public function index()
   {
       $data = Project_Customer_Status::query();
       $data->orderBy(request('sort_by'),request('sort_type'));
       $data->withCount('customers');
       return helper_response_fetch(CustomerSettingsStatusIndexResource::collection($data->paginate(request('per_page')))->resource);
   }

    public function all()
    {
        $data = Project_Customer_Status::query();
        $data->orderByDesc('id');
        return helper_response_fetch(CustomerSettingsStatusShortResource::collection($data->get()));
    }

   public function store($request)
   {
       $data = Project_Customer_Status::create([
           'name' => $request->name,
           'color' => $request->color,
           'description' => $request->description,

       ]);
       return helper_response_fetch(new CustomerSettingsStatusSingleResource($data));
   }

   public function show($item)
   {
       return helper_response_fetch(new CustomerSettingsStatusSingleResource($item));
   }

   public function update($request, $item)
   {
       $item->update([
           'name' => $request->name,
           'color' => $request->color,
           'description' => $request->description,
       ]);
       return helper_response_updated(new CustomerSettingsStatusSingleResource($item));
   }

   public function destroy($item)
   {
       $item->delete();
       return helper_response_deleted();
   }


}
