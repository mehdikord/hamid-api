<?php
namespace App\Repositories\ProjectLevels;
use App\Interfaces\ProjectLevels\ProjectLevelInterface;
use App\Interfaces\Tags\TagInterface;
use App\Models\Project_Level;
use App\Models\Tag;


class ProjectLevelRepository implements ProjectLevelInterface
{

   public function index()
   {
       $data = Project_Level::query();
       $data->orderBy(request('sort_by'),request('sort_type'));
       return helper_response_fetch($data->paginate(request('per_page')));
   }

    public function all()
    {
        $data = Project_Level::query();
        $data->orderByDesc('id');
        return helper_response_fetch($data->get());
    }

   public function store($request)
   {
       $data = Project_Level::create([
           'name' => $request->name,
           'description' => $request->description,
       ]);
       return helper_response_fetch($data);
   }

   public function show($item)
   {
       return helper_response_fetch($item);
   }

   public function update($request, $item)
   {
       $item->update([
           'name' => $request->name,
           'description' => $request->description,
       ]);
       return helper_response_updated($item);
   }

   public function destroy($item)
   {
       $item->delete();
       return helper_response_deleted();
   }


}
