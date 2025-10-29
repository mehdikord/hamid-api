<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Morilog\Jalali\Jalalian;

function helper_bot_send_markdown($id,$topic_id=null,$message)
{
    $data = [
        "chat_id"=> $id,
        "topic_id"=> $topic_id ? $topic_id : null,
        "message"=> "$message",
        "parse_mode"=> "HTML"
    ];
    $url = env('BOT_ADDRESS')."/webhook/notify";
    return helper_core_send_post_request($url,$data);
}

function helper_bot_send_buttons($id,$message)
{

}

function helper_bot_send_raw($id,$message)
{

}

function helper_bot_send_group_invoice($invoice)
{

    $invoice_data = [
        'image'=> $invoice->file_url ? env('APP_URL').$invoice->file_url : null,
        "price_deal"=> $invoice->project_customer->target_price,
        "price_deposit"=> $invoice->amount,
        "date"=> Jalalian::fromCarbon(Carbon::make($invoice->created_at))->format('Y-m-d'),
        "customer_name"=> $invoice->project_customer->customer->name,
        "customer_phone"=> $invoice->project_customer->customer->phone,
        'customer_province'=> $invoice->project_customer?->customer?->province?->name,
        'customer_city'=> $invoice->project_customer?->customer?->city?->name,
        'customer_id' => $invoice->project_customer?->customer?->telegram_id,
        "assignee"=> $invoice->user->name,
    ];
    if($invoice->project)
    {
        $project = $invoice->project;
        foreach($project->telegram_groups as $group){
            if($group->telegram_id){
                $invoice_data['group_id'] = $group->telegram_id;
                if($group->topics->where('selected',true)->count() > 0){
                    $invoice_data['topic_id'] = $group->topics->where('selected',true)->first()->topic_id;
                }
                $url = env('BOT_ADDRESS')."/api/receipts";
                helper_core_send_post_request($url,$invoice_data);
            }
        }

        Log::info('invoice_data',$invoice_data);
    }

    return true;
}




