<?php

namespace App\Repositories\Telegram;

use App\Http\Resources\Telegram\TelegramGroupIndexResource;
use App\Interfaces\Telegram\TelegramInterface;
use App\Models\Telegram_Group;



class TelegramRepository implements TelegramInterface
{
    public function all()
    {
        $data = Telegram_Group::with('project')->get();
        return helper_response_fetch(TelegramGroupIndexResource::collection($data)->resource);
    }
    public function assign($request,$group)
    {

        if($request->filled('project_id')){
            $group->update(['project_id' => $request->project_id]);
            if($request->filled('topic_id')){
                foreach($group->topics as $topic){
                    $topic->update(['selected' => false]);
                   if($request->topic_id == $topic->topic_id){
                        $topic->update(['selected' => true]);
                   }
                }
            }
        }
        return helper_response_fetch(new TelegramGroupIndexResource($group));
    }
}
