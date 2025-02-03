<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project_Customer extends Model
{
    use HasFactory;
    protected $table = 'project_customers';
    protected $guarded=[];

    public const
        STATUS_PENDING = 'pending',
        STATUS_ASSIGNED = 'assigned'


    ;
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
