<?php

namespace App\Interfaces\Projects;

interface ProjectStatusInterface
{
    public function index();

    public function all();

    public function store($request);

    public function show($item);

    public function update($request,$item);

    public function destroy($item);


}
