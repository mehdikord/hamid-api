<?php

namespace App\Http\Controllers\Users\Reporting;

use App\Http\Controllers\Controller;
use App\Interfaces\Users\UserReportingInterface;
use Illuminate\Http\Request;

class ReportingController extends Controller
{
    protected UserReportingInterface $repository;

    public function __construct(UserReportingInterface $repository)
    {
        $this->repository = $repository;
    }

    public function invoices()
    {
        return $this->repository->invoices();
    }

    
}
