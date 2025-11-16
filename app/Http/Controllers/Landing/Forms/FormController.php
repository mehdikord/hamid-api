<?php

namespace App\Http\Controllers\Landing\Forms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Projects\Forms\ProjectFormsLandingCreateRequest;
use App\Http\Resources\Projects\Forms\ProjectFromLandingResource;
use App\Models\Customer;
use App\Models\Project_Form;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FormController extends Controller
{

    public function get_form($token)
    {
        $form = Project_Form::where('token', $token)->where('is_active',true)->with('project')->firstorfail();
        $form->update(['view' => $form->view+1]);
        return helper_response_fetch(new ProjectFromLandingResource($form));
    }

    public function store_form($token,ProjectFormsLandingCreateRequest  $request)
    {
        //check phone
        $phone = $request->phone;
        if (mb_substr($phone, 0, 1, 'UTF-8') != '0'){
            $phone = '0'.$phone;
        }
        $form = Project_Form::where('token', $token)->where('is_active',true)->first();
        if ($form){
            $project = $form->project;
            //Find Customer
            $customer = Customer::where('phone','LIKE','%'. $phone .'%')->first();
            if ($customer){
                $customer->update(['name' =>  $request->name,'description' => $request->description]);
                //check customer in project
                $project_customer = $project->customers()->where('customer_id',$customer->id)->first();
                if ($project_customer){
                    if ($request->filled('fields')){
                        $project_customer->fields()->delete();
                        foreach ($request->fields as $field){
                            $project_customer->fields()->create([
                                'field_id' => $field['field_id'],
                                'val' => $field['val']
                            ]);
                        }
                    }
                    if($form->tag_id){
                        $project_customer->tags()->sync($form->tag_id);
                    }
                }else{
                    $project_customer = $project->customers()->create([
                       'customer_id' => $customer->id,
                        'import_at' => Carbon::now(),
                        'from_form' => true,
                        'import_method_id' => $form->import_method_id,

                    ]);
                    if ($request->filled('fields')){
                        $project_customer->fields()->delete();
                        foreach ($request->fields as $field){
                            $project_customer->fields()->create([
                                'field_id' => $field['field_id'],
                                'val' => $field['val']
                            ]);
                        }
                    }
                    if($form->tag_id){
                        $project_customer->tags()->attach($form->tag_id);
                    }
                }
            }else{
                $customer = Customer::create([
                    'phone' => $phone,
                    'name' => $request->name,
                    'description' => $request->description,
                ]);
                $project_customer = $project->customers()->create([
                    'customer_id' => $customer->id,
                    'import_at' => Carbon::now(),
                    'from_form' => true,
                    'import_method_id' => $form->import_method_id,
                ]);
                if($form->tag_id){
                    $project_customer->tags()->sync($form->tag_id);
                }
                if ($request->filled('fields')){
                    $project_customer->fields()->delete();
                    foreach ($request->fields as $field){
                        $project_customer->fields()->create([
                            'field_id' => $field['field_id'],
                            'val' => $field['val']
                        ]);
                    }
                }
            }
            $form->update(['register' => $form->view+1]);

            return helper_response_main('اطلاعات شما با موفقیت ثبت گردید ، باتشکر از شما','','',200);
        }
        return helper_response_error('فرم مورد نظر نامعتبر است !');

    }
}
