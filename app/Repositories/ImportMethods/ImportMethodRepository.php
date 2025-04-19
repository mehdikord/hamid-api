<?php
namespace App\Repositories\ImportMethods;
use App\Http\Resources\ImportMethods\ImportMethodIndexResource;
use App\Interfaces\ImportMethods\importMethodInterface;
use App\Models\Import_Method;


class ImportMethodRepository implements importMethodInterface
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
       return helper_response_fetch(new ImportMethodIndexResource($item));
   }

   public function update($request, $item)
   {
       $item->update([
           'name' => $request->name,
           'description' => $request->description,
       ]);
       return helper_response_updated(new ImportMethodIndexResource($item));
   }

   public function destroy($item)
   {
       $item->delete();
       return helper_response_deleted();
   }


}
