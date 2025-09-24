<?php

namespace App\Console\Commands\Fixer;

use App\Models\Project_Customer;
use Illuminate\Console\Command;

class CustomerTargetPriceFixedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:customer-target-price-fix {--chunk=100 : Number of records to process at once}';

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
        $this->info('Starting to fix customer target prices...');

        $data = Project_Customer::whereHas('users', callback: function ($query) {
            $query->whereNotNull('target_price');
        })->get();

        $totalRecords = $data->count();

        if ($totalRecords === 0) {
            $this->info('No records found to process.');
            return;
        }

        $this->info("Found {$totalRecords} records to process.");

        // Create progress bar
        $progressBar = $this->output->createProgressBar($totalRecords);
        $progressBar->setFormat('verbose');
        $progressBar->start();

        $processed = 0;
        foreach ($data as $item) {
            $item->update(['target_price' => $item->users()->sum('target_price')]);
            $processed++;
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
        $this->info("Successfully processed {$processed} records.");
    }
}
