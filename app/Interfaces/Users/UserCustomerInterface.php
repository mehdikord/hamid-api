<?php
namespace App\Interfaces\Users;
interface UserCustomerInterface
{
    public function users_index($user);

    public function show($customer);

    public function update($customer,$request);

    public function statuses_store($customer,$request);

    //Reports
    public function reports_store($customer,$request);


    //invoices
    public function invoices_store($customer,$request);
    public function invoices_target_store($customer,$request);


}
