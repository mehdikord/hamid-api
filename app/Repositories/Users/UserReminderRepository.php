<?php

namespace App\Repositories\Users;

use App\Http\Resources\UserReminders\UserReminderIndexResource;
use App\Http\Resources\UserReminders\UserReminderSingleResource;
use App\Interfaces\Users\UserReminderInterface;
use App\Models\User_Reminder;

class UserReminderRepository implements UserReminderInterface
{
    public function index()
    {
        $data = auth('users')->user()->reminders();
        $data->orderBy(request('sort_by'), request('sort_type'));
        return helper_response_fetch(UserReminderIndexResource::collection($data->paginate(request('per_page')))->resource);
    }

    public function store($request)
    {
        $data = auth('users')->user()->reminders()->create([
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
            'time' => $request->time,
            'offset' => $request->offset,
            'status' => $request->status ?? 'pending',
        ]);
        return helper_response_created(new UserReminderSingleResource($data));
    }

    public function show($reminder)
    {
        return helper_response_fetch(new UserReminderSingleResource($reminder));
    }

    public function update($request, $reminder)
    {
        $reminder->update([
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
            'time' => $request->time,
            'offset' => $request->offset,
            'status' => $request->status,
        ]);
        return helper_response_updated(new UserReminderSingleResource($reminder));
    }

    public function destroy($reminder)
    {
        $reminder->delete();
        return helper_response_deleted();
    }
}

