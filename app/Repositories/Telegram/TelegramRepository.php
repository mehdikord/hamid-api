<?php

namespace App\Repositories\Telegram;

use App\Http\Resources\Telegram\TelegramGroupIndexResource;
use App\Interfaces\Telegram\TelegramInterface;
use App\Models\Telegram_Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramRepository implements TelegramInterface
{
    public function all()
    {
        $data = Telegram_Group::with('project')->get();
        return helper_response_fetch(TelegramGroupIndexResource::collection($data)->resource);
    }
}
