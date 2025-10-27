<?php

namespace App\Http\Resources\Users;

use App\Http\Resources\Positions\PositionsShortResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $name
 * @property mixed $email
 * @property mixed $profile
 * @property mixed $config
 */
class UserProjectCustomerResource extends JsonResource
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
            'position_id' => $this->position_id,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'is_active' => $this->is_active,
            'target_price' => $this->target_price,
            'user' => new UserShortResource($this->user),
            'position' => new PositionsShortResource($this->position)
        ];
    }
}
