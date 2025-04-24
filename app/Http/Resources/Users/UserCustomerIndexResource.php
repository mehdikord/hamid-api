<?php

namespace App\Http\Resources\Users;

use App\Http\Resources\Customers\CustomerIndexResource;
use App\Http\Resources\Projects\Projects\ProjectShortResource;
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
class UserCustomerIndexResource extends JsonResource
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
            'user_id' => $this->user_id,
            'customer' => new CustomerIndexResource($this->project_customer->customer),
            'project' => new ProjectShortResource($this->project_customer->project),
            'target_price' => $this->target_price,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'description' => $this->description,
            'status' => new ProjectStatusShortResource($this->project_customer->project_status),
            'is_active' => $this->is_active,
        ];
    }
}
