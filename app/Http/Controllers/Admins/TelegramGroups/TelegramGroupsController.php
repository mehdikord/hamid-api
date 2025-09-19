<?php

namespace App\Http\Controllers\Admins\TelegramGroups;

use App\Http\Controllers\Controller;
use App\Interfaces\Telegram\TelegramInterface;
use App\Models\Telegram_Group;
use Illuminate\Http\Request;

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
    public function assign(Request $request, Telegram_Group $group)
    {
        return $this->repository->assign($request, $group);
    }
}
