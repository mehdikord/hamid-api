<?php

namespace App\Http\Resources\Projects\Projects;

use App\Http\Resources\Customers\CustomerIndexResource;

use App\Http\Resources\Users\UserProjectCustomerResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $name
 * @property mixed $email
 * @property mixed $profile
 * @property mixed $config
 */
class ProjectCustomerIndexResource extends JsonResource
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
            'project_id' => $this->project_id,
            'customer_id' => $this->customer_id,
            'status' => $this->status,
            'import_at' => $this->import_at,
            'description' => $this->description,
            'customer' => new CustomerIndexResource($this->customer),
            'user' => new UserProjectCustomerResource($this->user),
            'project_status' => $this->project_status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

        ];
    }
}
