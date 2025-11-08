<?php

namespace App\Http\Resources\Projects\Projects;

use App\Http\Resources\Customers\CustomerIndexResource;
use App\Http\Resources\Users\UserProjectCustomerResource;
use Carbon\Carbon;
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
        //get user
        $convert_days = null;
        $user = $this->users()->where('position_id',helper_data_position_seller())->first();
        if($user){
            $start_date = Carbon::make($user->start_at);
            $first_invoice = $this->invoices()->where('user_id',$user->user->id)->first();
            if($first_invoice){
                $end_date = Carbon::make($first_invoice->created_at);
                $convert_days = $end_date->diffInDays($start_date);
            }

        }
        return [
            'id' => $this->id,
            'customer' => new CustomerIndexResource($this->customer),
            'users' => UserProjectCustomerResource::collection($this->users),
            'target_price' => $this->target_price,
            'sum_invoices' => $this->invoices()->sum('amount'),
            'convert_days' => $convert_days,
            'first_invoice_date' => $this->invoices() ? $this->invoices()->first()->created_at : null,
            'last_invoice_date' => $this->invoices() ? $this->invoices()->latest()->first()->created_at : null,
        ];
    }
}
