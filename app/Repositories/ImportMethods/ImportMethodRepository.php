<?php
namespace App\Repositories\ImportMethods;

use App\Http\Resources\ImportMethods\ImportMethodShortResource;
use App\Interfaces\ImportMethods\importMethodInterface;
use App\Models\Import_Method;


class ImportMethodRepository implements importMethodInterface
{

   public function index($project)
   {
       $data = $project->import_methods();
       $data->orderBy(request('sort_by'),request('sort_type'));
       return helper_response_fetch($data->paginate(request('per_page')));
   }

    public function all($project)
    {
        $data = $project->import_methods();
        $data->orderByDesc('id');
        return helper_response_fetch(ImportMethodShortResource::collection($data->get()));
    }

   public function store($request,$project)
   {
       $data = $project->import_methods()->create([
           'name' => $request->name,
           'description' => $request->description,
       ]);
       return helper_response_fetch($data);
   }

   public function show($item,$project)
   {
       return helper_response_fetch($item);
   }

   public function update($request, $item,$project)
   {
       $item->update([
           'name' => $request->name,
           'description' => $request->description,
       ]);
       return helper_response_updated($item);
   }

   public function destroy($item,$project)
   {
       $item->delete();
       return helper_response_deleted();
   }


}
