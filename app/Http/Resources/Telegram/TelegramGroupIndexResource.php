<?php

namespace App\Http\Resources\Telegram;

use App\Http\Resources\Projects\Projects\ProjectShortResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TelegramGroupIndexResource extends JsonResource
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
            'description' => $this->description,
            'telegram_id' => $this->telegram_id,
            'member_count' => $this->member_count,
            'type' => $this->type,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'project' => new ProjectShortResource($this->project),
        ];
    }
}
