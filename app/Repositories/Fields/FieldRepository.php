<?php
namespace App\Repositories\Fields;
use App\Http\Resources\Fields\FieldIndexResource;
use App\Interfaces\Fields\FieldInterface;
use App\Models\Fields\Field;


class FieldRepository implements FieldInterface
{

   public function index()
   {
       $data = Field::query();
       $data->with('options');
       $data->orderBy(request('sort_by'),request('sort_type'));
       return helper_response_fetch(FieldIndexResource::collection($data->paginate(request('per_page')))->resource);
   }

    public function all()
    {
        $data = Field::query();
        $data->with('options');
        $data->orderByDesc('id');
        return helper_response_fetch(FieldIndexResource::collection($data->get()));
    }

   public function store($request)
   {
       $data = Field::create([
           'title' => $request->title,
           'type' => $request->type,
           'placeholder' => $request->placeholder,
           'default' => $request->default,
           'description' => $request->description,

       ]);
       return helper_response_fetch(new FieldIndexResource($data));
   }

   public function show($item)
   {
       return helper_response_fetch(new FieldIndexResource($item));
   }

   public function update($request, $item)
   {
       $item->update([
           'title' => $request->title,
           'type' => $request->type,
           'placeholder' => $request->placeholder,
           'default' => $request->default,
           'description' => $request->description,
       ]);
       return helper_response_updated(new FieldIndexResource($item));
   }

   public function destroy($item)
   {
       $item->delete();
       return helper_response_deleted();
   }


}
