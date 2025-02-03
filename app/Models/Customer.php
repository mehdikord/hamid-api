<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;
    protected $table='customers';
    protected $guarded=[];

    public function projects(): HasMany
    {
        return $this->hasMany(Project_Customer::class, 'customer_id');
    }


}
