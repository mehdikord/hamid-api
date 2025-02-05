<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User_Project extends Model
{
    use HasFactory;
    protected $table='user_projects';
    protected $guarded=[];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function project(): BelongsTo{
        return $this->belongsTo(Project::class,'project_id');
    }
}
