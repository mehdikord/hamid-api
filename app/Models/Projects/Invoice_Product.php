<?php

namespace App\Models\Projects;

use App\Models\Project_Customer_Invoice;
use App\Models\Projects\Project_Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice_Product extends Model
{
    use HasFactory;

    protected $table = 'project_customer_invoice_products';
    protected $guarded = [];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Project_Customer_Invoice::class, 'project_customer_invoices_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Project_Product::class, 'project_product_id');
    }
}

