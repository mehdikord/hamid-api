<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project_Level extends Model
{

    protected $table = 'project_levels';
    protected $guarded=[];
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
    

}
