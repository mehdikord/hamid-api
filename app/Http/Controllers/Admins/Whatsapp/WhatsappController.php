<?php

namespace App\Http\Controllers\Admins\Whatsapp;

use App\Http\Controllers\Controller;
use App\Http\Requests\Whatsapp\WhatsappNumber\WhatsappSendMultiRequest;
use App\Http\Requests\Whatsapp\WhatsappNumber\WhatsappSendRequest;
use App\Interfaces\Whatsapp\WhatsappInterface;
use Illuminate\Http\Request;

class WhatsappController extends Controller
{
    protected WhatsappInterface $repository;

    public function __construct(WhatsappInterface $whatsapp)
    {
        $this->repository = $whatsapp;
    }

    public function send_message(WhatsappSendRequest $request)
    {
        return $this->repository->send_message($request);
    }
    public function send_message_multi(WhatsappSendMultiRequest $request)
    {
        return $this->repository->send_message_multi($request);
    }
}
