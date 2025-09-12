<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{

    protected $table = 'customers';
    protected $guarded = [];

    public function projects(): HasMany
    {
        return $this->hasMany(Project_Customer::class, 'customer_id');
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class,'province_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class,'city_id');
    }

    public static function columns(){
        return [
            [
                'field' => 'id',
                'title' => 'ID',
                'type' => 'number',
            ],
            [
                'field' => 'name',
                'title' => 'نام',
                'type' => 'text',
            ],
            [
                'field' => 'phone',
                'title' => 'موبایل',
                'type' => 'text',
            ],
            [
                'field' => 'email',
                'title' => 'ایمیل',
                'type' => 'text',
            ],
            [
                'field' => 'instagram_id',
                'title' => 'اینستاگرام',
                'type' => 'text',
            ],
            [
                'field' => 'created_at',
                'title' => 'تاریخ ایجاد',
                'type' => 'date',
            ],
            
        ];
    }


}
