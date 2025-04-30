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
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'instagram_id' => $this->instagram_id,
            'tel' => $this->tel,
            'description' => $this->description,
            'national_code' => $this->national_code,
            'address' => $this->address,
            'postal_code' => $this->postal_code,
            'created_at' => $this->created_at
        ];
    }
}
