<?php

namespace App\Console\Commands\Cron;

use App\Models\Admin;
use App\Models\Whatsapp\WhatsappNumber;
use App\Models\Whatsapp\WhatsappQueue;
use App\Services\WhatsappService;
use Illuminate\Console\Command;

class WhatsappQueueCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:queue-whatsapp';

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
        $service = new WhatsappService();
        foreach(Admin::get() as $admin){
            $active_numbers = WhatsappNumber::where('is_active',true)->where('admin_id',$admin->id)->count();
            if($active_numbers > 0){
                $numbers = WhatsappQueue::where('admin_id',$admin->id)->orderByDesc('id')->limit($active_numbers)->get();

                foreach($numbers as $number){
                    $phone = $number->phone;
                    if($phone){
                        $service->send_message($number->message, $number->project_message_id, $number->link, $phone,$number->customer_id,$number->project_id);
                    }
                    $number->delete();
                }
            }
        }







        //get items from whatsapp_queues table count of active numbers

    }
}
