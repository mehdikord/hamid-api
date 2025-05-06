<?php

namespace App\Models;

use App\Models\Fields\Field;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;
    protected $table = 'projects';
    protected $guarded=[];

    protected static function booted(): void
    {
        static::creating(static function ($model) {
            $model->created_by = auth()->id();
        });
        static::updating(static function ($model) {
            $model->updated_by = auth()->id();
        });
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(Project_Category::class,'project_category_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Project_Status::class,'project_status_id');
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Project_Customer::class,'project_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User_Project::class,'project_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Project_Customer_Report::class,'project_id');
    }

    public function invoices():HasMany
    {
        return $this->hasMany(Project_Customer_Invoice::class,'project_id');
    }

    public function fields(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Field::class,'project_fields');
    }

}
