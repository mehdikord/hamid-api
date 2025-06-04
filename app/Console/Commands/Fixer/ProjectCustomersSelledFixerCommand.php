<?php

namespace App\Console\Commands\Fixer;

use App\Models\Customer;
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
        foreach (Project_Customer::WhereHas('users',function ($query){$query->whereNotNull('target_price');})->get() as $customer) {
            //get target
            $target = $customer->users()->sum('target_price');
            $invoices_prices = $customer->invoices()->sum('amount');
            if ($invoices_prices >= $target) {
                $customer->update(['selled' => true]);
            }else{
                $customer->update(['selled' => false]);
            }

        }
    }
}
