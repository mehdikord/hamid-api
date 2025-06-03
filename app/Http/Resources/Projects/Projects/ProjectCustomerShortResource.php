<?php

namespace App\Http\Resources\Projects\Projects;

use App\Http\Resources\Customers\CustomerIndexResource;

use App\Http\Resources\User_Customers\UserCustomerReportResource;
use App\Http\Resources\Users\UserProjectCustomerResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $name
 * @property mixed $email
 * @property mixed $profile
 * @property mixed $config
 */
class ProjectCustomerShortResource extends JsonResource
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
            'customer' => new CustomerIndexResource($this->customer),
        ];
    }
}
