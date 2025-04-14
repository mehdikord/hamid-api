<?php

namespace App\Interfaces\Projects;

interface ProjectInterface
{
    public function index();

    public function store($request);

    public function show($item);

    public function update($request,$item);

    public function destroy($item);

    public function change_activation($item);

    public function add_customers($request,$item);

    public function get_customers($item);

    public function assigned_customers($item,$request);
    public function assigned_customers_single($item,$request);

    public function get_latest_reports($item);

    public function get_latest_invoices($item);

}
