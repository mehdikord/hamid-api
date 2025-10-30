<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project_Customer_Invoice extends Model
{
    use softDeletes;
    protected $table = 'project_customer_invoices';
    protected $guarded=[];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class,'project_id');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class,'admin_id');
    }
    public function project_customer(): BelongsTo
    {
        return $this->belongsTo(Project_Customer::class,'project_customer_id');
    }

    public static function columns($project)
    {
        $columns =  [
            [
                'field' => 'user_id',
                'title' => 'کارشناس',
                'type' => 'select',
                'data' => User::select('id','name')->where('is_active',true)->get(),
            ],
            [
                'field' => 'created_at',
                'title' => 'تاریخ ایجاد',
                'type' => 'date',
            ],
             [
                'field' => 'amount',
                'title' => 'مبلغ',
                'type' => 'number',
            ],
        ];
        return $columns;
    }
}
