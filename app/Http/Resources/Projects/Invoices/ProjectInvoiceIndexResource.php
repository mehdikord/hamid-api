<?php

namespace App\Http\Resources\Projects\Invoices;

use App\Http\Resources\Customers\CustomerIndexResource;
use App\Http\Resources\Projects\Categories\ProjectCategoryShortResource;
use App\Http\Resources\Projects\Statuses\ProjectStatusShortResource;
use App\Http\Resources\Users\UserShortResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $name
 * @property mixed $email
 * @property mixed $profile
 * @property mixed $config
 */
class ProjectInvoiceIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $customer = null;
        if ($this->project_customer && $this->project_customer->customer) {
            $customer = new CustomerIndexResource($this->project_customer->customer);
        }
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'description' => $this->description,
            'file_name' => $this->file_name,
            'file_url' => $this->file_url,
            'user' => new UserShortResource($this->user),
            'customer' => $customer,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

        ];
    }
}
