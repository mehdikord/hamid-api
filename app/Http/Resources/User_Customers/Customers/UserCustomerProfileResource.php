<?php

namespace App\Http\Resources\User_Customers\Customers;

use App\Http\Resources\Customers\CustomerIndexResource;
use App\Http\Resources\Customers\Settings\Statuses\CustomerSettingsStatusShortResource;
use App\Http\Resources\User_Customers\UserCustomerProjectCustomerResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use function Symfony\Component\Translation\t;

/**
 * @property mixed $id
 * @property mixed $name
 * @property mixed $email
 * @property mixed $profile
 * @property mixed $config
 */
class UserCustomerProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'national_code' => $this->national_code,
            'postal_code' => $this->postal_code,
            'address' => $this->address,
            'description' => $this->description,
            'instagram_id' => $this->instagram_id,
            'tel' => $this->tel,
            'created_at' => $this->created_at,
        ];
    }
}
