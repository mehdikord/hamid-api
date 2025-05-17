<?php

namespace App\Http\Controllers\Admins\Dashboard;

use App\Http\Controllers\Controller;
use App\Interfaces\Reporting\ReportingInterface;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected ReportingInterface $repository;

    public function __construct(ReportingInterface $reporting)
    {
        $this->middleware('generate_fetch_query_params')->only('index');
        $this->repository = $reporting;
    }

    public function users_weekly(Request $request)
    {
        return $this->repository->admin_users_weekly($request);
    }

    public function projects_summery()
    {
        return $this->repository->projects_summery();
    }
}
