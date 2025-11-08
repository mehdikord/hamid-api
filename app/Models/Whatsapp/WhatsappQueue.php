<?php

namespace App\Models\Whatsapp;

use App\Models\Admin;
use App\Models\Customer;
use App\Models\Project;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsappQueue extends Model
{

    protected $table = 'whatsapp_queue';
    protected $guarded = [];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
    public function whatsapp_number(): BelongsTo
    {
        return $this->belongsTo(WhatsappNumber::class, 'whatsapp_number_id');
    }
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
