<?php

namespace App\Models\Whatsapp;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsappNumber extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_numbers';
    protected $guarded = [];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}

