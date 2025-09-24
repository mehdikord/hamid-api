<?php

namespace App\Http\Resources\Users;

use App\Http\Resources\Customers\CustomerIndexResource;
use App\Http\Resources\ImportMethods\ImportMethodShortResource;
use App\Http\Resources\Projects\Projects\ProjectShortResource;
use App\Http\Resources\Projects\Statuses\ProjectStatusShortResource;
use App\Http\Resources\User_Customers\UserCustomerReportResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $name
 * @property mixed $email
 * @property mixed $profile
 * @property mixed $config
 */
class UserCustomerIndexResource extends JsonResource
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
            'project_customer_id' => $this->project_customer_id,
            'user_id' => $this->user_id,
            'customer' => new CustomerIndexResource($this->project_customer->customer),
            'project' => new ProjectShortResource($this->project_customer->project),
            'target_price' => $this->project_customer->target_price,
            'start_at' => $this->start_at,
            'created_at' => $this->created_at,
            'end_at' => $this->end_at,
            'description' => $this->description,
            'status' => new ProjectStatusShortResource($this->project_customer->project_status),
            'level' => $this->project_customer->project_level,
            'is_active' => $this->is_active,
            'last_report' => new UserCustomerReportResource($this->project_customer->reports()->latest()->first()),
            'reports_count' => $this->project_customer->reports()->count(),
            'invoices_count' => $this->project_customer->invoices()->count(),
            'sum_invoices' => $this->project_customer->invoices()->sum('amount'),
            'import_method' => new ImportMethodShortResource($this->project_customer->import_method),
            'tags' => $this->project_customer->tags
        ];
    }
}
