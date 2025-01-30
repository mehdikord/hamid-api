<?php
namespace App\Repositories\Projects;

use App\Http\Resources\Projects\Categories\ProjectCategoryIndexResource;
use App\Http\Resources\Projects\Categories\ProjectCategorySingleResource;
use App\Interfaces\Projects\ProjectCategoryInterface;
use App\Models\Project_Category;


class ProjectCategoryRepository implements ProjectCategoryInterface
{

   public function index()
   {
       $data = Project_Category::query();
       $data->orderBy(request('sort_by'),request('sort_type'));
       $data->withCount('projects');
       return helper_response_fetch(ProjectCategoryIndexResource::collection($data->paginate(request('per_page')))->resource);
   }

    public function all()
    {
        $data = Project_Category::query();
        $data->orderByDesc('id');

        return helper_response_fetch(ProjectCategoryIndexResource::collection($data->get()));
    }
   public function store($request)
   {
       $data = Project_Category::create([
           'name' => $request->name,
           'color' => $request->color,
           'description' => $request->description,

       ]);
       return helper_response_fetch(new ProjectCategorySingleResource($data));
   }

   public function show($item)
   {
       return helper_response_fetch(new ProjectCategorySingleResource($item));
   }

   public function update($request, $item)
   {
       $item->update([
           'name' => $request->name,
           'color' => $request->color,
           'description' => $request->description,
       ]);
       return helper_response_updated(new ProjectCategorySingleResource($item));
   }

   public function destroy($item)
   {
       $item->delete();
       return helper_response_deleted();
   }


}
