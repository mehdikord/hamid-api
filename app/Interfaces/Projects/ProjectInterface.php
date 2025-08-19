<?php

namespace App\Interfaces\Projects;

interface ProjectInterface
{
    public function index();
    public function all();
    public function pending_customers($project);

    public function pending_customers_success($project);

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
    public function customers_change_target($request,$item);

    public function delete_customers($project,$item);

    public function delete_multi($project,$request);

    public function assigned_customers($item,$request);
    public function assigned_customers_single($item,$request);

    public function assigned_customers_multi($item,$request);

    public function reports($item);
    public function reports_store($item,$request);

    public function reports_update($project,$report,$request);

    public function reports_destroy($project,$report);

    public function invoices_update($project,$invoice,$request);

    public function invoices_settle($project,$invoice);

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

    //Forms
    public function get_forms($project);

    public function store_forms($project,$request);

    public function update_forms($project,$item,$request);

    public function destroy_forms($project,$item);

    public function activation_forms($project,$item);

    //Exports
    public function export_customers($project);



}
