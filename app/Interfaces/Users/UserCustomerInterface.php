<?php
namespace App\Interfaces\Users;
use App\Models\Customer;

interface UserCustomerInterface
{

    public function users_index($user);

    public function users_customers($user);
    public function users_consultants($user);

    public function users_seller($user);

    public function users_consultants_old($user);

    public function show($customer);

    public function update($customer,$request);

    public function statuses_store($customer,$request);

    //Reports
    public function reports_store($customer,$request);

    public function reports_delete($item);


    public function all_reports_latest($customer);
    public function reports_index($customer);

    public function all_invoice_latest($customer);

    
    //invoices
    public function invoices_store($customer,$request);

    public function invoices_target_store($customer,$request);

    public function invoices_index($customer);

    public function dashboard($customer);

    public function projects($customer);

    public function projects_own($customer,$project);

    public function projects_report_store($customer,$project,$request);

    public function projects_invoice_store($customer,$project,$request);

    public function projects_fields($customer,$project);

    public function projects_levels($customer,$project);


}
