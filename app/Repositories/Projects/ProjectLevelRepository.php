<?php
namespace App\Repositories\Projects;

use App\Http\Resources\Projects\levels\ProjectLevelIndexResource;
use App\Interfaces\Projects\ProjectLevelInterface;

class ProjectLevelRepository implements ProjectLevelInterface
{

   public function index($project)
   {

       $data = $project->levels();
       $data->orderBy(request('sort_by'),request('sort_type'));

       return helper_response_fetch(ProjectLevelIndexResource::collection($data->paginate(request('per_page')))->resource);
   }

    public function all($project)
    {
        $data = $project->levels();
        $data->orderByDesc('id');
        return helper_response_fetch(ProjectLevelIndexResource::collection($data->get()));
    }

   public function store($request,$project)
   {
       $data = $project->levels()->create([
           'name' => $request->name,
           'color' => $request->color,
           'description' => $request->description,
           'priority' => $request->priority,
       ]);
       return helper_response_fetch(new ProjectLevelIndexResource($data));
   }

   public function show($item,$project)
   {
       return helper_response_fetch(new ProjectLevelIndexResource($item));
   }

   public function update($request, $item)
   {

        if(auth()->user()->type !== 'admin' && (!$item->project || $item->project->member_id != auth()->id())){
            return helper_response_error('شما اجازه ویرایش این وضعیت را ندارید');
        }

       $item->update([
           'name' => $request->name,
           'color' => $request->color,
           'description' => $request->description,
           'priority' => $request->priority,
       ]);
       return helper_response_updated(new ProjectLevelIndexResource($item));
   }

   public function destroy($item,$project)
   {
        if(auth()->user()->type !== 'admin' && (!$item->project || $item->project->member_id != auth()->id())){
            return helper_response_error('شما اجازه حذف این وضعیت را ندارید');
        }
       $item->delete();
       return helper_response_deleted();
   }


}
