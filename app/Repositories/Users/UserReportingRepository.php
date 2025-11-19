<?php

namespace App\Repositories\Users;

use App\Interfaces\Users\UserReportingInterface;
use Illuminate\Support\Carbon;

class UserReportingRepository implements UserReportingInterface
{
    public function invoices()
    {
        if(!request()->filled('from_date') || !request()->filled('to_date')){
            return helper_response_error('From date and to date are required');
        }
        $result = [];
        $from_date = Carbon::parse(request()->from_date);
        $to_date = Carbon::parse(request()->to_date);
        $is_same_date = $from_date->isSameDay($to_date);
        if($is_same_date){
            if(request()->filled('project_id')){
                $amount = auth('users')->user()->invoices()->whereDate('created_at',$from_date)->where('project_id',request()->project_id)->sum('amount');
            } else {
                $amount = auth('users')->user()->invoices()->whereDate('created_at',$from_date)->sum('amount');
            }
            $result[] = [
                'date' => $from_date->format('Y/m/d'),
                'amount' => $amount,
            ];
        } else {
            $current_date = $from_date->copy();
            while ($current_date->lte($to_date)) {
                if(request()->filled('project_id')){
                    $amount = auth('users')->user()->invoices()->whereDate('created_at', $current_date)->where('project_id',request()->project_id)->sum('amount');
                } else {
                    $amount = auth('users')->user()->invoices()->whereDate('created_at', $current_date)->sum('amount');
                }
                $result[] = [
                    'date' => $current_date->format('Y/m/d'),
                    'amount' => $amount,
                ];
                $current_date->addDay();
            }
        }
        return helper_response_fetch($result);

    }
}

