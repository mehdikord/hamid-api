<?php
namespace App\Repositories\Projects;
use App\Http\Resources\Projects\Statuses\ProjectStatusIndexResource;
use App\Http\Resources\Projects\Statuses\ProjectStatusShortResource;
use App\Http\Resources\Projects\Statuses\ProjectStatusSingleResource;
use App\Http\Resources\StatusMessages\StatusMessageIndexResource;
use App\Interfaces\Projects\ProjectStatusInterface;
use App\Models\Project_Status;


class ProjectStatusRepository implements ProjectStatusInterface
{

   public function index($project)
   {

       $data = $project->statuses();
       $data->orderBy(request('sort_by'),request('sort_type'));

       return helper_response_fetch(ProjectStatusIndexResource::collection($data->paginate(request('per_page')))->resource);
   }

    public function all($project)
    {
        $data = $project->statuses();
        $data->orderByDesc('id');
        return helper_response_fetch(ProjectStatusShortResource::collection($data->get()));
    }

   public function store($request,$project)
   {
       $data = $project->statuses()->create([
           'name' => $request->name,
           'color' => $request->color,
           'description' => $request->description,

       ]);
       return helper_response_fetch(new ProjectStatusSingleResource($data));
   }

   public function show($item,$project)
   {
       return helper_response_fetch(new ProjectStatusSingleResource($item));
   }

   public function update($request, $item)
   {

        if($item->project->member_id != auth()->id()){
            return helper_response_error('شما اجازه ویرایش این وضعیت را ندارید');
        }
       $item->update([
           'name' => $request->name,
           'color' => $request->color,
           'description' => $request->description,
       ]);
       return helper_response_updated(new ProjectStatusSingleResource($item));
   }

   public function destroy($item,$project)
   {
        if($item->project->member_id != auth()->id()){
            return helper_response_error('شما اجازه حذف این وضعیت را ندارید');
        }
       $item->delete();
       return helper_response_deleted();
   }

    public function get_messages($project,$status)
    {
        $data = $status->status_messages();
        $data->orderByDesc('id');
        return helper_response_fetch(StatusMessageIndexResource::collection($data->get()));
    }
    public function store_messages($project,$status,$request)
    {

        if ($request->filled('status_message_id')) {
            $status->status_messages()->attach($request->status_message_id);
        }

        return helper_response_fetch(StatusMessageIndexResource::collection($status->status_messages()->get()));
    }
}
