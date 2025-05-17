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
class ProjectCustomerIndexResource extends JsonResource
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
            'project_level_id' => $this->project_level_id,
            'customer_id' => $this->customer_id,
            'status' => $this->status,
            'import_at' => $this->import_at,
            'description' => $this->description,
            'customer' => new CustomerIndexResource($this->customer),
            'user' => new UserProjectCustomerResource($this->user),
            'project_status' => $this->project_status,
            'project_level' => $this->project_level,
            'last_report' => new UserCustomerReportResource($this->reports()->latest()->first()),
            'total_invoice' => $this->invoices()->sum('amount'),
            'tags' => $this->tags,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

        ];
    }
}
