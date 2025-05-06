<?php
namespace App\Interfaces\Users;
use App\Models\Customer;

interface UserCustomerInterface
{
    public function users_index($user);

    public function show($customer);

    public function update($customer,$request);

    public function statuses_store($customer,$request);

    //Reports
    public function reports_store($customer,$request);


    public function all_reports_latest($customer);

    public function all_invoice_latest($customer);

    //invoices
    public function invoices_store($customer,$request);
    public function invoices_target_store($customer,$request);

    public function dashboard($customer);

    public function projects($customer);


}
