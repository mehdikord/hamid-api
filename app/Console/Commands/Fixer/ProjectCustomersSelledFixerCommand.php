<?php

namespace App\Console\Commands\Fixer;

use App\Models\Customer;
use App\Models\Project;
use App\Models\Project_Customer;
use Illuminate\Console\Command;

class ProjectCustomersSelledFixerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:customers-selled-fixer';

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
        // Collect all customers from all projects
        $customers = collect();
        foreach (Project::all() as $project) {
            $customers = $customers->merge($project->customers);
        }

        $this->info('Processing ' . $customers->count() . ' customers...');

        // Process customers with progress bar
        $this->withProgressBar($customers, function ($customer) {
            if($customer->invoices()->sum('amount') >= $customer->target_price) {
                $customer->update(['selled' => true]);
            } else {
                $customer->update(['selled' => false]);
            }
        });

        $this->newLine();
        $this->info('Done! All customers processed successfully.');
    }
}
