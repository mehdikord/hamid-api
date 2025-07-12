<?php

namespace App\Models\Fields;

use App\Models\Scopes\MemberScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Field extends Model
{

    protected $table = 'fields';
    protected $guarded=[];

    protected static function booted()
    {
        static::addGlobalScope(new MemberScope);
        static::creating(function ($model) {
            if (helper_auth_is_member()){
                $model->member_id = auth('admins')->id();
            }
        });
    }
    public function options():HasMany
    {
        return $this->hasMany(Field_Option::class,'field_id');
    }
}
