<?php
namespace App\Repositories\Reporting;
use App\Interfaces\Reporting\ReportingInterface;
use App\Models\Project;
use App\Models\Project_Customer;
use App\Models\Project_Customer_Invoice;
use App\Models\Project_Customer_Report;
use App\Models\User;
use App\Models\User_Project_Customer;
use Carbon\Carbon;


class ReportingRepository implements ReportingInterface
{

    public function admin_users_weekly($request)
    {
        $days = [];
        for($i = 0; $i <= 6; $i++) {
            $days[] = Carbon::today()->subDays($i)->format('Y-m-d');
        }
        $result=[];
        $date = $request->date;
        if (!$date){
            $date = $days[0];
        }
        if ($date){
            foreach (User::all() as $user) {
                //get user projects
                foreach ($user->projects as $project) {
                    $customers = Project_Customer::where('project_id', $project->project_id)->whereHas('user', function ($query)use ($user) {
                        $query->where('user_id', $user->id);
                    })->pluck('id')->toArray();
                    $report_count = Project_Customer_Report::whereIn('project_customer_id', $customers)->whereDate('created_at',$date)->count();
                    $invoice_price = Project_Customer_Invoice::whereIn('project_customer_id', $customers)->whereDate('created_at',$date)->sum('amount');
                    $invoice_count = Project_Customer_Invoice::whereIn('project_customer_id', $customers)->whereDate('created_at',$date)->count();
                    $result[$user->id]['user'] = $user->name;
                    $result[$user->id]['project'][$project->project_id]['project'] = $project->project->name;
                    $result[$user->id]['project'][$project->project_id]['report_count'] = $report_count;
                    $result[$user->id]['project'][$project->project_id]['invoice_price'] = $invoice_price;
                    $result[$user->id]['project'][$project->project_id]['invoice_count'] = $invoice_count;
                    $result[$user->id]['project'][$project->project_id]['customers'] = count($customers);
                }

            }

        }

        $data = [
            'days' => $days,
            'current_date' => $date,
            'result' => $result,
        ];
        return helper_response_fetch($data);
    }

    public function projects_summery()
    {
        $result=[];
        $projects = Project::query();
        $projects->select(['id','name']);
        $projects->withCount('users','customers','reports','invoices');
        $projects = $projects->get();
        foreach ($projects as $project) {
            $assigned_customers = $project->customers()->where('status','assigned')->count();
            $invoices_price = $project->invoices()->sum('amount');
            $invoices_target = User_Project_Customer::whereHas('project_customer',function($query)use($project){
                $query->where('project_id',$project->id);
            })->sum('target_price');
            $result[]=[
                'project' => $project,
                'assigned_customers' => $assigned_customers,
                'invoices_price' => $invoices_price,
                'invoices_target' => $invoices_target,
            ];

        }

        return helper_response_fetch($result);

    }

}
