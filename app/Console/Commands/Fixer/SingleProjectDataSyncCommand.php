<?php

namespace App\Console\Commands\Fixer;

use App\Models\Project;
use Illuminate\Console\Command;

class SingleProjectDataSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:project-data-sync {project_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync project customer data with project settings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $project = Project::find($this->argument('project_id'));
        if (!$project) {
            $this->error('Project not found');
            return;
        }

        $this->info("Starting data synchronization for project: {$project->name}");
        $this->newLine();

        // Sync Import Methods
        $this->info("Syncing data for Import Methods");
        $importCustomers = $project->customers()->whereNotNull('import_method_id')->get();
        $importProgressBar = $this->output->createProgressBar($importCustomers->count());
        $importProgressBar->start();

        foreach($importCustomers as $customer){
            try {
                $import_method_name = $customer->import_method->name;
                $find_method = $project->import_methods->where('name', $import_method_name)->first();
                if ($find_method) {
                    $customer->update(['import_method_id' => $find_method->id]);
                }
            } catch (\Exception $e) {
                $this->warn("Error syncing import method for customer ID {$customer->id}: " . $e->getMessage());
            }
            $importProgressBar->advance();
        }
        $importProgressBar->finish();
        $this->newLine();

        // Sync Statuses
        $this->info("Syncing data for Statuses");
        $statusCustomers = $project->customers()->whereNotNull('project_customer_status_id')->get();
        $statusProgressBar = $this->output->createProgressBar($statusCustomers->count());
        $statusProgressBar->start();

        foreach($statusCustomers as $customer){
            try {
                $status_name = $customer->project_status->name;
                $find_status = $project->statuses->where('name', $status_name)->first();
                if ($find_status) {
                    $customer->update(['project_customer_status_id' => $find_status->id]);
                }
            } catch (\Exception $e) {
                $this->warn("Error syncing status for customer ID {$customer->id}: " . $e->getMessage());
            }
            $statusProgressBar->advance();
        }
        $statusProgressBar->finish();
        $this->newLine();

        // Sync Levels
        $this->info("Syncing data for Levels");
        $levelCustomers = $project->customers()->whereNotNull('project_level_id')->get();
        $levelProgressBar = $this->output->createProgressBar($levelCustomers->count());
        $levelProgressBar->start();

        foreach($levelCustomers as $customer){
            try {
                $level_name = $customer->project_level->name;
                $find_level = $project->levels->where('name', $level_name)->first();
                if ($find_level) {
                    $customer->update(['project_level_id' => $find_level->id]);
                }
            } catch (\Exception $e) {
                $this->warn("Error syncing level for customer ID {$customer->id}: " . $e->getMessage());
            }
            $levelProgressBar->advance();
        }
        $levelProgressBar->finish();
        $this->newLine();

        // Sync Tags
        $this->info("Syncing data for Tags");
        $tagCustomers = $project->customers()->whereHas('tags')->get();
        $tagProgressBar = $this->output->createProgressBar($tagCustomers->count());
        $tagProgressBar->start();

        foreach($tagCustomers as $customer){
            try {
                $tags_id = [];
                foreach($customer->tags as $tag){
                    $find_tag = $project->tags->where('name', $tag->name)->first();
                    if ($find_tag) {
                        $tags_id[] = $find_tag->id;
                    }
                }
                if (!empty($tags_id)) {
                    $customer->tags()->sync($tags_id);
                }
            } catch (\Exception $e) {
                $this->warn("Error syncing tags for customer ID {$customer->id}: " . $e->getMessage());
            }
            $tagProgressBar->advance();
        }
        $tagProgressBar->finish();
        $this->newLine();

        $this->info("Data synchronization completed successfully!");
    }

}
