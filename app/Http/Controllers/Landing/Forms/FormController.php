<?php

namespace App\Http\Controllers\Landing\Forms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Projects\Forms\ProjectFormsLandingCreateRequest;
use App\Http\Resources\Projects\Forms\ProjectFromLandingResource;
use App\Models\Customer;
use App\Models\Project_Form;
use Illuminate\Http\Request;

class FormController extends Controller
{

    public function get_form($token)
    {
        $form = Project_Form::where('token', $token)->where('is_active',true)->firstorfail();
        return helper_response_fetch(new ProjectFromLandingResource($form));
    }

    public function store_form($token,ProjectFormsLandingCreateRequest  $request)
    {
        $form = Project_Form::where('token', $token)->where('is_active',true)->first();
        if ($form){
            //Find Customer
            $customer = Customer::where('phone',$request->phone)->first();
            if ($customer){
                //check customer in project
                $project = $form->project;


            }
            return helper_response_error('شماره شما ثبت نشده است !');
        }
        return helper_response_error('فرم مورد نظر نامعتبر است !');

    }
}
