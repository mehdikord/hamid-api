<?php

namespace App\Models;

use App\Models\Fields\Project_Customer_Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Project_Customer extends Model
{
    protected $table = 'project_customers';
    protected $guarded=[];

    public const
        STATUS_PENDING = 'pending',
        STATUS_ASSIGNED = 'assigned'


    ;
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function project_status() :BelongsTo
    {
        return $this->belongsTo(Project_Customer_Status::class, 'project_customer_status_id');
    }

    public function project_level(): BelongsTo
    {
        return $this->belongsTo(Project_Level::class, 'project_level_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User_Project_Customer::class, 'project_customer_id','id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User_Project_Customer::class, 'project_customer_id','id');
    }

    public function reports():HasMany
    {
        return $this->hasMany(Project_Customer_Report::class, 'project_customer_id');
    }

    public function invoices():HasMany
    {
        return $this->hasMany(Project_Customer_Invoice::class, 'project_customer_id');
    }

    public function statuses():HasMany
    {
        return $this->hasMany(User_Project_Customer_Status::class, 'project_customer_id');
    }

    public function fields()
    {
        return $this->hasMany(Project_Customer_Field::class, 'project_customer_id');
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable','taggable');
    }

    public function import_method(): BelongsTo
    {
        return $this->belongsTo(Import_Method::class, 'import_method_id');
    }

    public static function columns(){
        $columns = [
            [
                'field' => 'id',
                'title' => 'ID',
                'type' => 'number',
            ],
            [
                'field' => 'import_method_id',
                'title' => 'نوع ورودی',
                'type' => 'select',
                'data' => Import_Method::select('id','name')->get(),
            ],
            [
                'field' => 'tag_id',
                'title' => 'تگ',
                'type' => 'select',
                'data' => Tag::where()->select('id','name')->get(),
            ],

            [
                'field' => 'project_customer_status_id',
                'title' => 'وضعیت',
                'type' => 'select',
                'data' => Project_Customer_Status::select('id','name')->get(),
            ],
            [
                'field' => 'project_level_id',
                'title' => 'مرحله',
                'type' => 'select',
                'data' => Project_Level::select('id','name')->get(),
            ],
            [
                'field' => 'status',
                'title' => 'وضعیت ارجاع',
                'type' => 'select',
                'data' => [
                    [
                        'id' => 'assigned',
                        'name' => 'ارجاع شده',
                    ],
                    [
                        'id' => 'pending',
                        'name' => 'در انتظار',
                    ],
                ],
            ],
            [
                'field' => 'created_at',
                'title' => 'تاریخ ایجاد',
                'type' => 'date',
            ],
            [
                'field' => 'import_at',
                'title' => 'تاریخ ورودی',
                'type' => 'date',
            ],
            [
                'field' => 'users.user_id',
                'title' => 'کارشناس',
                'type' => 'select',
                'data' => User::select('id','name')->get(),
                'relation' => 'has_many',
            ]

        ];


        //Add relation
        foreach (Customer::columns() as $column) {
            $columns[] = [
                'field' => 'customer.'.$column['field'],
                'title' =>'اطلاعات مشتری : '.$column['title'],
                'type' => $column['type'],
                'relation' => 'belongs_to',
            ];
        }
        return $columns;
    }
}
