<?php

namespace App\Models\Fields;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field_Option extends Model
{
    use HasFactory;
    protected $table = 'field_options';
    protected $guarded=[];
}
