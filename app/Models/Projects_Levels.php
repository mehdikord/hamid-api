<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Projects_Levels extends Model
{
    use HasFactory;

    protected $table = 'projects_levels';
    protected $guarded = [];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class,'project_id');
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(Project_Level::class,'project_level_id');
    }
}
