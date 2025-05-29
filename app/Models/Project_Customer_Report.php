<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project_Customer_Report extends Model
{
    use softDeletes;
    protected $table = 'project_customer_reports';
    protected $guarded=[];

     public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class,'project_id');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function project_customer(): BelongsTo
    {
        return $this->belongsTo(Project_Customer::class,'project_customer_id');
    }

}
