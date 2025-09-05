<?php

namespace App\Interfaces\Projects;

interface ProjectMessageInterface
{
    public function index($project);

    public function all($project);

    public function store($project,$request);

    public function show($project,$item);

    public function update($project,$request,$item);

    public function destroy($project,$item);

}
