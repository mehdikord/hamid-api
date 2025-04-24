<?php
namespace App\Repositories\Users;
use App\Http\Resources\Users\UserCustomerIndexResource;
use App\Interfaces\Users\UserCustomerInterface;

class UserCustomerRepository implements UserCustomerInterface
{
    public function users_index($user)
    {
        $data = $user->customers();
        $data->orderBy(request('sort_by'),request('sort_type'));
        return helper_response_fetch(UserCustomerIndexResource::collection($data->paginate(request('per_page')))->resource);

    }


}
