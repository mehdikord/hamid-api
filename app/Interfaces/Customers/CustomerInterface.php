<?php

namespace App\Interfaces\Customers;

interface CustomerInterface
{
    public function index();

    public function all();

    public function store($request);

    public function show($item);

    public function update($request,$item);

    public function destroy($item);

    public function projects_fields($item,$project);
    public function projects_reports($item,$project);

    public function projects_invoices($item,$project);


}
