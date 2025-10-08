<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project_Customer_Status extends Model
{
    use HasFactory;
    protected $table='project_customer_statuses';
    protected $guarded=[];

    public function customers() :hasMany
    {
        return $this->hasMany(Project_Customer::class,'project_customer_status_id');
    }
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class,'project_id');
    }
}
