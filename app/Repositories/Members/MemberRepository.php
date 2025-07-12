<?php
namespace App\Repositories\Members;
use App\Http\Resources\Members\MemberIndexResource;
use App\Interfaces\Members\MemberInterface;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;


class MemberRepository implements MemberInterface
{

   public function index()
   {
       $data = Admin::query();
       $data->where('type','user');
       $data->orderBy(request('sort_by'),request('sort_type'));
       return helper_response_fetch(MemberIndexResource::collection($data->paginate(request('per_page')))->resource);
   }

    public function all()
    {
        $data = Admin::query();
        $data->where('type','user');
        $data->orderByDesc('id');
        return helper_response_fetch(MemberIndexResource::collection($data->get()));
    }

   public function store($request)
   {
       $data = Admin::create([
           'name' => $request->name,
           'phone' => $request->phone,
           'email' => $request->email,
           'password' => Hash::make($request->password),
       ]);

       return helper_response_fetch(new MemberIndexResource($data));
   }
    public function change_password($request, $item)
    {
        $item->update([
            'password' => Hash::make($request->password),
        ]);
        return helper_response_updated(new MemberIndexResource($item));
    }


   public function show($item)
   {
       return helper_response_fetch(new MemberIndexResource($item));
   }

   public function update($request, $item)
   {
       $item->update([
           'name' => $request->name,
           'phone' => $request->phone,
           'email' => $request->email,
       ]);

       return helper_response_updated(new MemberIndexResource($item));
   }

   public function destroy($item)
   {
       $item->delete();
       return helper_response_deleted();
   }


}
