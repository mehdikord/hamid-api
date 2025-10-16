<?php
namespace App\Repositories\Tags;
use App\Interfaces\Tags\TagInterface;
use App\Models\Project;
use App\Models\Tag;


class TagRepository implements TagInterface
{

   public function index($project)
   {
       $data = $project->tags();
       $data->orderBy(request('sort_by'),request('sort_type'));
       return helper_response_fetch($data->paginate(request('per_page')));
   }

    public function all($project)
    {
        $data = $project->tags();
        $data->orderByDesc('id');
        return helper_response_fetch($data->get());
    }

   public function store($request,$project)
   {
       $data = $project->tags()->create([
           'name' => $request->name,

       ]);
       // activity log
       helper_activity_create(null,null,null,null,'ایجاد تگ'," : ایجاد تگ ".$data->name."");
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
       ]);
       // activity log
       helper_activity_create(null,null,null,null,'ویرایش تگ'," : ویرایش تگ ".$item->name."");
       return helper_response_updated($item);
   }

   public function destroy($item,$project)
   {
       // activity log
       helper_activity_create(null,null,null,null,'حذف تگ'," : حذف تگ ".$item->name."");
       $item->delete();
       return helper_response_deleted();
   }


}
