<?php

namespace App\Interfaces\Projects;

interface ProjectLevelInterface
{
    public function index($project);

    public function all($project);

    public function store($request,$project);

    public function show($item,$project);

    public function update($request,$item);

    public function destroy($item,$project);


}
