<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Import_Method extends Model
{
    use HasFactory;
    protected $table = 'import_methods';
    protected $guarded=[];
}
