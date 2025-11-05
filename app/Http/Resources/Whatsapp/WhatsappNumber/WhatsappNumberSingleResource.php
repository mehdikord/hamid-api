<?php

namespace App\Http\Resources\Whatsapp\WhatsappNumber;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WhatsappNumberSingleResource extends JsonResource
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
            'admin_id' => $this->admin_id,
            'number' => $this->number,
            'last_used' => $this->last_used,
            'is_active' => $this->is_active,
            'is_block' => $this->is_block,
            'use_count' => $this->use_count,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

