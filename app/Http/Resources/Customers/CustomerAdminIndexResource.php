<?php

namespace App\Http\Resources\Customers;

use App\Http\Resources\Projects\Categories\ProjectCategoryShortResource;
use App\Http\Resources\Projects\Statuses\ProjectStatusShortResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $name
 * @property mixed $email
 * @property mixed $profile
 * @property mixed $config
 */
class CustomerAdminIndexResource extends JsonResource
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
            'province_id' => $this->province_id,
            'city_id' => $this->city_id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'instagram_id' => $this->instagram_id,
            'gender' => $this->gender,
            'province' => $this->province,
            'city' => $this->city,
            'created_at' => $this->created_at
        ];
    }
}
