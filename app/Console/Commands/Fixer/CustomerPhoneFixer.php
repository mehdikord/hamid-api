<?php

namespace App\Console\Commands\Fixer;

use App\Models\Customer;
use Illuminate\Console\Command;

class CustomerPhoneFixer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:customer-phone-fixer';

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
        foreach (Customer::all() as $item) {
            if (mb_substr($item->phone, 0, 1, 'UTF-8') != '0'){
                $item->update(['phone'=> '0'.$item->phone ]);
            }
        }
        $this->info('Done');
    }
}
