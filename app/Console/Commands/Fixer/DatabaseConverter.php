<?php

namespace App\Console\Commands\Fixer;

use App\Models\Project;
use App\Models\Project_Customer;
use App\Models\Project_Customer_Invoice;
use Illuminate\Console\Command;

class DatabaseConverter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:database-converter';

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
        $this->info('🔄 Starting database conversion...');
        $projects_without_product = Project::whereDoesntHave('products')->get();
        foreach ($projects_without_product as $project) {
            $product = $project->products()->create([
                'name' => 'محصول پیش فرض',
                'description' => 'محصول پیش فرض',
                'price' => 1000000,
                'type' => 'service'
            ]);

            $invoices = Project_Customer_Invoice::where('project_id',$project->id)->get();
            foreach($invoices as $invoice){

                $new_invoice = $project->invoices()->create([
                    'user_id' => $invoice->user_id,
                    'project_customer_id' => $invoice->project_customer_id,
                    'amount' => $invoice->amount,
                    'target_price' => $invoice->project_customer->target_price,
                    'description' => $invoice->description,
                    'created_at' => $invoice->created_at,
                ]);
                $paid = 0;
                if($invoice->amount >= $invoice->project_customer->target_price){
                    $paid = 1;
                }
                $new_invoice->orders()->create([
                    'product_id' => $product->id,
                    'project_id' => $project->id,
                    'quantity' => 1,
                    'amount' => $invoice->amount,
                    'file_path' => $invoice->file_path,
                    'file_url' => $invoice->file_url,
                    'created_at' => $invoice->created_at,
                ]);
                $new_invoice->update(['paid' => $paid]);

                $invoice->delete();
            }

        }


        $projects_with_product = Project::whereHas('products')->get();
        foreach ($projects_with_product as $project) {

            $invoices = Project_Customer_Invoice::where('project_id',$project->id)->get();
            foreach ($invoices as $invoice) {

                $new_invoice = $project->invoices()->create([
                    'user_id' => $invoice->user_id,
                    'project_customer_id' => $invoice->project_customer_id,
                    'amount' => $invoice->amount,
                    'target_price' => $invoice->project_customer->target_price,
                    'description' => $invoice->description,
                    'created_at' => $invoice->created_at,
                ]);
                $paid = 0;
                if($invoice->amount >= $invoice->project_customer->target_price){
                    $paid = 1;
                }
                $new_invoice->update(['paid' => $paid]);

                if($project->id == 18){

                    $product_id = null;
                    if($invoice->amount <= 7000000){
                        $product_id = 3;
                    }else{
                        $product_id = 4;
                    }
                    $new_invoice->orders()->create([
                        'product_id' => $product_id,
                        'project_id' => $project->id,
                        'quantity' => 1,
                        'amount' => $invoice->amount,
                        'file_path' => $invoice->file_path,
                        'file_url' => $invoice->file_url,
                        'created_at' => $invoice->created_at,
                    ]);
                }else{
                    foreach ($invoice->invoice_products as $product) {
                        $new_invoice->orders()->create([
                            'product_id' => $product->project_product_id,
                            'project_id' => $project->id,
                            'quantity' => 1,
                            'amount' => $invoice->amount,
                            'file_path' => $invoice->file_path,
                            'file_url' => $invoice->file_url,
                            'created_at' => $invoice->created_at,
                        ]);
                    }
                }


                $invoice->delete();
            }
        }

        $this->info('🔄 Database conversion completed successfully');

    }
}
