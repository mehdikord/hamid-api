<?php

namespace App\Interfaces\Members;

interface MemberInterface
{
    public function index();

    public function all();

    public function store($request);

    public function show($item);

    public function update($request,$item);
    public function change_password($request,$item);

    public function destroy($item);


}
