<?php

namespace App\Interfaces\Bot;

interface BotInterface
{
    // Authentication methods
    public function send_auth_code($request);
    public function verify_auth_code($request);

    // Group management methods
    public function join_group($request);
    public function leave_group($request);
    public function get_groups($request);
    public function get_group_by_id($group_id);

    // Project-related bot methods
    public function get_projects($request);
    public function get_project_by_id($project_id);
    public function join_project_group($request);

    // User bot methods
    public function get_user_profile($request);
    public function update_user_profile($request);

    // Bot configuration methods
    public function get_bot_settings();
    public function update_bot_settings($request);

    // Message handling methods
    public function send_message($request);
    public function get_messages($request);

    // Webhook methods
    public function handle_webhook($request);
    public function process_telegram_update($update);
}
