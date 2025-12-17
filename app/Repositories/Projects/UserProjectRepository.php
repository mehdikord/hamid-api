<?php
namespace App\Repositories\Projects;

use App\Http\Resources\Fields\FieldIndexResource;
use App\Http\Resources\Projects\Invoices\UsersInvoiceResource;
use App\Http\Resources\Projects\Projects\ProjectRelationResource;
use App\Http\Resources\Projects\Projects\ProjectShortResource;
use App\Http\Resources\Projects\Reports\UsersReportResource;
use App\Interfaces\Projects\UserProjectInterface;

class UserProjectRepository implements UserProjectInterface
{
    public function all()
    {
        $result=[];
        $unique_ids = [];
        $data = auth('users')->user()->projects;
        foreach ($data as $item) {
            if (!in_array($item->project->id, $unique_ids, true)) {
                $result[] = $item->project;
                $unique_ids[] = $item->project->id;
            }
        }
        return helper_response_fetch(ProjectRelationResource::collection($result));
    }
    public function reports()
    {
        $data = auth('users')->user()->reports();


        if(request()->filled('search') && !empty(request()->search['project_id'])){
            $data = $data->where('project_id',request()->search['project_id']);
        }
        if(request()->filled('search') && !empty(request()->search['phone'])){

            $data = $data->whereHas('project_customer.customer',function($query) {
                $query->where('phone','like','%'.request()->search['phone'].'%');
            });

        }

        if(request()->filled('search') && !empty(request()->search['from_date'])){
            $data = $data->where('created_at','>=',request()->search['from_date']);
        }

        if(request()->filled('search') && !empty(request()->search['to_date'])){
            $data = $data->where('created_at','<=',request()->search['to_date']);
        }

        $data->orderBy(request('sort_by'),request('sort_type'));
        return helper_response_fetch(UsersReportResource::collection($data->paginate(request()->per_page))->resource);
    }
    public function invoices()
    {
        $data = auth('users')->user()->invoices();
        if(request()->filled('search') && !empty(request()->search['project_id'])){
            $data = $data->whereHas('project_customer.project',function($query) {
                $query->where('id',request()->search['project_id']);
            });
        }
        if(request()->filled('search') && !empty(request()->search['phone'])){

            $data = $data->whereHas('project_customer.customer',function($query) {
                $query->where('phone','like','%'.request()->search['phone'].'%');
            });

        }

        if(request()->filled('search') && !empty(request()->search['from_date'])){
            $data = $data->where('created_at','>=',request()->search['from_date']);
        }

        if(request()->filled('search') && !empty(request()->search['to_date'])){
            $data = $data->where('created_at','<=',request()->search['to_date']);
        }

        $data->orderBy(request('sort_by'),request('sort_type'));
        return helper_response_fetch(UsersInvoiceResource::collection($data->paginate(request()->per_page))->resource);
    }

    public function fields($project)
    {
        return helper_response_fetch(FieldIndexResource::collection($project->fields));
    }
}
