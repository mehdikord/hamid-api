<?php

namespace App\Repositories\Projects;

use App\Interfaces\Projects\ProjectProductInterface;
use App\Http\Resources\Projects\Products\ProjectProductIndexResource;
use App\Http\Resources\Projects\Products\ProjectProductShortResource;
use App\Models\Projects\Project_Product;

class ProjectProductRepository implements ProjectProductInterface
{
    public function index($project)
    {
        $data = $project->products();
        $data->where('project_id',$project->id);
        $data->orderBy(request('sort_by'),request('sort_type'));
        return helper_response_fetch(ProjectProductIndexResource::collection($data->paginate(request('per_page')))->resource);
    }
    public function all($project)
    {
        $data = $project->products();
        $data->where('project_id',$project->id);
        $data->orderByDesc('id');
        return helper_response_fetch(ProjectProductShortResource::collection($data->get()));
    }

    public function store($project,$request)
    {
        $data = $project->products()->create([
            'name' => $request->name,
            'type' => $request->type,
            'price' => $request->price,
            'access' => $request->access,
            'description' => $request->description,
        ]);

        // activity log
        helper_activity_create(null,null,$project->id, null,'ایجاد محصول','ایجاد محصول '.$data->name);
        return helper_response_fetch(new ProjectProductIndexResource($data));
    }

    public function show($project,$item)
    {
        return helper_response_fetch(new ProjectProductIndexResource($item));
    }

    public function update($project,$request,$item)
    {
        $item->update([
            'name' => $request->name,
            'type' => $request->type,
            'price' => $request->price,
            'access' => $request->access,
            'description' => $request->description,
        ]);
        // activity log
        helper_activity_create(null,null,$project->id,null,'ویرایش محصول',"ویرایش محصول ".$item->name);
        return helper_response_updated(new ProjectProductIndexResource($item));
    }

    public function destroy($project,$item)
    {
        // activity log
        helper_activity_create(null,null,$project->id,null,'حذف محصول','حذف محصول '.$item->name);
        $item->delete();
        return helper_response_deleted();
    }

}

