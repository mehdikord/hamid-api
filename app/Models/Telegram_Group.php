<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Telegram_Group extends Model
{
    use HasFactory;
    protected $table = 'telegram_groups';
    protected $guarded = [];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class,'project_id');
    }
}
