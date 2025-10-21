<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Interfaces\Reporting\ReportingInterface;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectReportController extends Controller
{
    protected ReportingInterface $repository;
    public function __construct(ReportingInterface $reporting)
    {
        $this->repository = $reporting;
    }

    public function summery(Project $project)
    {
        return $this->repository->summery($project);
    }
}
