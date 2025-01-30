<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auth_Code extends Model
{
    use HasFactory;
    protected $table = 'auth_codes';
    protected $guarded=[];

}
