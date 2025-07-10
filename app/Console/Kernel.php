<?php
namespace App\Console;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Application;
use Illuminate\Events\Dispatcher;

class Kernel extends ConsoleKernel
{
    public function __construct(Application $app, Dispatcher $events)
    {
        parent::__construct($app, $events);
    }

    protected function schedule(Schedule $schedule)
    {
        Log::info('Loading scheduler tasks at ' . now());
        $schedule->command('monitor:temperature')->everyMinute()->appendOutputTo(storage_path('logs/schedule.log'));
        $schedule->command('monthly:save')->monthlyOn(1, '00:00')->appendOutputTo(storage_path('logs/schedule.log'));
        $schedule->command('corn:update')->hourly()->appendOutputTo(storage_path('logs/schedule.log'));
        $schedule->command('inspire')->hourly()->appendOutputTo(storage_path('logs/schedule.log')); // Add inspire
    }

    protected function commands()
    {
        Log::info('Loading console commands at ' . now());
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php'); // Restore this
    }
}
