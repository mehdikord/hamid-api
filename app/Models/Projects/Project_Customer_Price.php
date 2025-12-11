<?php

namespace App\Models\Projects;

use App\Models\Project_Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project_Customer_Price extends Model
{

    protected $table = 'project_customer_prices';
    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function project_customer(): BelongsTo
    {
        return $this->belongsTo(Project_Customer::class, 'project_customer_id');
    }

    public function project_product(): BelongsTo
    {
        return $this->belongsTo(Project_Product::class, 'project_product_id');
    }
}
