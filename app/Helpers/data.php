<?php

function helper_data_customer_status_success(){
    $status = \App\Models\Project_Customer_Status::where('name','LIKE','%'.'موفق'.'%')->first();
    return $status->id ?? 5;
}

function helper_data_customer_status_follow(){
    $status = \App\Models\Project_Customer_Status::where('name','LIKE','%'.'پیگیری'.'%')->first();
    return $status->id ?? 2;
}

function helper_data_customer_status_failed(){
    $status = \App\Models\Project_Customer_Status::where('name','LIKE','%'.'ناموفق'.'%')->first();
    return $status->id ?? 6;
}

function helper_data_project_level_consultant()
{
   return  \App\Models\Project_Level::where('name','LIKE','%'.'مشاوره'.'%')->first()->id ?? 1;
}

function helper_data_project_level_checkup()
{
    return  \App\Models\Project_Level::where('name','LIKE','%'.'معاینه'.'%')->first()->id ?? 2;
}

function helper_data_position_consultant(){
    return \App\Models\Position::where('name','LIKE','%'.'مشاور'.'%')->first()->id ?? 1;

}

function helper_data_position_seller(){
    return \App\Models\Position::where('name','LIKE','%'.'فروش'.'%')->first()->id ?? 2;

}

