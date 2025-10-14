<?php
namespace App\Repositories\Fields;
use App\Http\Resources\Fields\FieldIndexResource;
use App\Interfaces\Fields\FieldInterface;
use App\Models\Fields\Field;


class FieldRepository implements FieldInterface
{

   public function index($project)
   {
       $data = $project->fields();
       $data->with('options');
       $data->orderBy(request('sort_by'),request('sort_type'));
       return helper_response_fetch(FieldIndexResource::collection($data->paginate(request('per_page')))->resource);
   }

    public function all($project)
    {
        $data = $project->fields();
        $data->with('options');
        $data->orderByDesc('id');
        return helper_response_fetch(FieldIndexResource::collection($data->get()));
    }

   public function store($request,$project)
   {
       $data = $project->fields()->create([
           'title' => $request->title,
           'type' => $request->type,
           'placeholder' => $request->placeholder,
           'default' => $request->default,
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
       return helper_response_fetch(new FieldIndexResource($data));
   }

   public function show($item,$project)
   {
       if($project->id != $item->project_id){
            return helper_response_error('Project not found');
        }
       $item->load('options');
       return helper_response_fetch(new FieldIndexResource($item));
   }

   public function update($request, $item,$project)
   {
        if($project->id != $item->project_id){
            return helper_response_error('Project not found');
        }
       $item->load('options');
       $item->update([
           'title' => $request->title,
           'type' => $request->type,
           'placeholder' => $request->placeholder,
           'default' => $request->default,
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

       return helper_response_updated(new FieldIndexResource($item));
   }

   public function destroy($item,$project)
   {
       if($project->id != $item->project_id){
            return helper_response_error('Project not found');
        }
       $item->load('options');
       $item->options()->delete();
       $item->delete();
       return helper_response_deleted();
   }


}
