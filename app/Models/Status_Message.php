<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status_Message extends Model
{
    use HasFactory;
    protected $table = 'status_messages';
    protected $guarded = [];

    public function options(): HasMany
    {
        return $this->hasMany(Status_Message_Option::class,'status_message_id');
    }
   
}
