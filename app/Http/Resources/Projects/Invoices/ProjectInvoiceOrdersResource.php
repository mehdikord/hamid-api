<?php

namespace App\Http\Resources\Projects\Invoices;
use App\Http\Resources\Projects\Products\ProjectProductShortResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @property mixed $id
 */
class ProjectInvoiceOrdersResource extends JsonResource
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
            'invoice_id' => $this->invoice_id,
            'product_id' => $this->product_id,
            'product' => new ProjectProductShortResource($this->product),
            'quantity' => $this->quantity,
            'amount' => $this->amount,
            'file_url' => $this->file_url,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
