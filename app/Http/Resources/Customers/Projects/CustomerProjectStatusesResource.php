<?php

namespace App\Http\Resources\Customers\Projects;

use App\Http\Resources\Projects\Categories\ProjectCategoryShortResource;
use App\Http\Resources\Projects\Statuses\ProjectStatusShortResource;
use App\Http\Resources\Users\UserShortResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $name
 * @property mixed $email
 * @property mixed $profile
 * @property mixed $config
 */
class CustomerProjectStatusesResource extends JsonResource
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
            'status' => new ProjectStatusShortResource($this->status),
            'level' => $this->level,
            'user' => new UserShortResource($this->user),
            'description' => $this->description,
            'created_at' => $this->created_at,
        ];
    }
}
