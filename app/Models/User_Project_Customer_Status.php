<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User_Project_Customer_Status extends Model
{
    use HasFactory;
    protected $table = 'user_project_customer_statuses';
    protected $guarded=[];

    public function project_customer():BelongsTo
    {
        return $this->belongsTo(Project_Customer::class,'project_customer_id');
    }

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function customer():BelongsTo
    {
        return $this->belongsTo(Customer::class,'customer_id');
    }

    public function status():BelongsTo
    {
        return $this->belongsTo(Project_Customer_Status::class,'customer_status_id');
    }


}
