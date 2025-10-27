<?php

namespace App\Http\Resources\Projects\Projects;

use App\Http\Resources\Customers\CustomerIndexResource;
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
class ProjectCustomerClientsResource extends JsonResource
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
            'customer' => new CustomerIndexResource($this->customer),
            'users' => UserProjectCustomerResource::collection($this->users),
            'target_price' => $this->target_price,
            'sum_invoices' => $this->invoices()->sum('amount'),
            'first_invoice_date' => $this->invoices() ? $this->invoices()->first()->created_at : null,
            'last_invoice_date' => $this->invoices() ? $this->invoices()->latest()->first()->created_at : null,
        ];
    }
}
