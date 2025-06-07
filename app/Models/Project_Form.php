<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project_Form extends Model
{
    protected $table = 'project_forms';
    protected $guarded = [];

    public function fields(): HasMany
    {
        return $this->hasMany(Project_Form_Field::class, 'project_form_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
