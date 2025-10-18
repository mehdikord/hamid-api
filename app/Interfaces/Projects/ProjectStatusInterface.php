<?php

namespace App\Interfaces\Projects;

interface ProjectStatusInterface
{
    public function index($project);

    public function all($project);

    public function store($request,$project);

    public function show($item,$project);

    public function update($request,$item);

    public function destroy($item,$project);

    public function get_messages($project,$status);

    public function store_messages($project,$status,$request);

}
