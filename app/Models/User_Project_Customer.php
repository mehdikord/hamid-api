<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User_Project_Customer extends Model
{
    use HasFactory;
    protected $table='user_project_customers';
    protected $guarded=[];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function project_customer(): BelongsTo
    {
        return $this->belongsTo(Project_Customer::class,'project_customer_id');
    }

}
