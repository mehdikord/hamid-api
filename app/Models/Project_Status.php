<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project_Status extends Model
{
    use HasFactory;
    protected $table = 'project_statuses';
    protected $guarded=[];

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class,'project_status_id');
    }
}
