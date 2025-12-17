<?php

namespace App\Http\Resources\User_Customers;

use App\Http\Resources\Customers\CustomerIndexResource;
use App\Http\Resources\Customers\Settings\Statuses\CustomerSettingsStatusShortResource;
use App\Http\Resources\Projects\Projects\ProjectRelationResource;
use App\Http\Resources\Projects\Projects\ProjectShortResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use function Symfony\Component\Translation\t;

/**
 * @property mixed $id
 * @property mixed $name
 * @property mixed $email
 * @property mixed $profile
 * @property mixed $config
 */
class UserCustomerProjectCustomerResource extends JsonResource
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
            'customer_id' => $this->customer_id,
            'project' => new ProjectRelationResource($this->project),
            'customer' => new CustomerIndexResource($this->customer),
        ];
    }
}
