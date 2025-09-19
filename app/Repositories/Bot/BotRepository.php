<?php

namespace App\Repositories\Bot;

use App\Interfaces\Bot\BotInterface;
use App\Models\Telegram_Group;
use App\Models\Project;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class BotRepository implements BotInterface
{
    // Authentication methods
    public function send_auth_code($request)
    {
        try {
            // Implementation for sending authentication code
            $phone = $request->input('phone');
            $code = rand(1000, 9999);

            // Store code in session or cache
            session(['bot_auth_code' => $code, 'bot_phone' => $phone]);

            // Send SMS or Telegram message with code
            // This would integrate with SMS service or Telegram Bot API

            return response()->json([
                'success' => true,
                'message' => 'Authentication code sent successfully',
                'code' => $code // Remove this in production
            ]);
        } catch (\Exception $e) {
            Log::error('Bot auth send error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send authentication code'
            ], 500);
        }
    }

    public function verify_auth_code($request)
    {
        try {
            $phone = $request->input('phone');
            $code = $request->input('code');

            if (session('bot_auth_code') == $code && session('bot_phone') == $phone) {
                // Clear the code from session
                session()->forget(['bot_auth_code', 'bot_phone']);

                return response()->json([
                    'success' => true,
                    'message' => 'Authentication successful'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Invalid authentication code'
            ], 401);
        } catch (\Exception $e) {
            Log::error('Bot auth verify error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify authentication code'
            ], 500);
        }
    }

    // Group management methods
    public function join_group($request)
    {

            $data  = $request->group_metadata;
            if($data){
                $telegram_id = $data['group_id'];
                $name = $data['title'];
                $member_count = $data['member_count'];
                $type = $data['type'];
                $description = $data['description'];

                $telegramGroup = Telegram_Group::updateOrCreate(
                    ['telegram_id' => $telegram_id],
                    [
                        'name' => $name,
                        'member_count' => $member_count,
                        'type' => $type,
                        'description' => $description,
                        'is_active' => true
                    ]
                );

                // Handle topics - delete existing topics and create new ones
                if (isset($data['available_topics'])) {
                    $topics = $data['available_topics'];
                    // Delete existing topics for this group
                    $telegramGroup->topics()->delete();

                    if (count($topics)) {
                        foreach ($topics as $topic) {
                            $telegramGroup->topics()->create([
                                'topic_id' => $topic['topic_id'],
                                'name' => $topic['name'],
                            ]);
                        }
                    }
                }


                return helper_response_created(result: $telegramGroup);
            }
            return helper_response_error('Group metadata not found');
    }

    public function leave_group($request)
    {
        try {
            $groupId = $request->input('group_id');

            $telegramGroup = Telegram_Group::where('telegram_id', $groupId)->first();

            if ($telegramGroup) {
                $telegramGroup->update(['is_active' => false]);

                return response()->json([
                    'success' => true,
                    'message' => 'Group left successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Group not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Bot leave group error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to leave group'
            ], 500);
        }
    }

    public function get_groups($request)
    {
        try {
            $groups = Telegram_Group::with('project')
                ->where('is_active', true)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $groups
            ]);
        } catch (\Exception $e) {
            Log::error('Bot get groups error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve groups'
            ], 500);
        }
    }

    public function get_group_by_id($group_id)
    {
        try {
            $group = Telegram_Group::with('project')
                ->where('telegram_id', $group_id)
                ->where('is_active', true)
                ->first();

            if ($group) {
                return response()->json([
                    'success' => true,
                    'data' => $group
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Group not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Bot get group by id error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve group'
            ], 500);
        }
    }

    // Project-related bot methods
    public function get_projects($request)
    {
        try {
            $projects = Project::where('is_active', true)->get();

            return response()->json([
                'success' => true,
                'data' => $projects
            ]);
        } catch (\Exception $e) {
            Log::error('Bot get projects error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve projects'
            ], 500);
        }
    }

    public function get_project_by_id($project_id)
    {
        try {
            $project = Project::with(['telegram_groups'])
                ->where('id', $project_id)
                ->where('is_active', true)
                ->first();

            if ($project) {
                return response()->json([
                    'success' => true,
                    'data' => $project
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Project not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Bot get project by id error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve project'
            ], 500);
        }
    }

    public function join_project_group($request)
    {
        try {
            $projectId = $request->input('project_id');
            $groupId = $request->input('group_id');

            // Check if project exists and is active
            $project = Project::where('id', $projectId)->where('is_active', true)->first();

            if (!$project) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project not found or inactive'
                ], 404);
            }

            // Create or update telegram group
            $telegramGroup = Telegram_Group::updateOrCreate(
                ['telegram_id' => $groupId],
                [
                    'project_id' => $projectId,
                    'is_active' => true
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Successfully joined project group',
                'data' => $telegramGroup
            ]);
        } catch (\Exception $e) {
            Log::error('Bot join project group error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to join project group'
            ], 500);
        }
    }

    // User bot methods
    public function get_user_profile($request)
    {
        try {
            $userId = $request->input('user_id');
            $user = User::find($userId);

            if ($user) {
                return response()->json([
                    'success' => true,
                    'data' => $user
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Bot get user profile error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user profile'
            ], 500);
        }
    }

    public function update_user_profile($request)
    {
        try {
            $userId = $request->input('user_id');
            $user = User::find($userId);

            if ($user) {
                $user->update($request->only(['name', 'phone', 'email']));

                return response()->json([
                    'success' => true,
                    'message' => 'Profile updated successfully',
                    'data' => $user
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Bot update user profile error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user profile'
            ], 500);
        }
    }

    // Bot configuration methods
    public function get_bot_settings()
    {
        try {
            // This would typically come from a configuration table or env variables
            $settings = [
                'bot_token' => config('bot.telegram_token'),
                'webhook_url' => config('bot.webhook_url'),
                'is_active' => config('bot.is_active', true)
            ];

            return response()->json([
                'success' => true,
                'data' => $settings
            ]);
        } catch (\Exception $e) {
            Log::error('Bot get settings error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve bot settings'
            ], 500);
        }
    }

    public function update_bot_settings($request)
    {
        try {
            // This would typically update a configuration table
            // For now, we'll just return success

            return response()->json([
                'success' => true,
                'message' => 'Bot settings updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Bot update settings error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update bot settings'
            ], 500);
        }
    }

    // Message handling methods
    public function send_message($request)
    {
        try {
            $chatId = $request->input('chat_id');
            $message = $request->input('message');
            $botToken = config('bot.telegram_token');

            $response = Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML'
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Message sent successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to send message'
            ], 400);
        } catch (\Exception $e) {
            Log::error('Bot send message error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message'
            ], 500);
        }
    }

    public function get_messages($request)
    {
        try {
            // This would typically retrieve messages from a messages table
            // For now, we'll return a placeholder response

            return response()->json([
                'success' => true,
                'data' => [],
                'message' => 'No messages found'
            ]);
        } catch (\Exception $e) {
            Log::error('Bot get messages error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve messages'
            ], 500);
        }
    }

    // Webhook methods
    public function handle_webhook($request)
    {
        try {
            $update = $request->all();

            // Process the webhook update
            $this->process_telegram_update($update);

            return response()->json([
                'success' => true,
                'message' => 'Webhook processed successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Bot webhook error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to process webhook'
            ], 500);
        }
    }

    public function process_telegram_update($update)
    {
        try {
            // Log the update for debugging
            Log::info('Telegram update received: ' . json_encode($update));

            // Process different types of updates
            if (isset($update['message'])) {
                $this->handle_message($update['message']);
            } elseif (isset($update['callback_query'])) {
                $this->handle_callback_query($update['callback_query']);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Bot process update error: ' . $e->getMessage());
            return false;
        }
    }

    private function handle_message($message)
    {
        // Handle incoming messages
        Log::info('Processing message: ' . json_encode($message));
    }

    private function handle_callback_query($callbackQuery)
    {
        // Handle callback queries from inline keyboards
        Log::info('Processing callback query: ' . json_encode($callbackQuery));
    }
}
