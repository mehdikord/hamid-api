<?php

namespace App\Http\Resources\Projects\Invoices;

use App\Http\Resources\Customers\CustomerIndexResource;
use App\Http\Resources\Projects\Products\ProjectProductShortResource;
use App\Http\Resources\Projects\Projects\ProjectRelationResource;
use App\Http\Resources\User_Customers\UserCustomerInvoiceOrdersResource;
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
class UsersInvoiceResource extends JsonResource
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
            'project' => ProjectRelationResource::make($this->project_customer?->project),
            'customer' => CustomerIndexResource::make($this->project_customer?->customer),
            'user' => new UserShortResource($this->user),
            'amount' => $this->amount,
            'target_price' => $this->target_price,
            'paid' => $this->paid,
            'paid_amount' => $this->orders->sum('amount'),
            'description' => $this->description,
            'file_name' => $this->file_name,
            'file_url' => $this->file_url,
            'created_at' => $this->created_at,
            'orders' => UserCustomerInvoiceOrdersResource::collection($this->orders),

        ];
    }
}
