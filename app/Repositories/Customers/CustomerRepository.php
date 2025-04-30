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
           'postal_code' => $request->postal_code,
           'description' => $request->description,
       ]);

       return helper_response_updated(new CustomerSingleResource($item));
   }

   public function destroy($item)
   {
       $item->delete();
       return helper_response_deleted();
   }


}
