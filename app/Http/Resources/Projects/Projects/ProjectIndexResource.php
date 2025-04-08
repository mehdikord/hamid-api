<?php

namespace App\Http\Resources\Projects\Projects;

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
class ProjectIndexResource extends JsonResource
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
            'code' => $this->code,
            'image' => $this->image,
            'manager_name' => $this->manager_name,
            'manager_phone' => $this->manager_phone,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'total_customers' => $this->total_customers,
            'pending_customers' => $this->customers()->where('status', 'pending')->count(),
            'users_count' => $this->users_count,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'category' => new ProjectCategoryShortResource($this->category),
            'status' => new ProjectStatusShortResource($this->status)
        ];
    }
}
