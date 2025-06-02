<?php

namespace App\Console\Commands\Fixer;

use App\Models\Position;
use App\Models\Project_Customer;
use App\Models\User_Project_Customer;
use Illuminate\Console\Command;

class UsersPositionsFixerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fixer-users-positions';

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
        $consultant = Position::where('slug','consultant')->first();
        $seller = Position::where('slug','seller')->first();
        foreach (User_Project_Customer::all() as $item){
            if ($item->project_customer->project_level_id == 1){
                $item->update(['position_id' => $consultant->id]);
            }else{
                $item->update(['position_id' => $seller->id]);
                //get first report
                $report = $item->project_customer->reports()->first();
                if ($report){
                    User_Project_Customer::create([
                        'project_customer_id' => $item->project_customer->id,
                        'user_id' => $report->user_id,
                        'position_id' => $consultant->id,
                        'start_at' => $report->created_at,
                        'is_active' => true
                    ]);
                }
            }

        }
    }
}
