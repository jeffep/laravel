<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class MonthlySave extends Command
{
    protected $signature = 'monthly:save';
    protected $description = 'Save and rename files at the end of each month';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $currentMonth = Carbon::now()->month;
        $previousMonth = $currentMonth == 1 ? 12 : $currentMonth - 1;
        $currentYear = Carbon::now()->year;

        $files = [
            'garagetemperatures',
            'backporchtemperatures',
            'workroomtemperatures',
            'dentemperatures'
        ];

        foreach ($files as $file) {
            $oldPath = "/var/www/html/{$file}.json";
            $newPath = "/var/www/html/{$file}-{$previousMonth}{$currentYear}.json";
            if (file_exists($oldPath)) {
                rename($oldPath, $newPath);
            }
            touch($oldPath);
        }

        $this->info('Files have been saved and new files created.');
    }
}

