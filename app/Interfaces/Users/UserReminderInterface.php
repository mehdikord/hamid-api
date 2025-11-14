<?php

namespace App\Interfaces\Users;

interface UserReminderInterface
{
    public function index();

    public function store($request);

    public function show($reminder);

    public function update($request, $reminder);

    public function destroy($reminder);
}

