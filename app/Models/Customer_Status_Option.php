<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer_Status_Option extends Model
{
    protected $table = 'customer_status_options';
    protected $guarded = [];

    public function customer_status():BelongsTo
    {
        return $this->belongsTo(User_Project_Customer_Status::class,'customer_status_id');
    }
    public function message_option():BelongsTo
    {
        return $this->belongsTo(Status_Message_Option::class,'message_option_id');
    }
}
