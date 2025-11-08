<?php
/*
Whatsapp Service:
This service is used to send and receive whatsapp messages from and to customers
*/

namespace App\Services;

use App\Models\Project;
use App\Models\Whatsapp\WhatsappLog;
use App\Models\Whatsapp\WhatsappNumber;
use App\Models\Whatsapp\WhatsappQueue;
use Illuminate\Support\Facades\Http;

class WhatsappService
{
    private $api_key;

    public function __construct()
    {
        $this->api_key = env('WHATSAPP_API');
    }

    public function send_message($message,$phone,$customer_id=null,$project_id=null)
    {

        $admin_id = null;
        if(auth('admins')->check()){
            $admin_id = auth('admins')->id();
        }else{
            $project = Project::find($project_id);
            if($project){
                $admin_id = $project->member_id;
            }
        }
        $number = $this->active_number(admin_id: $admin_id);
        $data =[];
        $data['message'] = $message;
        $data['number'] = $phone;
        if($number){
            $data['sender'] = $number->name;
            $result = $this->send_request($data);
            $number->update([
                'last_used' => now(),
                'use_count' => $number->use_count + 1,
            ]);
            if($result['status']){
                $this->log_create($number->id,$message,$phone,true,$customer_id,$project_id,$result['message'],$admin_id);
                return 'success';
            }else{
                $this->log_create($number->id,$message,$phone,false,$customer_id,$project_id,$result['message'],$admin_id);
                return 'error';
            }
        }
        $this->add_to_queue($message,$phone,$customer_id, $project_id,$admin_id);
        return 'queued';
    }


    public function check_queue()
    {
        return WhatsappQueue::count();
    }

    public function select_number($admin_id = null){
        //frist check null numbers
        $null_use_count = WhatsappNumber::where('admin_id',$admin_id ?? auth('admins')->id())->where('is_active',true)->where('is_block',false)->whereNull('last_used')->first();
        if($null_use_count){
            return $null_use_count;
        }
        //get by last used
        $last_used = WhatsappNumber::where('admin_id',$admin_id ?? auth('admins')->id())->where('is_active',true)->where('is_block',false)->orderBy('last_used','desc')->first();
        if($last_used){
            return $last_used;
        }
        return null;

    }

    public function active_number($admin_id = null)
    {
        $null_use_count = WhatsappNumber::where('admin_id',$admin_id ?? auth('admins')->id())->where('is_active',true)->where('is_block',false)->whereNull('last_used')->first();
        if($null_use_count){
            return $null_use_count;
        }
        $last_used = WhatsappNumber::where('admin_id',$admin_id ?? auth('admins')->id())->where('is_active',true)->where('is_block',false)->where('last_used', '<', now()->subMinutes(5))->orderBy('last_used','desc')->first();
        if($last_used){
            return $last_used;
        }
        return null;
    }

    public function add_to_queue($message,$phone,$customer_id=null,$project_id=null,$admin_id = null)
    {
        WhatsappQueue::create([
            'admin_id' => $admin_id ?? auth('admins')->id(),
            'message' => $message,
            'phone' => $phone,
            'customer_id' => $customer_id,
            'project_id' => $project_id,
        ]);

    }

    public function log_create($number_id,$message,$phone, $status, $customer_id=null,$project_id=null,$response=null,$admin_id = null)
    {
        WhatsappLog::create(attributes: [
            'whatsapp_number_id' => $number_id,
            'admin_id' => $admin_id ?? auth('admins')->id(),
            'message' => $message,
            'customer_id' => $customer_id,
            'project_id' => $project_id,
            'phone' => $phone,
            'is_success' => $status,
            'response' => $response,
        ]);

    }

    public function send_request($data){

        if($this->api_key){
            $data['api_key'] = $this->api_key;
            $url = 'https://web.officebaz.ir/api/send-text.php';
            $response = Http::post($url, $data);
            $result = $response->json();
            if(isset($result['status']) && $result['status'] == 'success'){
                return ['status' => true, 'message' => 'Message sent successfully'];
            }
            return ['status' => false, 'message' => $result['message']];
        }
        return ['status' => false, 'message' => 'API key is not set'];
    }


}
