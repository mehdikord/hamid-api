<?php

namespace App\Http\Controllers\Admins\TelegramGroups;

use App\Http\Controllers\Controller;
use App\Interfaces\Telegram\TelegramInterface;


class TelegramGroupsController extends Controller
{
    protected TelegramInterface $repository;
    public function __construct(TelegramInterface $telegram)
    {
        $this->repository = $telegram;
    }
    public function all()
    {
        return $this->repository->all();
    }
}
