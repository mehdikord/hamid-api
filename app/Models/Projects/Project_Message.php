<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Project;

class Project_Message extends Model
{
    use HasFactory;
    protected $table = 'project_messages';
    protected $guarded = [];

    public function getButtonsAttribute($value)
    {
        if (!empty($value)) {
            return json_decode($value, true);
        }
        return null;
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class,'project_id');
    }
}
