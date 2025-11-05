<?php

namespace App\Interfaces\Whatsapp;

interface WhatsappInterface
{
    public function index();

    public function all();

    public function store($request);

    public function show($item);

    public function update($request, $item);

    public function destroy($item);
}

