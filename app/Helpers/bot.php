<?php

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

