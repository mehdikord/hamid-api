<?php

namespace App\Repositories\Whatsapp;

use App\Http\Resources\Whatsapp\WhatsappNumber\WhatsappLogIndexResource;
use App\Http\Resources\Whatsapp\WhatsappNumber\WhatsappNumberIndexResource;
use App\Http\Resources\Whatsapp\WhatsappNumber\WhatsappNumberSingleResource;
use App\Http\Resources\Whatsapp\WhatsappNumber\WhatsappQueueIndexResource;
use App\Interfaces\Whatsapp\WhatsappInterface;
use App\Models\Customer;
use App\Models\Project_Customer;
use App\Models\Whatsapp\WhatsappLog;
use App\Models\Whatsapp\WhatsappNumber;
use App\Models\Whatsapp\WhatsappQueue;
use App\Services\WhatsappService;
use Illuminate\Support\Str;

class WhatsappRepository implements WhatsappInterface
{
    protected WhatsappService $service;

    public function __construct(WhatsappService $whatsapp)
    {
        $this->service = $whatsapp;
    }

    public function index()
    {
        $data = WhatsappNumber::query();
        $data->orderBy(request('sort_by'), request('sort_type'));
        return helper_response_fetch(WhatsappNumberIndexResource::collection($data->paginate(request('per_page')))->resource);
    }

    public function all()
    {
        $data = WhatsappNumber::query();
        $data->orderByDesc('id');
        return helper_response_fetch(WhatsappNumberIndexResource::collection($data->get()));
    }

    public function store($request)
    {
        $data = WhatsappNumber::create([
            'admin_id' => auth('admins')->id(),
            'number' => $request->number,
            'name' => $request->name,
            'use_count' => 0,
            'is_active' => true,
            'is_block' => false,
            'last_used' => null,
        ]);
        return helper_response_fetch(new WhatsappNumberIndexResource($data));
    }

    public function show($item)
    {
        return helper_response_fetch(new WhatsappNumberSingleResource($item));
    }

    public function update($request, $item)
    {
        $item->update([
            'number' => $request->number,
            'name' => $request->name,
        ]);
        return helper_response_updated(new WhatsappNumberSingleResource($item));
    }

    public function destroy($item)
    {
        $item->delete();
        return helper_response_deleted();
    }

    public function send_message($request)
    {
        if($request->filled('customer_id')){
            $customer = Customer::find($request->customer_id);
            if(!$customer){
                return helper_response_error('Customer not found');
            }
            $phone = $customer->phone;
            if (!empty($phone) && $phone[0] == '0') {
                $phone = substr($phone, 1);
            }
            $message = $request->message;

            if($request->link == '1'){
                $project_customer = Project_Customer::where('customer_id',$customer->id)->where('project_id',$request->project_id)->first();
                if(!$project_customer->link){
                   $project_customer->update([
                    'link' => $project_customer->id.Str::random(3),
                   ]);
                }
                $link = "https://i.tonl.ir/a/".$project_customer->link;
                $message .= "\n\n".'لینک: '.$link;
            }

            if($phone){
                $result = $this->service->send_message($message,$phone,$customer->id,$request->project_id);
                if($result == 'success'){
                    return helper_response_created('Message sent successfully');
                }elseif($result == 'error'){
                    return helper_response_error('Message not sent');
                }elseif($result == 'queued'){
                    return helper_response_created('Message queued');
                }
            }
            return  helper_response_error(' customer Phone not found');
        }
        return helper_response_error('Customer or project not found');


    }
    public function send_message_multi($request)
    {

        $customers = Customer::whereIn('id',$request->customer_id)->get();
        foreach($customers as $customer){
            $phone = $customer->phone;
            if (!empty($phone) && $phone[0] == '0') {
                $phone = substr($phone, 1);
            }
            $message = $request->message;
            if($request->link == '1'){
                $project_customer = Project_Customer::where('customer_id',$customer->id)->where('project_id',$request->project_id)->first();
                if(!$project_customer->link){
                   $project_customer->update([
                    'link' => $project_customer->id.Str::random(3),
                   ]);
                }
                $link = "https://i.tonl.ir/a/".$project_customer->link;
                $message .= "\n\n".'لینک: '.$link;
            }
            if($phone){
                $this->service->send_message($message,$phone,$customer->id,$request->project_id);
            }
        }
        return helper_response_created('Successfully sent messages to customers');
    }

    public function queue()
    {
        $data = WhatsappQueue::query();
        $data->where('admin_id', auth('admins')->id());
        $data->orderBy(request('sort_by'), request('sort_type'));
        return helper_response_fetch(WhatsappQueueIndexResource::collection($data->paginate(request('per_page')))->resource);

    }
    public function logs()
    {
        $data = WhatsappLog::query();
        $data->where('admin_id', auth('admins')->id());
        $data->orderBy(request('sort_by'), request('sort_type'));
        return helper_response_fetch(WhatsappLogIndexResource::collection($data->paginate(request('per_page')))->resource);
    }

}

