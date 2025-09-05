<?php

namespace App\Http\Resources\Activities;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $title
 * @property mixed $activity
 * @property mixed $ip
 * @property mixed $device
 * @property mixed $created_at
 * @property mixed $updated_at
 * @property mixed $admin
 * @property mixed $user
 * @property mixed $project
 * @property mixed $customer
 */
class ActivitySingleResource extends JsonResource
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
            'activity' => $this->activity,
            'ip' => $this->ip,
            'device' => $this->device,
            'admin' => $this->when($this->admin, [
                'id' => $this->admin?->id,
                'name' => $this->admin?->name,
                'email' => $this->admin?->email,
            ]),
            'user' => $this->when($this->user, [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
                'email' => $this->user?->email,
                'phone' => $this->user?->phone,
            ]),
            'project' => $this->when($this->project, [
                'id' => $this->project?->id,
                'name' => $this->project?->name,
                'code' => $this->project?->code,
            ]),
            'customer' => $this->when($this->customer, [
                'id' => $this->customer?->id,
                'name' => $this->customer?->name,
                'phone' => $this->customer?->phone,
                'email' => $this->customer?->email,
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
