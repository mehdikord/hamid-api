<?php
/*
Whatsapp Service:
This service is used to send and receive whatsapp messages from and to customers
*/

namespace App\Services;

use App\Models\Project;
use App\Models\Projects\Project_Message;
use App\Models\Whatsapp\WhatsappLog;
use App\Models\Whatsapp\WhatsappNumber;
use App\Models\Whatsapp\WhatsappQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    private $api_key;
    public $base_url;

    public function __construct()
    {
        $this->api_key = env('WHATSAPP_API');
        $this->base_url = "https://web.officebaz.ir/wh/";
    }

    public function send_message($message,$message_id = null, $link = null,$phone,$customer_id=null,$project_id=null)
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
        $data['recipient'] = $phone;
        if($message_id){
            $buttons = [];
            if($link){
                $buttons[] = [
                    'name' => 'ثبت درخواست',
                    'buttonParamsJson' => json_encode(['display_text' => 'ثبت درخواست' , 'url' => $link,'merchant_url' => $link]),
                ];
            }
            $get_message = Project_Message::find($message_id);
            if($get_message){
                if($get_message->buttons){
                    foreach(json_decode($get_message->buttons, true) as $button){
                        $buttons[] = [
                            'name' => $button['title'],
                            'buttonParamsJson' => '{\"display_text\": \"Visit Website\", \"url\": \"https://example.com\", \"merchant_url\": \"https://example.com\"}',
                        ];
                    }
                }
                $data['message_text'] = '.';
                $data['cards'] = [
                    [
                        'title' => $get_message->title,
                        'body' => $message,
                        'image' => $get_message->file ? ['url' => env('APP_URL').$get_message->file] : null,
                        'buttons' => $buttons,
                    ]
                ];

            }
        }
        if($number){
            $data['device_name'] = $number->name;
            $result = $this->send_request( $data);
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
        $this->add_to_queue($message,$message_id,$link,$phone,$customer_id, $project_id,$admin_id);
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
        $last_used = WhatsappNumber::where('admin_id',$admin_id ?? auth('admins')->id())->where('is_active',true)->where('is_block',false)->where('last_used', '<', now()->subMinutes(2))->orderBy('last_used','desc')->first();
        if($last_used){
            return $last_used;
        }
        return null;
    }

    public function add_to_queue($message,$message_id = null,$link = null,$phone,$customer_id=null,$project_id=null,$admin_id = null)
    {
        WhatsappQueue::create([
            'admin_id' => $admin_id ?? auth('admins')->id(),
            'message' => $message,
            'phone' => $phone,
            'customer_id' => $customer_id,
            'project_id' => $project_id,
            'project_message_id' => $message_id,
            'link' => $link,
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
        try {
            if($this->api_key){
                $data['api_key'] = $this->api_key;
                $url = $this->base_url.'send_cards.php';
                $response = Http::post($url, $data);

                // Check if request was successful
                if($response->failed()){
                    return ['status' => false, 'message' => 'HTTP request failed with status: ' . $response->status()];
                }

                $result = $response->json();

                // Check if JSON parsing was successful
                if($result === null){
                    return ['status' => false, 'message' => 'Invalid response from API'];
                }

                if(isset($result['success']) && $result['success'] == 'true'){
                    Log::info('Message sent successfully with device: '.$data['device_name']);
                    return ['status' => true, 'message' => 'Message sent successfully with device: '.$data['device_name']];
                }

                return ['status' => false, 'message' => $result['message'] ?? 'Unknown error occurred'];
            }
            return ['status' => false, 'message' => 'API key is not set'];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return ['status' => false, 'message' => 'Connection error: ' . $e->getMessage()];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => 'An error occurred: ' . $e->getMessage()];
        }
    }


}
