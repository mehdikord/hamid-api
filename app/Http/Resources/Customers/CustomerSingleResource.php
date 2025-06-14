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
class CustomerSingleResource extends JsonResource
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
            'tel' => $this->tel,
            'gender' => $this->gender,
            'description' => $this->description,
            'national_code' => $this->national_code,
            'address' => $this->address,
            'postal_code' => $this->postal_code,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'province' => $this->province,
            'city' => $this->city,
            'projects' => CustomerProjectsShortResource::collection($this->projects)
        ];
    }
}
