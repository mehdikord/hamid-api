<?php

namespace App\Models;

use App\Models\Fields\Field;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project_Form_Field extends Model
{
    protected $table = 'project_form_fields';
    protected $guarded = [];

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class, 'field_id');
    }
}
