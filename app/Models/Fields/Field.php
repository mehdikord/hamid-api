<?php

namespace App\Models\Fields;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Field extends Model
{
    use HasFactory;
    protected $table = 'fields';
    protected $guarded=[];

    public function options():HasMany
    {
        return $this->hasMany(Field_Option::class,'field_id');
    }
}
