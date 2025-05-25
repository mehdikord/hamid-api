<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User_Position extends Model
{
    protected $table = 'user_positions';
    protected $guarded = [];

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class,'position_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
