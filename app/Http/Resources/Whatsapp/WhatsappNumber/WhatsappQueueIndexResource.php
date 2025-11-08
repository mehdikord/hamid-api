<?php

namespace App\Http\Resources\Whatsapp\WhatsappNumber;

use App\Http\Resources\Customers\CustomerIndexResource;
use App\Http\Resources\Projects\Projects\ProjectShortResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WhatsappQueueIndexResource extends JsonResource
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
            'message' => $this->message,
            'phone' => $this->phone,
            'customer' => new CustomerIndexResource($this->customer),
            'project' => new ProjectShortResource($this->project),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

