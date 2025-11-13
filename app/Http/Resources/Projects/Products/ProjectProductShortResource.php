<?php

namespace App\Http\Resources\Projects\Products;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $project_id
 * @property mixed $name
 * @property mixed $type
 */
class ProjectProductShortResource extends JsonResource
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
            'name' => $this->name,
            'type' => $this->type,
            'price' => $this->price,
        ];
    }
}

