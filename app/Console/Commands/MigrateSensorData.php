<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PDO;

class MigrateSensorData extends Command
{
    protected $signature = 'sensor:migrate-data {--dry-run}';
    protected $description = 'Migrate sensor data from old SQLite structure into new MySQL schema';

    public function handle()
    {
        $sqlitePath = base_path('old_database.sqlite'); // Adjust path if needed

        if (!file_exists($sqlitePath)) {
            $this->error('SQLite database not found.');
            return 1;
        }

        $sqlite = new PDO("sqlite:$sqlitePath");
        $mysql = DB::connection('mysql');

        $rows = $sqlite->query('SELECT location, time, temperature, humidity FROM sensor_data')->fetchAll(PDO::FETCH_ASSOC);

        $sensorCache = [];

        foreach ($rows as $row) {
            $location = $row['location'];
            $time = date('Y-m-d H:i:s', $row['time']);

            // Check cache or insert sensor
            if (!isset($sensorCache[$location])) {
                $existing = $mysql->table('sensors')->where('location', $location)->first();

                if ($existing) {
                    $sensorId = $existing->id;
                } elseif (!$this->option('dry-run')) {
                    $sensorId = $mysql->table('sensors')->insertGetId([
                        'name' => $location,
                        'type' => 'env',
                        'location' => $location,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $sensorId = 'DRY_RUN';
                }

                $sensorCache[$location] = $sensorId;
            } else {
                $sensorId = $sensorCache[$location];
            }

            // Insert sensor_data rows
            foreach (['temperature' => $row['temperature'], 'humidity' => $row['humidity']] as $title => $value) {
                if (!$this->option('dry-run')) {
                    $mysql->table('sensor_data')->insert([
                        'sensor_id' => $sensorId,
                        'time' => $time,
                        'title' => $title,
                        'value' => $value,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $this->line(($this->option('dry-run') ? '[Dry Run] ' : '') . "Inserted $title for '$location' at $time with value $value.");
            }
        }

        $this->info('Sensor migration complete!');
        return 0;
    }
}
