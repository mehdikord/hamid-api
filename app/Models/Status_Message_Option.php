<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status_Message_Option extends Model
{
    use HasFactory;
    protected $table = 'status_message_options';
    protected $guarded = [];

    public function status_message(): BelongsTo
    {
        return $this->belongsTo(Status_Message::class,'status_message_id');
    }


    public function customers() :HasMany
    {
        return $this->hasMany(Customer_Status_Option::class,'message_option_id');
    }
}
