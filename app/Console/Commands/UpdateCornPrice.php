<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\SchwabController;

class UpdateCornPrice extends Command
{
    protected $signature = 'corn:update';
    protected $description = 'Update corn futures price from Schwab API';

    public function handle()
    {
        $controller = app(SchwabController::class);
        $controller->updateCornPrice(); // We’ll add this method
        $this->info('Corn price updated successfully!');
    }
}
