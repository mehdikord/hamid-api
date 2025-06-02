<?php

use App\Models\Project_Customer;

function helper_reporting_customers_selled_price($customer_ids=[]){

    $price=0;
    foreach (Project_Customer::whereIn('id',$customer_ids)->where('selled',true)->get() as $item) {
        $price+=$item->invoices()->sum('amount');
    }
    return $price;
}
function helper_reporting_customers_not_selled_price($customer_ids=[]){

    $price=0;
    foreach (Project_Customer::whereIn('id',$customer_ids)->where('selled',false)->get() as $item) {
        $price+=$item->invoices()->sum('amount');
    }
    return $price;
}
