<?php

namespace App\Repositories\Whatsapp;

use App\Http\Resources\Whatsapp\WhatsappNumber\WhatsappNumberIndexResource;
use App\Http\Resources\Whatsapp\WhatsappNumber\WhatsappNumberSingleResource;
use App\Interfaces\Whatsapp\WhatsappInterface;
use App\Models\Whatsapp\WhatsappNumber;

class WhatsappRepository implements WhatsappInterface
{
    public function index()
    {
        $data = WhatsappNumber::query();
        $data->orderBy(request('sort_by'), request('sort_type'));
        return helper_response_fetch(WhatsappNumberIndexResource::collection($data->paginate(request('per_page')))->resource);
    }

    public function all()
    {
        $data = WhatsappNumber::query();
        $data->orderByDesc('id');
        return helper_response_fetch(WhatsappNumberIndexResource::collection($data->get()));
    }

    public function store($request)
    {
        $data = WhatsappNumber::create([
            'admin_id' => auth('admins')->id(),
            'number' => $request->number,
            'use_count' => 0,
            'is_active' => true,
            'is_block' => false,
            'last_used' => null,
        ]);
        return helper_response_fetch(new WhatsappNumberIndexResource($data));
    }

    public function show($item)
    {
        return helper_response_fetch(new WhatsappNumberSingleResource($item));
    }

    public function update($request, $item)
    {
        $item->update([
            'number' => $request->number,
        ]);
        return helper_response_updated(new WhatsappNumberSingleResource($item));
    }

    public function destroy($item)
    {
        $item->delete();
        return helper_response_deleted();
    }
}

