<?php
namespace App\Repositories\Positions;
use App\Interfaces\Positions\PositionInterface;
use App\Models\Position;


class PositionRepository implements PositionInterface
{

   public function index()
   {
       $data = Position::query();
       $data->orderBy(request('sort_by'),request('sort_type'));
       return helper_response_fetch($data->paginate(request('per_page')));
   }

    public function all()
    {
        $data = Position::query();
        $data->orderByDesc('id');
        return helper_response_fetch($data->get());
    }

   public function store($request)
   {
       $data = Position::create([
           'name' => $request->name,
           'color' => $request->color,
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
           'color' => $request->color,
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
