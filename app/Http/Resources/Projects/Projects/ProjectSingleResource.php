<?php

namespace App\Http\Resources\Projects\Projects;

use App\Http\Resources\Fields\FieldIndexResource;
use App\Http\Resources\Projects\Categories\ProjectCategoryShortResource;
use App\Http\Resources\Projects\levels\ProjectLevelIndexResource;
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
class ProjectSingleResource extends JsonResource
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
            'project_category_id' => $this->project_category_id,
            'project_status_id' => $this->project_status_id,
            'name' => $this->name,
            'code' => $this->code,
            'image' => $this->image,
            'manager_name' => $this->manager_name,
            'manager_phone' => $this->manager_phone,
            'description' => $this->description,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'total_customers' => $this->total_customers,
            'pending_customers' => $this->customers()->where('status', 'pending')->count(),
            'users_count' => $this->users()->count(),
            'reports_count' => $this->reports()->count(),
            'invoices_count' => $this->invoices()->count(),
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'category' => new ProjectCategoryShortResource($this->category),
            'status' => new ProjectStatusShortResource($this->status),
            'fields' => FieldIndexResource::collection($this->fields),
            'positions' => ProjectPositionResource::collection($this->positions),
            'levels' => ProjectLevelIndexResource::collection($this->levels)
        ];
    }
}
