<?php

namespace App\Console\Commands\Fixer;

use App\Models\Project;
use App\Models\Project_Customer_Invoice;
use App\Models\Project_Customer_Report;
use App\Models\User_Project;
use Illuminate\Console\Command;

class ProjectUsersFixerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fixer-project-users';

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
        User_Project::whereNotNull('id')->delete();

        $result = [];
        foreach (Project_Customer_Report::all() as $item) {
            $result[$item->project_id][$item->user_id][]=$item->id;
        }
        foreach ($result as $project_id=>$users) {
            foreach ($users as $user_id=>$items) {
                User_Project::UpdateOrCreate(['project_id'=>$project_id,'user_id'=>$user_id],[
                    'total_reports' => count($items),
                ]);
            }
        }

        //invoices price
        $invoices=[] ;
        foreach (Project_Customer_Invoice::all() as $item) {
            $invoices[$item->project_id][$item->user_id][]=$item->amount;
        }
        foreach ($invoices as $project_id=>$users) {
            foreach ($users as $user_id=>$items) {
                User_Project::UpdateOrCreate(['project_id'=>$project_id,'user_id'=>$user_id],[
                    'total_price' => array_sum($items),
                ]);
            }
        }



    }
}
