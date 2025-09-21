<?php

namespace App\Console\Commands;

use App\Models\Project_Customer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CustomerAssignedFixCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:customer-assigned-fix {--chunk=100 : Number of records to process at once}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix customer assignment status by checking if users are assigned to pending customers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $chunkSize = (int) $this->option('chunk');

        // Get total count for progress bar
        $totalCount = Project_Customer::where('status', Project_Customer::STATUS_PENDING)->count();

        if ($totalCount === 0) {
            $this->info('No pending customers found.');
            return;
        }

        $this->info("Processing {$totalCount} pending customers in chunks of {$chunkSize}...");

        $progressBar = $this->output->createProgressBar($totalCount);
        $progressBar->start();

        $assignedIds = [];
        $pendingIds = [];
        $processed = 0;

        // Process records in chunks to avoid memory issues
        Project_Customer::where('status', Project_Customer::STATUS_PENDING)
            ->chunk($chunkSize, function ($customers) use (&$assignedIds, &$pendingIds, &$processed, $progressBar) {

                // Preload users relationship to avoid N+1 queries
                $customers->load('users');

                foreach ($customers as $customer) {
                    if ($customer->users->count() > 0) {
                        $assignedIds[] = $customer->id;
                    } else {
                        $pendingIds[] = $customer->id;
                    }

                    $processed++;
                    $progressBar->advance();
                }

                // Clear memory after each chunk
                unset($customers);
            });

        $progressBar->finish();
        $this->newLine();

        // Perform bulk updates
        $this->performBulkUpdates($assignedIds, $pendingIds);

        $this->info("Processed {$processed} customers successfully!");
        $this->info("Updated " . count($assignedIds) . " customers to assigned status");
        $this->info("Kept " . count($pendingIds) . " customers as pending");
    }

    /**
     * Perform bulk updates for better performance
     */
    private function performBulkUpdates(array $assignedIds, array $pendingIds): void
    {
        $this->info('Performing bulk updates...');

        // Update assigned customers
        if (!empty($assignedIds)) {
            Project_Customer::whereIn('id', $assignedIds)
                ->update(['status' => Project_Customer::STATUS_ASSIGNED]);
        }

        // Update pending customers (though they're already pending, this ensures consistency)
        if (!empty($pendingIds)) {
            Project_Customer::whereIn('id', $pendingIds)
                ->update(['status' => Project_Customer::STATUS_PENDING]);
        }
    }
}
