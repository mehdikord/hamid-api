<?php

namespace App\Interfaces\Tags;

interface TagInterface
{
    public function index();

    public function all();

    public function store($request);

    public function show($item);

    public function update($request,$item);

    public function destroy($item);


}
