<?php

namespace App\Console\Commands\Helper;

use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class InvoiceDownloaderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:invoice-downloader {project_id : The ID of the project}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download all invoice files from a project and save them in a zip file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $projectId = $this->argument('project_id');

        // Find the project
        $project = Project::find($projectId);

        if (!$project) {
            $this->error("Project with ID {$projectId} not found.");
            return 1;
        }

        $projectName = $project->name ?? $project->id;
        $this->info("Project found: {$projectName}");

        // Get all invoices with files
        $invoices = $project->invoices()
            ->whereNotNull('file_path')
            ->where('file_path', '!=', '')
            ->get();

        if ($invoices->isEmpty()) {
            $this->warn("No invoices with files found for this project.");
            return 0;
        }

        $this->info("Found {$invoices->count()} invoice(s) with files.");

        // Create zip file path in storage
        $storagePath = 'public/data/invoices/' . $projectId . '/file.zip';
        $zipFilePath = storage_path('app/' . $storagePath);

        // Create directory if it doesn't exist
        $zipDir = dirname($zipFilePath);
        if (!is_dir($zipDir)) {
            mkdir($zipDir, 0755, true);
        }

        // Remove existing zip file if exists
        if (file_exists($zipFilePath)) {
            unlink($zipFilePath);
        }

        $zip = new ZipArchive();

        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            $this->error("Error creating zip file.");
            return 1;
        }

        $addedFiles = 0;
        $skippedFiles = 0;

        foreach ($invoices as $invoice) {
            $filePath = $invoice->file_path;

            // Check if file exists in storage
            if (!Storage::exists($filePath)) {
                $this->warn("Invoice #{$invoice->id} file not found: {$filePath}");
                $skippedFiles++;
                continue;
            }

            // Get file content
            $fileContent = Storage::get($filePath);

            // Use invoice ID and original filename for unique naming in zip
            $fileNameInZip = $invoice->id . '_' . ($invoice->file_name ?? 'invoice_' . $invoice->id);

            // Add file to zip
            if ($zip->addFromString($fileNameInZip, $fileContent)) {
                $addedFiles++;
                $this->line("Invoice #{$invoice->id} file added.");
            } else {
                $this->warn("Error adding invoice #{$invoice->id} file.");
                $skippedFiles++;
            }
        }

        $zip->close();

        if ($addedFiles > 0) {
            $this->info("Zip file created successfully.");
            $this->info("Added {$addedFiles} file(s) to zip.");

            if ($skippedFiles > 0) {
                $this->warn("Skipped {$skippedFiles} file(s).");
            }

            $this->info("Storage path: app/public/data/invoices/{$projectId}/file.zip");
        } else {
            $this->error("No files were added to zip.");
            // Clean up empty zip file
            if (file_exists($zipFilePath)) {
                unlink($zipFilePath);
            }
            return 1;
        }

        return 0;
    }
}
