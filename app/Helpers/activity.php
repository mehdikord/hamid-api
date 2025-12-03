<?php
// all activities helper functions

use App\Models\Activity;
use Jenssegers\Agent\Agent;
function helper_activity_create($admin_id = null,$user_id = null,$project_id = null,$customer_id = null,$title = null,$activity = null){
    if(!$admin_id){
        $admin_id = auth('admins')->id();
    }
    if(!$user_id){
        $user_id = auth('users')->id();
    }

    $agent = new Agent();
    $device = [
        'browser' => $agent->browser(),
        'platform' => $agent->platform(),
        'device' => $agent->device(),
    ];
    $device = json_encode($device);
    Activity::create([
        'admin_id' => $admin_id,
        'user_id' => $user_id,
        'project_id' => $project_id,
        'customer_id' => $customer_id,
        'title' => $title,
        'activity' => $activity,
        'device' => $device,
        'ip' => request()->ip(),
    ]);
}

