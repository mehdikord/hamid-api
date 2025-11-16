<?php

namespace App\Http\Resources\Projects\Forms;

use App\Http\Resources\Customers\CustomerIndexResource;
use App\Http\Resources\ImportMethods\ImportMethodShortResource;
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
class ProjectFromIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'project_id' => $this->project_id,
            'id'=> $this->id,
            'name'=> $this->name,
            'description' => $this->description,
            'link' => $this->link,
            'fields' => ProjectFromFieldsIndexResource::collection($this->fields),
            'is_active' => $this->is_active,
            'register' => $this->register,
            'view' => $this->view,
            'theme_name' => $this->theme_name,
            'theme_color' => $this->theme_color,
            'import_method' => new ImportMethodShortResource($this->import_method),
            'tag' => $this->tag,

        ];
    }
}
