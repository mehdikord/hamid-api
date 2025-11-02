<?php
namespace App\Repositories\Reporting;
use App\Interfaces\Reporting\ReportingInterface;
use App\Models\Project;
use App\Models\Project_Customer;
use App\Models\Project_Customer_Invoice;
use App\Models\Project_Customer_Report;
use App\Models\Project_Customer_Status;
use App\Models\User;
use App\Models\User_Project_Customer;
use App\Models\User_Project_Customer_Status;
use Carbon\Carbon;


class ReportingRepository implements ReportingInterface
{

    public function admin_users_weekly($request,$project)
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

            foreach ($project->users as $user){
                $customers_ids = $user->user->customers()->whereHas('project_customer',function ($query)use($project){
                    $query->where('project_id',$project->id);
                })->pluck('project_customer_id')->toArray();
                $customer_count = $user->user->customers()->whereHas('project_customer',function ($query)use($project){
                    $query->where('project_id',$project->id);
                })->whereDate('created_at',$date)->count();
                $levels=[];
                $statuses=[];
                foreach ($project->levels as $level){
                    $levels[$level->level->id]['name'] = $level->level->name;
                    $levels[$level->level->id]['count'] = 0;
                }
                foreach (Project_Customer_Status::all() as $status){
                    $statuses[$status->id]['name'] = $status->name;
                    $statuses[$status->id]['count'] = 0;
                }
                $logs = User_Project_Customer_Status::whereIn('project_customer_id',$customers_ids)->whereDate('created_at',$date)->get();
                foreach ($logs as $log){
                    if ($log->project_level_id){
                        $levels[$log->project_level_id]['count']++;
                    }
                    if ($log->customer_status_id){
                        $statuses[$log->customer_status_id]['count']++;
                    }
                }
                $result[]=[
                    'user' => $user->user,
                    'customer_count' => $customer_count,
                    'levels' => $levels,
                    'statuses' => $statuses,
                ];

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

    public function projects_invoices_users($project)
    {
        //get projects users
        $users = [];
        foreach ($project->users as $user){


            $project_customers_ids = $project->customers()->whereHas('users',function ($query)use($user){
                $query->where('user_id',$user->user_id);
            })->pluck('id')->toArray();

            $target_sum = User_Project_Customer::whereIn('project_customer_id',$project_customers_ids)->where('user_id',$user->user_id)->sum('target_price');
            $invoice_sum = helper_reporting_customers_invoices_sum($project_customers_ids,$user->user_id);
            $done_count = Project_Customer::whereIn('id',$project_customers_ids)->where('selled',true)->count();
            $false_count = Project_Customer::whereIn('id',$project_customers_ids)->where('selled',false)->whereHas('invoices')->count();
            $done_price = helper_reporting_customers_selled_price($project_customers_ids);
            $false_price = helper_reporting_customers_not_selled_price($project_customers_ids);
            $all_count = $done_count + $false_count;
            $convert_present = 0;
            if ($all_count){
                $convert_present = round(($done_count * 100) / $all_count,2);
            }



            $users[]=[
                'user' => $user->user,
                'target_sum' => $target_sum,
                'invoice_sum' => $invoice_sum,
                'done_count' => $done_count,
                'false_count' => $false_count,
                'done_price' => $done_price,
                'false_price' => $false_price,
                'convert'     => $convert_present,
            ];

        }

        return helper_response_fetch($users);
    }

    public function summery($project){

        $main_customers = $project->customers()->count();
        $main_customer_assigned = $project->customers()->where('status','assigned')->count();
        $main_customer_invoices = $project->customers()->whereHas('invoices')->count();
        $main_total_invoice_target = $project->customers()->sum('target_price');
        $main_total_invoice_amount = $project->invoices()->sum('amount');
        $main_total_reports = $project->reports()->count();

        if(request()->filled("from_date") && request()->filled("to_date")){
            $from_date = helper_core_jalali_to_carbon(request()->from_date);
            $to_date = helper_core_jalali_to_carbon(request()->to_date);
            $is_same_date = $from_date->isSameDay($to_date);

            if($is_same_date){
                $main_customers = $project->customers()->whereDate('import_at',$from_date)->count();

                $main_customer_assigned = $project->customers()->whereHas('users',function($query)use($from_date){
                    $query->whereDate('start_at',$from_date);
                })->where('status','assigned')->count();
                
                $main_customer_invoices = $project->customers()->whereHas('invoices',function($query)use($from_date){
                    $query->whereDate('created_at',$from_date);
                })->count();
                $main_total_invoice_target = $project->customers()->whereHas('invoices',function($query)use($from_date){
                    $query->whereDate('created_at',$from_date);
                })->sum('target_price');
                $main_total_invoice_amount = $project->invoices()->whereDate('created_at',$from_date)->sum('amount');
                $main_total_reports = $project->reports()->whereDate('created_at',$from_date)->count();
            } else {
                $main_customers = $project->customers()->whereBetween('import_at',[$from_date,$to_date])->count();
                $main_customer_assigned = $project->customers()->whereHas('users',function($query)use($from_date,$to_date){
                    $query->whereBetween('start_at',[$from_date,$to_date]);
                })->where('status','assigned')->count();
                $main_customer_invoices = $project->customers()->whereHas('invoices',function($query)use($from_date,$to_date){
                    $query->whereBetween('created_at',[$from_date,$to_date]);
                })->count();
                $main_total_invoice_target = $project->customers()->whereHas('invoices',function($query)use($from_date,$to_date){
                    $query->whereBetween('created_at',[$from_date,$to_date]);
                })->sum('target_price');
                $main_total_invoice_amount = $project->invoices()->whereBetween('created_at',[$from_date,$to_date])->sum('amount');
                $main_total_reports = $project->reports()->whereBetween('created_at',[$from_date,$to_date])->count();
            }
        }

        $main = [
            'customers' => $main_customers,
            'customer_assigned' => $main_customer_assigned,
            'customer_invoices' => $main_customer_invoices,
            'total_invoice_target' => $main_total_invoice_target,
            'total_invoice_amount' => $main_total_invoice_amount,
            'total_reports' => $main_total_reports,
        ];

        $info =[];
        $invoices = [];
        if(request()->filled("from_date") && request()->filled("to_date")){
            $from_date = helper_core_jalali_to_carbon(request()->from_date);
            $to_date = helper_core_jalali_to_carbon(request()->to_date);
            $is_same_date = $from_date->isSameDay($to_date);

            $info_assigned = $project->customers()->whereHas('users',function($query)use($from_date,$to_date,$is_same_date){
                if($is_same_date){
                    $query->whereDate('start_at',$from_date);
                } else {
                    $query->whereBetween('start_at',[$from_date,$to_date]);
                }
            })->count();

            if($is_same_date){
                $info_registered_customers = $project->customers()->whereDate('import_at',$from_date)->count();
                $info_reports = $project->reports()->whereDate('created_at',$from_date)->count();
                $info_total_invoice_amount = $project->customers()->whereHas('invoices')->whereDate('created_at',$from_date)->sum('target_price');
            } else {
                $info_registered_customers = $project->customers()->whereBetween('import_at',[$from_date,$to_date])->count();
                $info_reports = $project->reports()->whereBetween('created_at',[$from_date,$to_date])->count();
                $info_total_invoice_amount = $project->customers()->whereHas('invoices')->whereBetween('created_at',[$from_date,$to_date])->sum('target_price');
            }


            $info_statuses =[];
            foreach ($project->statuses as $status){
                if($is_same_date){
                    $get_status_info = $project->customers()->whereHas('statuses',function($query)use($status){
                        $query->where('customer_status_id',$status->id);
                    })->whereDate('created_at',$from_date)->count();
                } else {
                    $get_status_info = $project->customers()->whereHas('statuses',function($query)use($status){
                        $query->where('customer_status_id',$status->id);
                    })->whereBetween('created_at',[$from_date,$to_date])->count();
                }
                if ($get_status_info){
                    $info_statuses[] = [
                        'name' => $status->name,
                        'color' => $status->color,
                        'count' => $get_status_info,
                    ];
                }

            }
            $info_levels =[];
            foreach ($project->levels as $level){
                if($is_same_date){
                    $get_level_info = $project->customers()->whereHas('statuses',function($query)use($level){
                        $query->where('project_level_id',$level->id);
                    })->whereDate('created_at',$from_date)->count();
                } else {
                    $get_level_info = $project->customers()->whereHas('statuses',function($query)use($level){
                        $query->where('project_level_id',$level->id);
                    })->whereBetween('created_at',[$from_date,$to_date])->count();
                }
                if ($get_level_info){
                    $info_levels[] = [
                        'name' => $level->name,
                        'color' => $level->color,
                        'count' => $get_level_info,
                    ];
                }
            }

            // Populate invoices array with daily invoice amounts
            $current_date = $from_date->copy();
            while ($current_date->lte($to_date)) {
                $daily_amount = $project->invoices()->whereDate('created_at', $current_date)->sum('amount');
                $invoices[] = [
                    'date' => $current_date->format('Y/m/d'),
                    'amount' => $daily_amount ?? 0,
                ];
                $current_date->addDay();
            }

            $info = [
                'assigned' => $info_assigned,
                'reports' => $info_reports,
                'statuses' => $info_statuses,
                'levels' => $info_levels,
                'registered_customers' => $info_registered_customers,
                'total_invoice_amount' => $info_total_invoice_amount,
            ];

        }

        return helper_response_fetch([
            'main' => $main,
            'info' => $info,
            'invoices' => $invoices,
        ]);




    }

    public function users_summery($project)
    {
        if(request()->filled("from_date") && request()->filled("to_date") && request()->filled("user_id")){
            $from_date = helper_core_jalali_to_carbon(request()->from_date);
            $to_date = helper_core_jalali_to_carbon(request()->to_date);
            $is_same_date = $from_date->isSameDay($to_date);
            $user = User::select('id','name')->find(request()->user_id);
            $result = [
                'user' => $user,
                'assigned_customers' => 0,
                'call_count' => 0,
                'change_level_count' => 0,
                'info_statuses' => [],
                'first_level_count' => 0,
                'second_level_count' => 0,
            ];
            if($user){
                if($is_same_date){
                    $assigned_customers = $user->customers()->whereHas('project_customer',function($query)use($project){
                        $query->where('project_id',$project->id);
                    })->whereDate('start_at',$from_date)->count();
                } else {
                    $assigned_customers = $user->customers()->whereHas('project_customer',function($query)use($project){
                        $query->where('project_id',$project->id);
                    })->whereBetween('start_at',[$from_date,$to_date])->count();
                }

                if($is_same_date){
                    $call_count = $project->customers()->whereHas('reports',function($query)use($user,$from_date){
                        $query->where('user_id',$user->id)->whereDate('created_at',$from_date);
                    })->count();
                } else {
                    $call_count = $project->customers()->whereHas('reports',function($query)use($user,$from_date,$to_date){
                        $query->where('user_id',$user->id)->whereBetween('created_at',[$from_date,$to_date]);
                    })->count();
                }

                if($is_same_date){
                    $change_level_count = $project->customers()->whereHas('statuses',function($query)use($user,$from_date){
                        $query->where('user_id',$user->id)->whereDate('created_at',$from_date)->whereNotNull('project_level_id');
                    })->count();
                } else {
                    $change_level_count = $project->customers()->whereHas('statuses',function($query)use($user,$from_date,$to_date){
                        $query->where('user_id',$user->id)->whereBetween('created_at',[$from_date,$to_date])->whereNotNull('project_level_id');
                    })->count();
                }

                $info_statuses =[];
                foreach ($project->statuses as $status){
                    if($is_same_date){
                        $get_status_info = $project->customers()->whereHas('statuses',function($query)use($status,$user,$from_date){
                            $query->where('customer_status_id',$status->id)->where('user_id',$user->id)->whereDate('created_at',$from_date);
                        })->count();
                    } else {
                        $get_status_info = $project->customers()->whereHas('statuses',function($query)use($status,$user,$from_date,$to_date){
                            $query->where('customer_status_id',$status->id)->where('user_id',$user->id)->whereBetween('created_at',[$from_date,$to_date]);
                        })->count();
                    }
                    if ($get_status_info){
                            $info_statuses[] = [
                                'name' => $status->name,
                                'color' => $status->color,
                                'count' => $get_status_info,
                            ];
                        }

                }

                if($is_same_date){
                    $first_level_count = $project->customers()->whereHas('statuses',function($query)use($user,$from_date){
                        $query->where('user_id',$user->id)->whereDate('created_at',$from_date)->whereHas('level',function($query){
                            $query->where('priority',1);
                        });
                    })->count();

                    $second_level_count = $project->customers()->whereHas('statuses',function($query)use($user,$from_date){
                        $query->where('user_id',$user->id)->whereDate('created_at',$from_date)->whereHas('level',function($query){
                            $query->where('priority',2);
                        });
                    })->count();
                } else {
                    $first_level_count = $project->customers()->whereHas('statuses',function($query)use($user,$from_date,$to_date){
                        $query->where('user_id',$user->id)->whereBetween('created_at',[$from_date,$to_date])->whereHas('level',function($query){
                            $query->where('priority',1);
                        });
                    })->count();

                    $second_level_count = $project->customers()->whereHas('statuses',function($query)use($user,$from_date,$to_date){
                        $query->where('user_id',$user->id)->whereBetween('created_at',[$from_date,$to_date])->whereHas('level',function($query){
                            $query->where('priority',2);
                        });
                    })->count();
                }

                $result = [
                    'user' => $user,
                    'assigned_customers' => $assigned_customers,
                    'call_count' => $call_count,
                    'change_level_count' => $change_level_count,
                    'info_statuses' => $info_statuses,
                    'first_level_count' => $first_level_count,
                    'second_level_count' => $second_level_count,
                ];

            }
            return helper_response_fetch($result);
        }
    }
}
