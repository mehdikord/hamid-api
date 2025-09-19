<?php

namespace App\Interfaces\Telegram;

interface TelegramInterface
{
    public function all();
    public function assign($request,$group);

}
