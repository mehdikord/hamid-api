<?php

namespace App\Console\Commands\Install;

use App\Models\Position;
use Illuminate\Console\Command;

class PositionsInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install-positions';

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
        foreach (config('installation.positions') as $position) {
            Position::updateOrCreate(['slug' => $position['slug']],['name' => $position['name']]);
        }
    }
}
