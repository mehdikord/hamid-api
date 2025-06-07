<?php

namespace App\Http\Resources\Projects\Forms;

use App\Http\Resources\Customers\CustomerIndexResource;
use App\Http\Resources\Fields\FieldIndexResource;
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
class ProjectFromFieldsIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'project_form_id' => $this->project_form_id,
            'field_id' => $this->field_id,
            'id' => $this->id,
            'title' => $this->title,
            'field' => new FieldIndexResource($this->field)
        ];
    }
}
