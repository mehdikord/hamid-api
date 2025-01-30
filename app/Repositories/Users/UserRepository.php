<?php
namespace App\Repositories\Users;

use App\Http\Resources\Profile\UserProfileResource;
use App\Http\Resources\Users\UserIndexResource;
use App\Http\Resources\Users\UserSingleResource;
use App\Interfaces\Users\UserInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserInterface
{

   public function index()
   {
       $data = User::query();
       $data->orderBy(request('sort_by'),request('sort_type'));
       return helper_response_fetch(UserIndexResource::collection($data->paginate(request('per_page')))->resource);
   }

   public function store($request)
   {
       $data = User::create([
           'name' => $request->name,
           'phone' => $request->phone,
           'email' => $request->email,
           'password' => Hash::make($request->password),
           'description' => $request->description,
           'is_active' => true,
       ]);
       return helper_response_fetch(new UserIndexResource($data));
   }

   public function show($item)
   {
       return helper_response_fetch(new UserSingleResource($item));
   }

   public function update($request, $item)
   {
       $item->update([
           'name' => $request->name,
           'phone' => $request->phone,
           'email' => $request->email,
           'description' => $request->description,
       ]);
       return helper_response_updated(new UserSingleResource($item));
   }

   public function change_password($request, $item)
   {
       $item->update([
           'password' => Hash::make($request->password),
       ]);
       return helper_response_updated(new UserSingleResource($item));
   }

   public function destroy($item)
   {
       $item->delete();
       return helper_response_deleted();
   }

   public function change_activation($item)
   {
       $item->update(['is_active' => !$item->is_active]);
       return helper_response_updated([]);
   }


}
