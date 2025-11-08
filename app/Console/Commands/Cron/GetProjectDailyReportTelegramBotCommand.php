<?php

namespace App\Console\Commands\Cron;

use App\Models\Project;
use App\Models\Project_Customer;
use App\Models\Project_Customer_Invoice;
use App\Models\Project_Customer_Report;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Morilog\Jalali\Jalalian;

class GetProjectDailyReportTelegramBotCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cron-daily-report-bot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {


        $today = Carbon::now()->format('Y-m-d');

        // Get current Jalali date
        $jalaliToday = Jalalian::fromCarbon(Carbon::now());
        $jalaliYear = $jalaliToday->getYear();
        $jalaliMonth = $jalaliToday->getMonth();

        // First day of the current Jalali month
        $jalaliMonthStart = Jalalian::fromFormat('Y-m-d', sprintf('%d-%02d-01', $jalaliYear, $jalaliMonth));
        $first_day_of_mounth = $jalaliMonthStart->toCarbon()->startOfDay();

        // Last day of the current Jalali month - get first day of next month and subtract one day
        $nextMonth = $jalaliMonth + 1;
        $nextMonthYear = $jalaliYear;
        if ($nextMonth > 12) {
            $nextMonth = 1;
            $nextMonthYear++;
        }
        $jalaliNextMonthStart = Jalalian::fromFormat('Y-m-d', sprintf('%d-%02d-01', $nextMonthYear, $nextMonth));
        $end_day_of_mounth = $jalaliNextMonthStart->toCarbon()->subDay()->endOfDay();


        // $today = "2025-05-26";
        //gte projects that have telegram group
        foreach(Project::whereHas('telegram_groups')->get() as $project){

            $today_assigned_customers = Project_Customer::where('project_id',$project->id)->whereHas('users',function ($query)use($today){
                $query->whereDate('start_at',$today);
            })->count();

            $today_reports_count = Project_Customer::where('project_id',$project->id)->whereHas('reports',function ($query)use($today){
                $query->whereDate('created_at',$today);
            })->count();

            $today_invoice_amount = Project_Customer_Invoice::where('project_id',$project->id)->whereDate('created_at',$today)->sum('amount');
            $mounth_invoice_amount = Project_Customer_Invoice::where('project_id',$project->id)->whereDate('created_at','>=',$first_day_of_mounth)->whereDate('created_at','<=',$end_day_of_mounth)->sum('amount');

            $today_completed_amount = 0;

            foreach(Project_Customer_Invoice::where('project_id',$project->id)->whereDate('created_at',$today)->get() as $invoice){
                $total_invoice_amount = Project_Customer_Invoice::where('project_customer_id',$invoice->project_customer_id)->sum('amount');
                if($total_invoice_amount >= $invoice->project_customer->target_price){
                    $today_completed_amount++;
                }
            }

            $message = "#گزارش_روزانه - ".Jalalian::fromCarbon(Carbon::make($today))->format('Y-m-d');
            $message .= "\n\n";
            $message .= " تعداد کل شماره‌های ارجاع شده : ".$today_assigned_customers;
            $message .= "\n\n";
            $message .= "تعداد کل تماس‌های امروز : ".$today_reports_count;
            $message .= "\n\n";
            $message .= "💰💰💰💰💰💰";
            $message .= "\n\n";
            $message .= "  مجموع  فیش‌های امروز: ".number_format($today_invoice_amount);
            $message .= "\n\n";
            $message .= "مجموع  تکمیل وجه‌های امروز: ".$today_completed_amount;
            $message .= "\n\n";
            $message .= "💰💰💰💰💰💰";
            $message .= "\n\n";
            $message .= "  فروش این ماه : ".number_format($mounth_invoice_amount);
            $message .= "💰💰💰💰💰💰";
            $message .= "\n\n";
            //get users info
            $exists_users = [];
            foreach($project->users as $user){

                if($user->user && !in_array($user->user_id, $exists_users) && $user->user->is_active){
                $total_assigned_customers = Project_Customer::where('project_id',$project->id)->whereHas('users',function ($query)use($user,$today){
                    $query->where('user_id',$user->user_id)->whereDate('start_at',$today);
                })->count();
                $total_invoice_count = Project_Customer_Invoice::where('project_id',$project->id)->where('user_id',$user->user_id)->whereDate('created_at',$today)->count();
                $total_invoice_amount = Project_Customer_Invoice::where('project_id',$project->id)->where('user_id',$user->user_id)->whereDate('created_at',$today)->sum('amount');

                // Convert current date to Jalali and get the month range
                $todayCarbon = Carbon::parse($today);
                $jalaliToday = Jalalian::fromCarbon($todayCarbon);
                $jalaliYear = $jalaliToday->getYear();
                $jalaliMonth = $jalaliToday->getMonth();

                // Get start and end of Jalali month
                // First day of the month
                $jalaliMonthStart = Jalalian::fromFormat('Y-m-d', sprintf('%d-%02d-01', $jalaliYear, $jalaliMonth));
                // Last day of the month - get first day of next month and subtract one day
                $nextMonth = $jalaliMonth + 1;
                $nextMonthYear = $jalaliYear;
                if ($nextMonth > 12) {
                    $nextMonth = 1;
                    $nextMonthYear++;
                }
                $jalaliNextMonthStart = Jalalian::fromFormat('Y-m-d', sprintf('%d-%02d-01', $nextMonthYear, $nextMonth));
                $jalaliMonthEnd = Jalalian::fromCarbon($jalaliNextMonthStart->toCarbon()->subDay());

                // Convert Jalali dates to Carbon for database query
                $monthStartCarbon = $jalaliMonthStart->toCarbon()->startOfDay();
                $monthEndCarbon = $jalaliMonthEnd->toCarbon()->endOfDay();

                $total_month_invoice_amount = Project_Customer_Invoice::where('project_id',$project->id)->where('user_id',$user->user_id)->whereBetween('created_at', [$monthStartCarbon, $monthEndCarbon])->sum('amount');

                $message .= "👤 ".$user?->user?->name."\n\n";
                // $message .= "📱 تعداد شماره‌های ارجاع شده : ".$total_assigned_customers."\n\n";
                $message .= "🛒 تعداد فروش امروز : ".$total_invoice_count."\n\n";
                $message .= "💰 مجموع فروش امروز : ".number_format($total_invoice_amount)."\n\n";
                $message .= "📊 مجموع فروش این ماه : ".number_format($total_month_invoice_amount)."\n\n";
                $message .= "✨✨✨✨✨✨✨✨✨✨✨✨\n\n";
                $exists_users[] = $user->user_id;
                }
            }
            foreach($project->telegram_groups as $group){
                if($group->whereHas('topics')){
                    foreach($group->topics as $topic){
                        if($topic->selected){
                            helper_bot_send_markdown($group->telegram_id, $topic->topic_id, $message);
                        }
                    }
                }else{
                    helper_bot_send_markdown($group->telegram_id,null, $message);
                }
            }
        }



    }
}
