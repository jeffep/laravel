<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MigrateAllLegacyData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:legacy-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate all legacy data from sensor, user, and shelly sources';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->call('sensor:migrate-data');
        $this->call('user:migrate-data');
        $this->call('shelly:migrate-data');
    }
}
