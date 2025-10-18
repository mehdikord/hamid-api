<?php
namespace App\Repositories\StatusMessages;

use App\Http\Resources\StatusMessages\StatusMessageIndexResource;
use App\Http\Resources\StatusMessages\StatusMessageShortResource;
use App\Interfaces\StatusMessages\StatusMessageInterface;


class StatusMessageRepository implements StatusMessageInterface
{

   public function index($project)
   {
       $data = $project->status_messages();
       $data->orderBy(request('sort_by'),request('sort_type'));
       return helper_response_fetch(StatusMessageIndexResource::collection($data->paginate(request('per_page')))->resource);
   }

    public function all($project)
    {
        $data = $project->status_messages();
        $data->orderByDesc('id');
        return helper_response_fetch(StatusMessageShortResource::collection($data->get()));
    }

   public function store($request,$project)
   {
       $data = $project->status_messages()->create([
           'name' => $request->name,
           'description' => $request->description,
       ]);

       if ($request->filled('options')) {
           foreach ($request->options as $option) {
               if ($option) {
                   $data->options()->create([
                       'option' => $option,
                   ]);
               }
           }
       }

       return helper_response_fetch(new StatusMessageIndexResource($data));
   }

   public function show($item,$project)
   {
       return helper_response_fetch(new StatusMessageIndexResource($item));
   }

   public function update($request, $item,$project)
   {
       $item->update([
           'name' => $request->name,
           'description' => $request->description,
       ]);
       if ($request->filled('options')) {
           $item->options()->delete();
           foreach ($request->options as $option) {
               if ($option) {
                   $item->options()->create([
                       'option' => $option,
                   ]);
               }
           }
       }
       return helper_response_updated(new StatusMessageIndexResource($item));
   }

   public function destroy($item,$project)
   {
       $item->options()->delete();
       $item->delete();
       return helper_response_deleted();
   }


}
