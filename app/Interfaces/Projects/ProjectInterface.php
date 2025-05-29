<?php

namespace App\Interfaces\Projects;

interface ProjectInterface
{
    public function index();
    public function all();

    public function store($request);

    public function show($item);

    public function update($request,$item);

    public function destroy($item);

    public function all_customers($item);

    public function change_activation($item);

    public function add_customers($request,$item);

    public function get_customers($item);

    public function customers_change_status($request,$item);
    public function customers_change_level($request,$item);

    public function delete_customers($project,$item);

    public function assigned_customers($item,$request);
    public function assigned_customers_single($item,$request);

    public function reports($item);

    public function reports_update($project,$report,$request);

    public function reports_destroy($project,$report);
    public function invoices_destroy($project,$invoice);

    public function invoices($item);
    public function get_latest_reports($item);

    public function get_latest_invoices($item);

    public function get_fields($item);

    public function store_fields($item,$request);

    public function get_positions($item);

    public function store_positions($item,$request);
    public function get_levels($item);

    public function store_levels($item,$request);

    public function update_levels($project,$item,$request);

    public function delete_levels($project,$item);


}
