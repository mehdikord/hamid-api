<?php

namespace App\Interfaces\Fields;

interface FieldInterface
{
    public function index($project);

    public function all($project);

    public function store($request,$project);

    public function show($item,$project);

    public function update($request,$item,$project);

    public function destroy($item,$project);


}
