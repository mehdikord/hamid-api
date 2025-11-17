<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\Project_Customer;
use App\Models\Projects\Project_Message;
use App\Models\Tag;
use Illuminate\Http\Request;

class ActionController extends Controller
{
    public function activation($token)
    {

        if($token){
            $customer = Project_Customer::where('link',$token)->first();
            if($customer){
                //find tag
                $tag = Tag::where('project_id',$customer->project_id)->where('name','like','%پیگیری%')->first();
                if($tag){
                    $customer->tags()->attach($tag->id);
                }
                return helper_response_success('customer activated successfully');
            }
            return helper_response_error('customer not found');
        }
        return helper_response_error('token not found')

    }
}
