<?php

namespace App\Http\Controllers\Users\Reminders;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserReminders\UserReminderStoreRequest;
use App\Http\Requests\UserReminders\UserReminderUpdateRequest;
use App\Interfaces\Users\UserReminderInterface;
use App\Models\User_Reminder;

class ReminderController extends Controller
{
    protected UserReminderInterface $repository;

    public function __construct(UserReminderInterface $reminder)
    {
        $this->middleware('generate_fetch_query_params')->only('index');
        $this->repository = $reminder;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->repository->index();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserReminderStoreRequest $request)
    {
        return $this->repository->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(User_Reminder $reminder)
    {
        // Ensure the reminder belongs to the authenticated user
        if ($reminder->user_id !== auth('users')->id()) {
            return helper_response_access_denied();
        }
        return $this->repository->show($reminder);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserReminderUpdateRequest $request, User_Reminder $reminder)
    {
        // Ensure the reminder belongs to the authenticated user
        if ($reminder->user_id !== auth('users')->id()) {
            return helper_response_access_denied();
        }
        return $this->repository->update($request, $reminder);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User_Reminder $reminder)
    {
        // Ensure the reminder belongs to the authenticated user
        if ($reminder->user_id !== auth('users')->id()) {
            return helper_response_access_denied();
        }
        return $this->repository->destroy($reminder);
    }
}

