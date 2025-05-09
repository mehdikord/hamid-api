<?php
namespace App\Repositories\Tags;
use App\Interfaces\Tags\TagInterface;
use App\Models\Tag;


class TagRepository implements TagInterface
{

   public function index()
   {
       $data = Tag::query();
       $data->orderBy(request('sort_by'),request('sort_type'));
       return helper_response_fetch($data->paginate(request('per_page')));
   }

    public function all()
    {
        $data = Tag::query();
        $data->orderByDesc('id');
        return helper_response_fetch($data->get());
    }

   public function store($request)
   {
       $data = Tag::create([
           'name' => $request->name,
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
       ]);
       return helper_response_updated($item);
   }

   public function destroy($item)
   {
       $item->delete();
       return helper_response_deleted();
   }


}
