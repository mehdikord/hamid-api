<?php

namespace App\Models;

use App\Models\Fields\Project_Customer_Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Project_Customer extends Model
{
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

    public function project_status() :BelongsTo
    {
        return $this->belongsTo(Project_Customer_Status::class, 'project_customer_status_id');
    }

    public function project_level(): BelongsTo
    {
        return $this->belongsTo(Project_Level::class, 'project_level_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User_Project_Customer::class, 'project_customer_id','id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User_Project_Customer::class, 'project_customer_id','id');
    }

    public function reports():HasMany
    {
        return $this->hasMany(Project_Customer_Report::class, 'project_customer_id');
    }

    public function invoices():HasMany
    {
        return $this->hasMany(Project_Customer_Invoice::class, 'project_customer_id');
    }

    public function statuses():HasMany
    {
        return $this->hasMany(User_Project_Customer_Status::class, 'project_customer_id');
    }

    public function fields()
    {
        return $this->hasMany(Project_Customer_Field::class, 'project_customer_id');
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable','taggable');
    }
}
