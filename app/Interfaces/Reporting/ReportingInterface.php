<?php

namespace App\Interfaces\Reporting;

interface ReportingInterface
{
    public function admin_users_weekly($request,$project);

    public function projects_summery();


}
