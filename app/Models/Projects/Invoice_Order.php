<?php

namespace App\Models\Projects;

use App\Models\Project;
use App\Models\Project_Customer_Invoice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice_Order extends Model
{
    protected $table = 'invoice_orders';
    protected $guarded = [];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class,'project_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Project_Customer_Invoice::class,'invoice_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Project_Product::class,'product_id');
    }


}
