<?php

namespace App\Http\Resources\Fields;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $name
 * @property mixed $email
 * @property mixed $profile
 * @property mixed $config
 */
class FieldIndexResource extends JsonResource
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
            'title' => $this->title,
            'type' => $this->type,
            'placeholder' => $this->placeholder,
            'default' => $this->default,
            'description' => $this->description,
            'options' => FieldOptionIndexResource::collection($this->options)
        ];
    }
}
