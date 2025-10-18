<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Status_Message_Option extends Model
{
    use HasFactory;
    protected $table = 'status_message_options';
    protected $guarded = [];

    public function status_message(): BelongsTo
    {
        return $this->belongsTo(Status_Message::class,'status_message_id');
    }
}
