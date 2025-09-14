<?php

namespace App\Http\Controllers\Bot\Groups;

use App\Http\Controllers\Controller;
use App\Interfaces\Bot\BotInterface;
use Illuminate\Http\Request;

class GroupsController extends Controller
{
    protected BotInterface $repository;
    public function __construct(BotInterface $bot)
    {
        $this->repository = $bot;
    }
    public function join(Request $request)
    {
        return $this->repository->join_group($request);
    }
}
