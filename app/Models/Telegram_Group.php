<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Telegram_Group extends Model
{
    use HasFactory;
    protected $table = 'telegram_groups';
    protected $guarded = [];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class,'project_id');
    }

    public function topics(): HasMany
    {
        return $this->hasMany(Telegram_Group_Topic::class, 'telegram_group_id');
    }

}
