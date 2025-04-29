<?php

namespace App\Http\Resources\User_Customers;

use App\Http\Resources\Customers\CustomerIndexResource;
use App\Http\Resources\Customers\Settings\Statuses\CustomerSettingsStatusShortResource;
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
class UserCustomerStatusResource extends JsonResource
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
            'project_customer_id' => $this->project_customer_id,
            'status' => new CustomerSettingsStatusShortResource($this->status),
            'customer' => new CustomerIndexResource($this->customer),
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->created_at,
        ];
    }
}
