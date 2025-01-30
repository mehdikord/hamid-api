<?php
namespace App\Repositories\Projects;
use App\Http\Resources\Projects\Statuses\ProjectStatusIndexResource;
use App\Http\Resources\Projects\Statuses\ProjectStatusSingleResource;
use App\Interfaces\Projects\ProjectStatusInterface;
use App\Models\Project_Status;


class ProjectStatusRepository implements ProjectStatusInterface
{

   public function index()
   {
       $data = Project_Status::query();
       $data->orderBy(request('sort_by'),request('sort_type'));
       $data->withCount('projects');
       return helper_response_fetch(ProjectStatusIndexResource::collection($data->paginate(request('per_page')))->resource);
   }

    public function all()
    {
        $data = Project_Status::query();
        $data->orderByDesc('id');
        return helper_response_fetch(ProjectStatusIndexResource::collection($data->get()));
    }

   public function store($request)
   {
       $data = Project_Status::create([
           'name' => $request->name,
           'color' => $request->color,
           'description' => $request->description,

       ]);
       return helper_response_fetch(new ProjectStatusSingleResource($data));
   }

   public function show($item)
   {
       return helper_response_fetch(new ProjectStatusSingleResource($item));
   }

   public function update($request, $item)
   {
       $item->update([
           'name' => $request->name,
           'color' => $request->color,
           'description' => $request->description,
       ]);
       return helper_response_updated(new ProjectStatusSingleResource($item));
   }

   public function destroy($item)
   {
       $item->delete();
       return helper_response_deleted();
   }


}
