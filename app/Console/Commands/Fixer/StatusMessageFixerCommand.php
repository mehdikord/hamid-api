<?php

namespace App\Console\Commands\Fixer;

use App\Models\User;
use App\Models\User_Project_Customer_Status;
use Illuminate\Console\Command;

class StatusMessageFixerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:status-message {status_id : Status identifier} {option_id : Option identifier} {count : Number of items to process}';

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
        $statusId = (int) $this->argument('status_id');
        $optionId = (int) $this->argument('option_id');
        $count = (int) $this->argument('count');

        //get statuses
        $data = User_Project_Customer_Status::where('customer_status_id',$statusId)->whereDoesntHave('message_options')->take($count)->get();
        if(count($data) < 1){
            $this->info('No data found');
            return Command::FAILURE;
        }

        foreach ($data as $item) {
            $item->message_options()->create([
                'message_option_id' => $optionId,
            ]);
        }
        return Command::SUCCESS;
    }
}
