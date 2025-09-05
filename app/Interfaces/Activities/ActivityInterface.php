<?php

namespace App\Interfaces\Activities;

interface ActivityInterface
{
    public function index();

    public function show($item);
    
    public function destroy($item);
}
