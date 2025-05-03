<?php
/*
 * All Core functions is here ...
 */

function helper_core_code_generator($unique = 1, $count = 10): string
{
    $length = $count - strlen($unique) ;
    $start =1;
    $end = 9;
    for($i=1;$i<$length;$i++){
        $start.=0;
        $end.=9;
    }
    return $unique.random_int($start,$end);
}

function helper_core_get_user_customer_access($customer): array
{

    return $customer->projects()->whereHas('user',function($user){
        $user->where('user_id',auth()->user()->id);
    })->pluck('id')->toArray();

}





