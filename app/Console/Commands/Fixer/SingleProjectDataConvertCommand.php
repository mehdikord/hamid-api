<?php

namespace App\Console\Commands\Fixer;

use App\Models\Fields\Field;
use App\Models\Fields\Field_Option;
use App\Models\Import_Method;
use App\Models\Project;
use App\Models\Project_Customer_Status;
use App\Models\Project_Level;
use Illuminate\Console\Command;

class SingleProjectDataConvertCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:project-data-fixer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert and duplicate project data (fields, import methods, statuses, levels) for all projects';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $projects = Project::all();
        $totalProjects = $projects->count();

        if ($totalProjects === 0) {
            $this->info('No projects found to process.');
            return;
        }

        $this->info("Starting data conversion for {$totalProjects} projects...");

        // Initialize progress bar
        $progressBar = $this->output->createProgressBar($totalProjects);
        $progressBar->setFormat('verbose');
        $progressBar->start();

        $processedCount = 0;

        foreach($projects as $project){
            $this->line("\nProcessing Project: (ID: {$project->id})");

            //get fields
            $this->line("  → Processing fields...");
            foreach(Field::whereNull('project_id')->get() as $field){
                $new_field = Field::create([
                    'project_id' => $project->id,
                    'title' => $field->title,
                    'type' => $field->type,
                    'placeholder' => $field->placeholder,
                    'description' => $field->description,
                ]);
                if($field->options){
                    foreach($field->options as $option){
                        Field_Option::create([
                            'field_id' => $new_field->id,
                            'option' => $option->option,
                        ]);
                    }
                }
            }

            //import methods
            $this->line("  → Processing import methods...");
            foreach(Import_Method::whereNull('project_id')->get() as $method){
                $new_method = Import_Method::create(attributes: [
                    'project_id' => $project->id,
                    'name' => $method->name,
                    'description' => $method->description,
                ]);
            }

            //project_customer_statuses
            $this->line("  → Processing customer statuses...");
            foreach(Project_Customer_Status::whereNull('project_id')->get() as $status){
                $new_status = Project_Customer_Status::create(attributes: [
                    'project_id' => $project->id,
                    'name' => $status->name,
                    'color' => $status->color,
                    'description' => $status->description,
                ]);
            }

            //project_levels
            $this->line("  → Processing project levels...");
            foreach(Project_Level::whereNull('project_id')->get() as $level){
                $new_level = Project_Level::create(attributes: [
                    'project_id' => $project->id,
                    'name' => $level->name,
                    'color' => $level->color,
                    'description' => $level->description,
                ]);
            }

            $processedCount++;
            $progressBar->advance();

            $this->line("  ✓ Project {$project->id} completed ({$processedCount}/{$totalProjects})");
        }

        $progressBar->finish();
        $this->line("\n");
        $this->info("✅ Data conversion completed successfully! Processed {$totalProjects} projects.");
    }
}
