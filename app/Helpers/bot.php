<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

function helper_bot_send_markdown($id,$message)
{
    $data = [
        "chat_id"=> $id,
        "message"=> "$message",
        "parse_mode"=> "Markdown"
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
        "price_deal"=> 111,
        "price_deposit"=> $invoice->amount,
        "date"=> Carbon::make($invoice->created_at)->format('Y-m-d'),
        "customer_name"=> $invoice->project_customer->customer->name,
        "customer_phone"=> $invoice->project_customer->customer->phone,
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

    }

    return true;
}




