<?php

namespace App\Console\Commands\Fixer;

use App\Models\Project;
use Illuminate\Console\Command;

class ProjectCustomersComparisonCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:project-customers-comparison {from_project_id : The ID of the from project} {to_project_id : The ID of the to project}';

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
        $deleted_customers = 0;
        $exist_customers = 0;
        $invoices_customers = 0;
        $projectFrom = Project::find($this->argument('from_project_id'));
        foreach ($projectFrom->customers as $customer) {
            $get_customer = $customer->customer->projects()->where('project_id', $this->argument('to_project_id'))->first();
            if ($get_customer) {
                if($customer->invoices()->count() > 0){
                    $invoices_customers++;
                    $this->info('Invoices customer: ' . $customer->customer->phone);
                }else{
                    $customer->delete();
                    $deleted_customers++;
                }
            } else {
                $exist_customers++;
            }
        }
        $this->info('Deleted customers: ' . $deleted_customers);
        $this->info('Exist customers: ' . $exist_customers);
        $this->info('Invoices customers: ' . $invoices_customers);
    }
}
