<?php

namespace App\Http\Resources\User_Customers;

use App\Http\Resources\Customers\CustomerIndexResource;
use App\Http\Resources\Customers\Settings\Statuses\CustomerSettingsStatusShortResource;
use App\Http\Resources\Projects\Products\ProjectProductShortResource;
use App\Http\Resources\Users\UserShortResource;
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
class UserCustomerInvoiceResource extends JsonResource
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
            'project_customer' => new UserCustomerProjectCustomerResource($this->project_customer),
            'user' => new UserShortResource($this->user),
            'amount' => $this->amount,
            'description' => $this->description,
            'file_name' => $this->file_name,
            'file_url' => $this->file_url,
            'created_at' => $this->created_at,
            'products' => $this->when($this->relationLoaded('products') && $this->products->isNotEmpty(), function () {
                return ProjectProductShortResource::collection($this->products);
            }),
        ];
    }
}
