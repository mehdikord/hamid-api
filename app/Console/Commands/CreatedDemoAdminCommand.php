<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;

class CreatedDemoAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:demo-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Admin::query()->updateOrCreate(['email' => 'admin@crm.com'],['name' => 'Administrator','password' => \Hash::make(123456)]);
        $this->info('Demo admin account created');
    }
}
